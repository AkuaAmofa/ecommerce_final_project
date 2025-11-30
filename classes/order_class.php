<?php
// classes/order_class.php
require_once dirname(__DIR__) . '/settings/db_class.php';

class order_class extends db_connection {

    /**
     * Create order → returns new order_id or null
     * @param int|null $customer_id - Customer ID
     * @param string $invoice_no - Unique invoice number
     * @param string $order_date - Order date (YYYY-MM-DD) - optional, defaults to NOW()
     * @param string $status - Order status (default: 'Pending')
     * @return int|null - Returns order_id if successful, null if failed
     */
    public function create_order($customer_id, $invoice_no, $order_date = null, $status = 'Pending') {
        error_log("=== CREATE_ORDER METHOD CALLED ===");
        try {
            // Get connection first
            $conn = $this->db_conn();

            if (!$conn) {
                error_log("Failed to get database connection");
                return null;
            }

            $customer_id = $customer_id ? (int)$customer_id : null;
            $invoice_no = mysqli_real_escape_string($conn, $invoice_no);
            $status = mysqli_real_escape_string($conn, $status);

            // If order_date not provided, use NOW()
            if ($order_date) {
                $order_date = mysqli_real_escape_string($conn, $order_date);
                $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                        VALUES (" . ($customer_id ? $customer_id : "NULL") . ", '$invoice_no', '$order_date', '$status')";
            } else {
                $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                        VALUES (" . ($customer_id ? $customer_id : "NULL") . ", '$invoice_no', NOW(), '$status')";
            }

            error_log("Executing SQL: $sql");

            // Execute directly on the connection
            $result = mysqli_query($conn, $sql);

            if ($result) {
                // Get insert ID immediately from the same connection
                $order_id = mysqli_insert_id($conn);
                error_log("Order created successfully with ID: $order_id");

                if ($order_id > 0) {
                    return $order_id;
                } else {
                    error_log("Insert succeeded but ID is 0");
                    return null;
                }
            } else {
                $error = mysqli_error($conn);
                error_log("Order creation failed. MySQL error: " . $error);
                return null;
            }

        } catch (Exception $e) {
            error_log("Exception in create_order: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Add order details (products) to an order
     * @param int $order_id - Order ID
     * @param int $product_id - Product ID
     * @param int $qty - Quantity ordered
     * @param float $unit_price - Unit price (optional)
     * @return bool - Returns true if successful, false if failed
     */
    public function add_order_detail($order_id, $product_id, $qty, $unit_price = null) {
        try {
            $order_id = (int)$order_id;
            $product_id = (int)$product_id;
            $qty = (int)$qty;

            // Decrease ticket quantity for this product
            require_once dirname(__DIR__) . '/controllers/product_controller.php';
            $decrease_result = decrease_ticket_quantity_ctr($product_id, $qty);

            if (!$decrease_result) {
                error_log("Failed to decrease ticket quantity for product $product_id - not enough tickets or product not found");
                return false; // Don't create order detail if we can't decrease tickets
            }

            // If unit_price is provided, include it in the insert
            if ($unit_price !== null) {
                $unit_price = (float)$unit_price;
                $sql = "INSERT INTO orderdetails (order_id, product_id, qty, unit_price)
                        VALUES ($order_id, $product_id, $qty, $unit_price)";
            } else {
                // Original behavior - just order_id, product_id, qty
                $sql = "INSERT INTO orderdetails (order_id, product_id, qty)
                        VALUES ($order_id, $product_id, $qty)";
            }

            error_log("Adding order detail - Order: $order_id, Product: $product_id, Qty: $qty");

            return $this->db_write_query($sql);

        } catch (Exception $e) {
            error_log("Error adding order details: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Record a payment for an order
     * @param float $amount - Payment amount (or order_id if using old signature)
     * @param int $customer_id - Customer ID (or customer_id if using old signature)
     * @param int $order_id - Order ID (or amount if using old signature)
     * @param string $currency - Currency code (or currency if using old signature)
     * @param string $payment_date - Payment date (optional, defaults to NOW())
     * @param string $payment_method - Payment method (e.g., 'paystack', 'cash', 'bank_transfer')
     * @param string $transaction_ref - Transaction reference/ID from payment gateway
     * @param string $authorization_code - Authorization code from payment gateway
     * @param string $payment_channel - Payment channel (e.g., 'card', 'mobile_money')
     * @return int|bool - Returns payment_id if successful, false if failed
     */
    public function record_payment($amount, $customer_id, $order_id, $currency = 'GHS', $payment_date = null, $payment_method = 'direct', $transaction_ref = null, $authorization_code = null, $payment_channel = null) {
        error_log("=== RECORD_PAYMENT METHOD CALLED ===");
        try {
            $conn = $this->db_conn();
            if (!$conn) {
                error_log("Failed to get database connection");
                return false;
            }

            $amount = (float)$amount;
            $customer_id = $customer_id ? (int)$customer_id : null;
            $order_id = (int)$order_id;
            $currency = mysqli_real_escape_string($conn, $currency);
            $payment_method = mysqli_real_escape_string($conn, $payment_method);
            $transaction_ref = $transaction_ref ? mysqli_real_escape_string($conn, $transaction_ref) : null;
            $authorization_code = $authorization_code ? mysqli_real_escape_string($conn, $authorization_code) : null;
            $payment_channel = $payment_channel ? mysqli_real_escape_string($conn, $payment_channel) : null;

            // Build SQL with optional fields
            $columns = "(amt, order_id, currency, payment_date";
            $values = "($amount, $order_id, '$currency', " . ($payment_date ? "'" . mysqli_real_escape_string($conn, $payment_date) . "'" : "NOW()");

            if ($customer_id) {
                $columns .= ", customer_id";
                $values .= ", $customer_id";
            }

            $columns .= ", payment_method";
            $values .= ", '$payment_method'";

            if ($transaction_ref) {
                $columns .= ", transaction_ref";
                $values .= ", '$transaction_ref'";
            }
            if ($authorization_code) {
                $columns .= ", authorization_code";
                $values .= ", '$authorization_code'";
            }
            if ($payment_channel) {
                $columns .= ", payment_channel";
                $values .= ", '$payment_channel'";
            }

            $columns .= ")";
            $values .= ")";

            $sql = "INSERT INTO payment $columns VALUES $values";

            error_log("Executing SQL: $sql");

            if ($this->db_write_query($sql)) {
                $payment_id = $this->last_insert_id();
                error_log("Payment recorded successfully with ID: $payment_id");
                return $payment_id;
            } else {
                $error = mysqli_error($conn);
                error_log("Payment recording failed. MySQL error: " . $error);
                return false;
            }

        } catch (Exception $e) {
            error_log("Error recording payment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all orders for a user
     * @param int $customer_id - Customer ID
     * @return array - Returns array of orders or empty array if failed
     */
    public function get_user_orders($customer_id) {
        try {
            $customer_id = (int)$customer_id;

            $sql = "SELECT
                        o.order_id,
                        o.invoice_no,
                        o.order_date,
                        o.order_status,
                        p.amt as total_amount,
                        p.currency,
                        COUNT(od.product_id) as item_count
                    FROM orders o
                    LEFT JOIN payment p ON o.order_id = p.order_id
                    LEFT JOIN orderdetails od ON o.order_id = od.order_id
                    WHERE o.customer_id = $customer_id
                    GROUP BY o.order_id
                    ORDER BY o.order_date DESC, o.order_id DESC";

            $result = $this->db_fetch_all($sql);
            return $result ?: [];

        } catch (Exception $e) {
            error_log("Error getting user orders: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get details of a specific order
     * @param int $order_id - Order ID
     * @param int $customer_id - Customer ID (for security check)
     * @return array|false - Returns order details or false if not found
     */
    public function get_order_details($order_id, $customer_id) {
        try {
            $order_id = (int)$order_id;
            $customer_id = (int)$customer_id;

            $sql = "SELECT
                        o.order_id,
                        o.invoice_no,
                        o.order_date,
                        o.order_status,
                        o.customer_id,
                        p.amt as total_amount,
                        p.currency,
                        p.payment_date
                    FROM orders o
                    LEFT JOIN payment p ON o.order_id = p.order_id
                    WHERE o.order_id = $order_id AND o.customer_id = $customer_id";

            return $this->db_fetch_one($sql);

        } catch (Exception $e) {
            error_log("Error getting order details: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all products in a specific order
     * @param int $order_id - Order ID
     * @return array|false - Returns array of products in the order or false if failed
     */
    public function get_order_products($order_id) {
        try {
            $order_id = (int)$order_id;

            $sql = "SELECT
                        od.product_id,
                        od.qty,
                        p.product_title,
                        p.product_price,
                        p.product_image,
                        (od.qty * p.product_price) as subtotal
                    FROM orderdetails od
                    INNER JOIN products p ON od.product_id = p.product_id
                    WHERE od.order_id = $order_id";

            return $this->db_fetch_all($sql);

        } catch (Exception $e) {
            error_log("Error getting order products: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update order status
     * @param int $order_id - Order ID
     * @param string $order_status - New order status
     * @return bool - Returns true if successful, false if failed
     */
    public function update_order_status($order_id, $order_status) {
        try {
            $order_id = (int)$order_id;
            $order_status = mysqli_real_escape_string($this->db_conn(), $order_status);

            $sql = "UPDATE orders SET order_status = '$order_status' WHERE order_id = $order_id";

            error_log("Updating order status: $order_id to $order_status");

            return $this->db_write_query($sql);

        } catch (Exception $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get total tickets sold (sum of all order quantities)
     * @return int - Total number of tickets sold
     */
    public function get_total_tickets_sold() {
        try {
            $sql = "SELECT COALESCE(SUM(qty), 0) as total FROM orderdetails";
            $result = $this->db_fetch_one($sql);
            return $result ? (int)$result['total'] : 0;
        } catch (Exception $e) {
            error_log("Error getting total tickets sold: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total revenue (sum of all payments)
     * @return float - Total revenue
     */
    public function get_total_revenue() {
        try {
            $sql = "SELECT COALESCE(SUM(amt), 0) as total FROM payment";
            $result = $this->db_fetch_one($sql);
            return $result ? (float)$result['total'] : 0.0;
        } catch (Exception $e) {
            error_log("Error getting total revenue: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Get recent events with ticket sales count
     * @param int $limit - Number of events to return (default 3)
     * @return array - Array of events with ticket counts
     */
    public function get_recent_events_with_tickets($limit = 3) {
        try {
            $limit = (int)$limit;
            $sql = "SELECT
                        p.product_id,
                        p.product_title,
                        COALESCE(SUM(od.qty), 0) as tickets_sold
                    FROM products p
                    LEFT JOIN orderdetails od ON p.product_id = od.product_id
                    GROUP BY p.product_id, p.product_title
                    ORDER BY p.product_id DESC
                    LIMIT $limit";

            $result = $this->db_fetch_all($sql);
            return $result ?: [];
        } catch (Exception $e) {
            error_log("Error getting recent events with tickets: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total tickets sold for a specific organizer
     * @param int $organizer_id - Organizer user ID
     * @return int - Total number of tickets sold for this organizer's events
     */
    public function get_total_tickets_sold_by_organizer($organizer_id) {
        try {
            $organizer_id = (int)$organizer_id;
            $sql = "SELECT COALESCE(SUM(od.qty), 0) as total
                    FROM orderdetails od
                    JOIN products p ON od.product_id = p.product_id
                    WHERE p.organizer_id = $organizer_id";
            $result = $this->db_fetch_one($sql);
            return $result ? (int)$result['total'] : 0;
        } catch (Exception $e) {
            error_log("Error getting total tickets sold by organizer: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total revenue for a specific organizer
     * @param int $organizer_id - Organizer user ID
     * @return float - Total revenue for this organizer's events
     */
    public function get_total_revenue_by_organizer($organizer_id) {
        try {
            $organizer_id = (int)$organizer_id;
            $sql = "SELECT COALESCE(SUM(p.amt), 0) as total
                    FROM payment p
                    JOIN orders o ON p.order_id = o.order_id
                    JOIN orderdetails od ON o.order_id = od.order_id
                    JOIN products pr ON od.product_id = pr.product_id
                    WHERE pr.organizer_id = $organizer_id";
            $result = $this->db_fetch_one($sql);
            return $result ? (float)$result['total'] : 0.0;
        } catch (Exception $e) {
            error_log("Error getting total revenue by organizer: " . $e->getMessage());
            return 0.0;
        }
    }

    /**
     * Get count of active events for a specific organizer
     * @param int $organizer_id - Organizer user ID
     * @return int - Number of active events
     */
    public function get_active_events_count_by_organizer($organizer_id) {
        try {
            $organizer_id = (int)$organizer_id;
            $sql = "SELECT COUNT(*) as total FROM products WHERE organizer_id = $organizer_id";
            $result = $this->db_fetch_one($sql);
            return $result ? (int)$result['total'] : 0;
        } catch (Exception $e) {
            error_log("Error getting active events count by organizer: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get recent events with ticket sales for a specific organizer
     * @param int $organizer_id - Organizer user ID
     * @param int $limit - Number of events to return
     * @return array - Array of events with ticket counts
     */
    public function get_recent_events_with_tickets_by_organizer($organizer_id, $limit = 3) {
        try {
            $organizer_id = (int)$organizer_id;
            $limit = (int)$limit;
            $sql = "SELECT
                        p.product_id,
                        p.product_title,
                        COALESCE(SUM(od.qty), 0) as tickets_sold
                    FROM products p
                    LEFT JOIN orderdetails od ON p.product_id = od.product_id
                    WHERE p.organizer_id = $organizer_id
                    GROUP BY p.product_id, p.product_title
                    ORDER BY p.product_id DESC
                    LIMIT $limit";

            $result = $this->db_fetch_all($sql);
            return $result ?: [];
        } catch (Exception $e) {
            error_log("Error getting recent events with tickets by organizer: " . $e->getMessage());
            return [];
        }
    }
}
