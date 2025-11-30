<?php
// actions/update_payment_status_action.php
header('Content-Type: application/json');
session_start();

require_once '../controllers/payment_request_controller.php';
require_once '../settings/core.php';

// Check if user is logged in and is a super admin
if (!isLoggedIn() || !isSuperAdmin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied. Super admin access required.'
    ]);
    exit();
}

// Get request data
$request_id = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;
$status = isset($_POST['status']) ? trim($_POST['status']) : '';
$admin_notes = isset($_POST['admin_notes']) ? trim($_POST['admin_notes']) : '';

// Validate inputs
if ($request_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request ID.'
    ]);
    exit();
}

// Validate status
$valid_statuses = ['pending', 'processing', 'paid', 'rejected'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid status.'
    ]);
    exit();
}

// Update the payment request status
$result = update_payment_request_status_ctr($request_id, $status, $admin_notes);

if ($result) {
    $status_messages = [
        'processing' => 'Payment request is now being processed.',
        'paid' => 'Payment has been marked as completed.',
        'rejected' => 'Payment request has been rejected.',
        'pending' => 'Payment request status updated.'
    ];

    echo json_encode([
        'status' => 'success',
        'message' => $status_messages[$status]
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update payment status. Please try again.'
    ]);
}
exit();
?>
