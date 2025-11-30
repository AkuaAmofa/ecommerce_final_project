<?php
// actions/delete_product_action.php
header('Content-Type: application/json');
session_start();

require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

$response = [];

// Check if user is admin
if (!isAdmin()) {
    $response = [
        'status' => 'error',
        'message' => 'Access denied. Only admins can delete products.'
    ];
    echo json_encode($response);
    exit();
}

$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

if ($product_id <= 0) {
    $response = [
        'status' => 'error',
        'message' => 'Invalid product ID.'
    ];
    echo json_encode($response);
    exit();
}

try {
    // First, delete associated order details (if any exist)
    require_once '../settings/db_class.php';
    $db = new db_connection();
    $conn = $db->db_conn();

    // Delete from orderdetails first (foreign key constraint)
    $sql = "DELETE FROM orderdetails WHERE product_id = $product_id";
    mysqli_query($conn, $sql);

    // Now delete the product
    $result = delete_product_ctr($product_id);

    if ($result) {
        $response = [
            'status' => 'success',
            'message' => 'Event deleted successfully.'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Unable to delete event. Please try again.'
        ];
    }
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Unable to delete event. This event may have associated orders that need to be removed first.'
    ];
}

echo json_encode($response);
exit();
?>

