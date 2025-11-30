<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
session_start();

require_once dirname(__DIR__) . '/controllers/category_controller.php';


$response = [];

//Step 1: Check if logged in
if (!isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You must be logged in';
    echo json_encode($response);
    exit();
}

//Step 2: Admin only (adjust if customers should also view)
if ($_SESSION['role'] != 1) {
    $response['status'] = 'error';
    $response['message'] = 'Access denied. Admins only.';
    echo json_encode($response);
    exit();
}

//Step 3: Fetch categories for this organizer only
$organizer_id = $_SESSION['user_id'] ?? 0;
$categories = get_categories_by_organizer_ctr($organizer_id);

if ($categories) {
    $response['status'] = 'success';
    $response['data']   = $categories;
} else {
    $response['status'] = 'error';
    $response['message'] = 'No categories found';
}

echo json_encode($response);
exit();
?>
