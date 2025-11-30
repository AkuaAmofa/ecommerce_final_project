<?php
header('Content-Type: application/json');
session_start();

require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';
require_once '../controllers/product_controller.php';

$response = [];

// Step 1: Check if logged in
if (!isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You must be logged in';
    echo json_encode($response);
    exit();
}

// Step 2: Ensure admin access
if ($_SESSION['role'] != 1) {
    $response['status'] = 'error';
    $response['message'] = 'Access denied. Admins only.';
    echo json_encode($response);
    exit();
}

// Step 3: Collect category ID
$cat_id = isset($_POST['cat_id']) ? (int)$_POST['cat_id'] : 0;

if ($cat_id <= 0) {
    $response['status'] = 'error';
    $response['message'] = 'Category ID is required';
    echo json_encode($response);
    exit();
}

// Step 4: Cascade delete - Category -> Brands -> Products
try {
    require_once '../settings/db_class.php';
    $db = new db_connection();
    $conn = $db->db_conn();

    // Get all brands in this category
    $sql = "SELECT brand_id FROM brands WHERE brand_cat = $cat_id";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        while ($brand = mysqli_fetch_assoc($result)) {
            $brand_id = $brand['brand_id'];

            // For each brand, delete all its products
            $sql2 = "SELECT product_id FROM products WHERE product_brand = $brand_id";
            $result2 = mysqli_query($conn, $sql2);

            if ($result2) {
                while ($product = mysqli_fetch_assoc($result2)) {
                    $product_id = $product['product_id'];

                    // Delete orderdetails first for each product
                    $delete_od_sql = "DELETE FROM orderdetails WHERE product_id = $product_id";
                    mysqli_query($conn, $delete_od_sql);

                    // Then delete the product
                    delete_product_ctr($product_id);
                }
            }

            // Delete the brand
            delete_brand_ctr($brand_id);
        }
    }

    // Finally delete the category
    $result = delete_category_ctr($cat_id);

    // Step 5: Return response
    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Category and all associated brands and events deleted successfully';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Unable to delete category. Please try again.';
    }
} catch (Exception $e) {
    $response['status'] = 'error';
    $response['message'] = 'Unable to delete category. This category may have associated data that needs to be removed first.';
}

echo json_encode($response);
exit();
?>
