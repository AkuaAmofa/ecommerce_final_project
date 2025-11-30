<?php
// classes/payment_request_class.php
require_once dirname(__DIR__) . '/settings/db_class.php';

class payment_request_class extends db_connection
{
    /**
     * Submit a new payment request
     */
    public function submit_payment_request($organizer_id, $organizer_name, $total_tickets, $gross_revenue, $payment_method, $account_details)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        // Calculate commission (5%)
        $commission_rate = 0.05;
        $commission_amount = $gross_revenue * $commission_rate;
        $net_amount = $gross_revenue - $commission_amount;

        $sql = "INSERT INTO payment_requests
                (organizer_id, organizer_name, total_tickets_sold, gross_revenue, commission_amount, net_amount, payment_method, account_details)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isiiddss",
            $organizer_id,
            $organizer_name,
            $total_tickets,
            $gross_revenue,
            $commission_amount,
            $net_amount,
            $payment_method,
            $account_details
        );

        $ok = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /**
     * Get all payment requests for a specific organizer
     */
    public function get_organizer_payment_requests($organizer_id)
    {
        $organizer_id = (int)$organizer_id;
        $sql = "SELECT * FROM payment_requests
                WHERE organizer_id = $organizer_id
                ORDER BY request_date DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get a single payment request by ID
     */
    public function get_payment_request_by_id($request_id)
    {
        $request_id = (int)$request_id;
        $sql = "SELECT * FROM payment_requests WHERE request_id = $request_id LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get all payment requests (for admin)
     */
    public function get_all_payment_requests()
    {
        $sql = "SELECT * FROM payment_requests ORDER BY request_date DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Update payment request status (admin only)
     */
    public function update_payment_request_status($request_id, $status, $admin_notes = '')
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $processed_date = ($status === 'paid' || $status === 'rejected') ? date('Y-m-d H:i:s') : NULL;

        $sql = "UPDATE payment_requests
                SET request_status = ?,
                    admin_notes = ?,
                    processed_date = ?
                WHERE request_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $status, $admin_notes, $processed_date, $request_id);

        $ok = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /**
     * Check if organizer has any pending payment requests
     */
    public function has_pending_request($organizer_id)
    {
        $organizer_id = (int)$organizer_id;
        $sql = "SELECT COUNT(*) as count FROM payment_requests
                WHERE organizer_id = $organizer_id
                AND request_status = 'pending'";
        $result = $this->db_fetch_one($sql);
        return $result && $result['count'] > 0;
    }
}
?>
