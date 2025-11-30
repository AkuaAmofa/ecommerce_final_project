<?php
// actions/update_product_action.php
header('Content-Type: application/json');
session_start();

require_once '../controllers/product_controller.php';

// Response array
$response = [];

// Collect and sanitize inputs
$product_id   = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$product_cat  = isset($_POST['product_cat']) ? intval($_POST['product_cat']) : 0;
$product_brand= isset($_POST['product_brand']) ? intval($_POST['product_brand']) : 0;
$title        = isset($_POST['product_title']) ? trim($_POST['product_title']) : '';
$price        = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0.0;
$desc         = isset($_POST['product_desc']) ? trim($_POST['product_desc']) : '';
$keywords     = isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : '';
$image        = $_POST['product_image'] ?? ''; // from upload or existing filename
$location     = isset($_POST['product_location']) ? trim($_POST['product_location']) : '';
$event_date   = $_POST['event_date'] ?? '';
$event_time   = $_POST['event_time'] ?? '';

// Basic validation
if ($product_id <= 0 || empty($title) || $price <= 0) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Product ID, title, and price are required.'
    ]);
    exit();
}

// Handle direct image upload (optional)
if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    // Determine if we're on the server or local
    $is_server = strpos(__DIR__, '/home/akua.amofa') !== false;

    if ($is_server) {
        // On production server - use absolute path
        $upload_dir = '/home/akua.amofa/public_html/uploads';
    } else {
        // On local development - use relative path
        $uploads_path = __DIR__ . '/../uploads/';
        $upload_dir = realpath($uploads_path);

        if (!$upload_dir) {
            if (!mkdir($uploads_path, 0755, true) && !is_dir($uploads_path)) {
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Upload directory not found and could not be created.'
                ]);
                exit();
            }
            $upload_dir = realpath($uploads_path);
        }
    }

    $originalName = basename($_FILES['product_image']['name']);
    $uniqueName = "prod_" . uniqid() . "_" . $originalName;
    $targetPath = $upload_dir . DIRECTORY_SEPARATOR . $uniqueName;

    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
        $image = $uniqueName; // store only filename
    } else {
        echo json_encode([
            'status'  => 'error',
            'message' => 'Image upload failed during update.'
        ]);
        exit();
    }
}

// Run update query via controller
$result = update_product_ctr($product_id, $product_cat, $product_brand, $title, $price, $desc, $image, $keywords, $location, $event_date, $event_time);

if ($result) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Product updated successfully!'
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to update product. Please try again.'
    ]);
}
exit();
?>
