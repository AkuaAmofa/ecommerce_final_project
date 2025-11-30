<?php
// actions/submit_payment_request_action.php
header('Content-Type: application/json');
session_start();

require_once '../controllers/payment_request_controller.php';
require_once '../controllers/order_controller.php';
require_once '../settings/core.php';

// Check if user is logged in and is a super admin
if (!isLoggedIn() || !isSuperAdmin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied. Super admin access required.'
    ]);
    exit();
}

// Get organizer information
$organizer_id = $_SESSION['user_id'] ?? 0;
$organizer_name = $_SESSION['name'] ?? '';

// Check if organizer already has a pending request
if (has_pending_payment_request_ctr($organizer_id)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You already have a pending payment request. Please wait for it to be processed.'
    ]);
    exit();
}

// Get payment details from POST
$payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
$account_details = isset($_POST['account_details']) ? trim($_POST['account_details']) : '';

// Validate inputs
if (empty($payment_method) || empty($account_details)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please provide payment method and account details.'
    ]);
    exit();
}

// Get organizer's revenue statistics
$total_tickets = get_total_tickets_sold_by_organizer_ctr($organizer_id);
$gross_revenue = get_total_revenue_by_organizer_ctr($organizer_id);

// Validate that there's revenue to request
if ($gross_revenue <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You have no revenue to request payment for.'
    ]);
    exit();
}

// Submit the payment request
$result = submit_payment_request_ctr(
    $organizer_id,
    $organizer_name,
    $total_tickets,
    $gross_revenue,
    $payment_method,
    $account_details
);

if ($result) {
    // Calculate commission for display
    $commission = $gross_revenue * 0.05;
    $net_amount = $gross_revenue - $commission;

    echo json_encode([
        'status' => 'success',
        'message' => 'Payment request submitted successfully!',
        'data' => [
            'gross_revenue' => number_format($gross_revenue, 2),
            'commission' => number_format($commission, 2),
            'net_amount' => number_format($net_amount, 2)
        ]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to submit payment request. Please try again.'
    ]);
}
exit();
?>
