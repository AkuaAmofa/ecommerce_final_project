<?php
// actions/upload_product_image_action.php
header('Content-Type: application/json');
session_start();

$response = [];

try {
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No image uploaded or an upload error occurred.');
    }

    // Simple uploads directory (relative to this file)
    $upload_dir = __DIR__ . '/../uploads/';

    // Create uploads directory if it does not exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Upload directory not found and could not be created.');
        }
    }

    // Basic file type validation
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($_FILES['product_image']['tmp_name'] ?? '');

    if (!in_array($file_type, $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.');
    }

    // Generate unique filename (keep original extension)
    $ext = pathinfo($_FILES['product_image']['name'], PATHINFO_EXTENSION);
    $filename = uniqid('prod_', true) . '.' . $ext;

    $target_path = $upload_dir . $filename;

    // Try to move the uploaded file
    if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $target_path)) {
        throw new Exception('File upload failed. Could not move file.');
    }

    // Make sure it is readable
    @chmod($target_path, 0644);

    $response = [
        'status'    => 'success',
        'message'   => 'Image uploaded successfully.',
        'file_path' => $filename   // only store the filename in DB
    ];

} catch (Exception $e) {
    $response = [
        'status'  => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
exit();
