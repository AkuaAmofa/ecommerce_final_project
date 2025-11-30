<?php
header('Content-Type: application/json');
session_start();

require_once '../controllers/product_controller.php';

// Collect form data
$product_cat      = $_POST['product_cat'] ?? '';
$product_brand    = $_POST['product_brand'] ?? '';
$product_title    = trim($_POST['product_title'] ?? '');
$product_price    = $_POST['product_price'] ?? '';
$product_desc     = trim($_POST['product_desc'] ?? '');
$product_keywords = trim($_POST['product_keywords'] ?? '');
$product_image    = $_POST['product_image'] ?? ''; // from uploads
$product_location = trim($_POST['product_location'] ?? '');
$event_date       = $_POST['event_date'] ?? '';
$event_time       = $_POST['event_time'] ?? '';

// If file was directly uploaded (not via AJAX)
if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
    // Determine if we're on the server or local
    $is_server = strpos(__DIR__, '/home/akua.amofa') !== false;

    if ($is_server) {
        // On production server - use absolute path
        $uploadDir = '/home/akua.amofa/public_html/uploads';
    } else {
        // On local development - use relative path
        $uploads_path = __DIR__ . '/../uploads/';
        $uploadDir = realpath($uploads_path);

        if (!$uploadDir) {
            if (!mkdir($uploads_path, 0755, true) && !is_dir($uploads_path)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Upload directory not found and could not be created."
                ]);
                exit();
            }
            $uploadDir = realpath($uploads_path);
        }
    }

    $originalName = basename($_FILES['product_image']['name']);
    $uniqueName = "prod_" . uniqid() . "_" . $originalName;
    $targetPath = $uploadDir . DIRECTORY_SEPARATOR . $uniqueName;

    if (move_uploaded_file($_FILES['product_image']['tmp_name'], $targetPath)) {
        $product_image = $uniqueName; // store only filename
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "File upload failed while adding product."
        ]);
        exit();
    }
}

// Validate required fields
if (empty($product_cat) || empty($product_brand) || empty($product_title) || empty($product_price)) {
    echo json_encode([
        "status" => "error",
        "message" => "All required fields must be filled!"
    ]);
    exit();
}

// Add product
$result = add_product_ctr(
    $product_cat,
    $product_brand,
    $product_title,
    $product_price,
    $product_desc,
    $product_image,
    $product_keywords,
    $product_location,
    $event_date,
    $event_time
);

if ($result) {
    echo json_encode([
        "status" => "success",
        "message" => "Product added successfully!"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Failed to add product. Please try again."
    ]);
}
exit();
?>
