<?php
header('Content-Type: application/json');
session_start();

require_once '../controllers/category_controller.php';

$response = [];

//Step 1: Check if logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 1) {
    $response['status'] = 'error';
    $response['message'] = 'Access denied. Admins only.';
    echo json_encode($response);
    exit();
}

//Step 2: Collect data
$cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';
$organizer_id = $_SESSION['user_id'] ?? 0; // Get logged-in organizer ID

if (empty($cat_name)) {
    $response['status'] = 'error';
    $response['message'] = 'Category name is required';
    echo json_encode($response);
    exit();
}

//Step 3: Call controller
$result = add_category_ctr($cat_name, $organizer_id);

//Step 4: Return response
if ($result) {
    $response['status'] = 'success';
    $response['message'] = 'Category added successfully';
} else {
    $response['status'] = 'error';
    $response['message'] = 'Category already exists or could not be added';
}

echo json_encode($response);
exit();
?>
