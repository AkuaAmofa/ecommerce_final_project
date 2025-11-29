<?php
require_once dirname(__DIR__) . '/classes/order_class.php';

/**
 * Create a new order
 * @param int $customer_id - Customer ID
 * @param string $invoice_no - Unique invoice number
 * @param string $order_date - Order date (YYYY-MM-DD) - optional
 * @param string $status - Order status (default: 'Pending')
 * @return int|null - Returns order_id if successful, null if failed
 */
function create_order_ctr($customer_id, $invoice_no, $order_date = null, $status = 'Pending') {
    $ord = new order_class();
    return $ord->create_order($customer_id, $invoice_no, $order_date, $status);
}

/**
 * Add order details (products) to an order
 * @param int $order_id - Order ID
 * @param int $product_id - Product ID
 * @param int $qty - Quantity ordered
 * @param float $unit_price - Unit price (optional)
 * @return bool - Returns true if successful, false if failed
 */
function add_order_details_ctr($order_id, $product_id, $qty, $unit_price = null) {
    $ord = new order_class();
    return $ord->add_order_detail($order_id, $product_id, $qty, $unit_price);
}

/**
 * Record a payment for an order
 * @param float $amount - Payment amount
 * @param int $customer_id - Customer ID
 * @param int $order_id - Order ID
 * @param string $currency - Currency code (default: 'GHS')
 * @param string $payment_date - Payment date (optional)
 * @param string $payment_method - Payment method (default: 'direct')
 * @param string $transaction_ref - Transaction reference
 * @param string $authorization_code - Authorization code
 * @param string $payment_channel - Payment channel
 * @return int|bool - Returns payment_id if successful, false if failed
 */
function record_payment_ctr($amount, $customer_id, $order_id, $currency = 'GHS', $payment_date = null, $payment_method = 'direct', $transaction_ref = null, $authorization_code = null, $payment_channel = null) {
    $ord = new order_class();
    return $ord->record_payment($amount, $customer_id, $order_id, $currency, $payment_date, $payment_method, $transaction_ref, $authorization_code, $payment_channel);
}

/**
 * Get all orders for a user
 * @param int $customer_id - Customer ID
 * @return array - Returns array of orders or empty array if failed
 */
function get_user_orders_ctr($customer_id) {
    $ord = new order_class();
    return $ord->get_user_orders($customer_id);
}

/**
 * Get details of a specific order
 * @param int $order_id - Order ID
 * @param int $customer_id - Customer ID (for security check)
 * @return array|false - Returns order details or false if not found
 */
function get_order_details_ctr($order_id, $customer_id) {
    $ord = new order_class();
    return $ord->get_order_details($order_id, $customer_id);
}

/**
 * Get all products in a specific order
 * @param int $order_id - Order ID
 * @return array|false - Returns array of products in the order or false if failed
 */
function get_order_products_ctr($order_id) {
    $ord = new order_class();
    return $ord->get_order_products($order_id);
}

/**
 * Update order status
 * @param int $order_id - Order ID
 * @param string $order_status - New order status
 * @return bool - Returns true if successful, false if failed
 */
function update_order_status_ctr($order_id, $order_status) {
    $ord = new order_class();
    return $ord->update_order_status($order_id, $order_status);
}

/**
 * Get total tickets sold (sum of all order quantities)
 * @return int - Total number of tickets sold
 */
function get_total_tickets_sold_ctr() {
    $ord = new order_class();
    return $ord->get_total_tickets_sold();
}

/**
 * Get total revenue (sum of all payments)
 * @return float - Total revenue
 */
function get_total_revenue_ctr() {
    $ord = new order_class();
    return $ord->get_total_revenue();
}

/**
 * Get count of active events (products)
 * @return int - Number of active events
 */
function get_active_events_count_ctr() {
    require_once dirname(__DIR__) . '/controllers/product_controller.php';
    $products = get_all_products_ctr();
    return count($products);
}
