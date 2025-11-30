<?php
// actions/fetch_all_payment_requests_action.php
header('Content-Type: application/json');
session_start();

require_once '../controllers/payment_request_controller.php';
require_once '../settings/core.php';

// Check if user is logged in and is an admin
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied.'
    ]);
    exit();
}

// Get all payment requests (for super admin)
$requests = get_all_payment_requests_ctr();

if ($requests !== false) {
    echo json_encode([
        'status' => 'success',
        'data' => $requests
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch payment requests.'
    ]);
}
exit();
?>
