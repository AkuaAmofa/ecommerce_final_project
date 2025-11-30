<?php
// delete_brand_action.php
header('Content-Type: application/json');
session_start();

require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';
require_once '../controllers/product_controller.php';

// Step 1: Access control (Admins only)
if (!isAdmin()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Access denied. Only admins can delete brands.'
    ]);
    exit();
}

// Step 2: Collect input
$id = isset($_POST['brand_id']) ? (int)$_POST['brand_id'] : 0;

// Step 3: Validate input
if ($id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid brand ID.'
    ]);
    exit();
}

// Step 4: First delete all products (events) under this brand
try {
    require_once '../settings/db_class.php';
    $db = new db_connection();
    $conn = $db->db_conn();

    // Get all products for this brand
    $sql = "SELECT product_id FROM products WHERE product_brand = $id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Delete orderdetails first for each product
            $product_id = $row['product_id'];
            $delete_od_sql = "DELETE FROM orderdetails WHERE product_id = $product_id";
            mysqli_query($conn, $delete_od_sql);

            // Then delete the product
            delete_product_ctr($product_id);
        }
    }

    // Step 5: Now delete the brand
    $result = delete_brand_ctr($id);

    // Step 6: Respond
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Brand and all associated events deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Unable to delete brand. Please try again.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unable to delete brand. This brand may have associated data that needs to be removed first.'
    ]);
}
exit();
?>
