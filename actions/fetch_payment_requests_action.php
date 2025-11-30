<?php
// actions/fetch_payment_requests_action.php
header('Content-Type: application/json');
session_start();

require_once '../controllers/payment_request_controller.php';
require_once '../settings/core.php';

// Check if user is logged in and is an admin (organizer)
if (!isLoggedIn() || !isAdmin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied.'
    ]);
    exit();
}

// Get organizer's payment requests
$organizer_id = $_SESSION['user_id'] ?? 0;
$requests = get_organizer_payment_requests_ctr($organizer_id);

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
