<?php
// controllers/payment_request_controller.php
require_once dirname(__DIR__) . '/classes/payment_request_class.php';

/**
 * Submit a new payment request
 */
function submit_payment_request_ctr($organizer_id, $organizer_name, $total_tickets, $gross_revenue, $payment_method, $account_details)
{
    $payment_request = new payment_request_class();
    return $payment_request->submit_payment_request($organizer_id, $organizer_name, $total_tickets, $gross_revenue, $payment_method, $account_details);
}

/**
 * Get all payment requests for a specific organizer
 */
function get_organizer_payment_requests_ctr($organizer_id)
{
    $payment_request = new payment_request_class();
    return $payment_request->get_organizer_payment_requests($organizer_id);
}

/**
 * Get a single payment request by ID
 */
function get_payment_request_by_id_ctr($request_id)
{
    $payment_request = new payment_request_class();
    return $payment_request->get_payment_request_by_id($request_id);
}

/**
 * Get all payment requests (for super admin)
 */
function get_all_payment_requests_ctr()
{
    $payment_request = new payment_request_class();
    return $payment_request->get_all_payment_requests();
}

/**
 * Update payment request status (admin only)
 */
function update_payment_request_status_ctr($request_id, $status, $admin_notes = '')
{
    $payment_request = new payment_request_class();
    return $payment_request->update_payment_request_status($request_id, $status, $admin_notes);
}

/**
 * Check if organizer has pending request
 */
function has_pending_payment_request_ctr($organizer_id)
{
    $payment_request = new payment_request_class();
    return $payment_request->has_pending_request($organizer_id);
}
?>
