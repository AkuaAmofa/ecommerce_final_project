<?php
// actions/upload_product_image_action.php
header('Content-Type: application/json');
session_start();

$response = [];

try {
    // Check for file presence
    if (!isset($_FILES['product_image']) || $_FILES['product_image']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No image uploaded or an upload error occurred.');
    }

    // Define the ONLY allowed upload directory (try to create if missing)
    $uploads_path = __DIR__ . '/../uploads/';
    $upload_dir = realpath($uploads_path);

    if (!$upload_dir) {
        // Attempt to create the uploads directory with safe permissions
        error_log('uploads folder not found, attempting to create: ' . $uploads_path);
        if (!mkdir($uploads_path, 0755, true) && !is_dir($uploads_path)) {
            throw new Exception('Upload directory not found and could not be created.');
        }
        $upload_dir = realpath($uploads_path);
    }

    // Validate the file type (basic security)
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = mime_content_type($_FILES['product_image']['tmp_name']);
    if (!in_array($file_type, $allowed_types)) {
        throw new Exception('Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.');
    }

    // Generate a secure unique filename
    $filename = uniqid('prod_', true) . '_' . basename($_FILES['product_image']['name']);
    $target_path = $upload_dir . DIRECTORY_SEPARATOR . $filename;

    // Ensure the target path is still inside uploads/
    $resolved_path = realpath(dirname($target_path));
    if ($resolved_path !== $upload_dir) {
        throw new Exception('Invalid upload path. Uploads allowed only inside uploads/ folder.');
    }

    // Diagnostic logging before moving
    error_log('upload tmp_name: ' . ($_FILES['product_image']['tmp_name'] ?? ''));
    error_log('is_uploaded_file: ' . (is_uploaded_file($_FILES['product_image']['tmp_name']) ? 'true' : 'false'));
    error_log('target_path: ' . $target_path);
    error_log('upload_dir: ' . $upload_dir . ' perms: ' . substr(sprintf('%o', fileperms($upload_dir)), -4));

    // Move file to the uploads directory
    if (!move_uploaded_file($_FILES['product_image']['tmp_name'], $target_path)) {
        error_log('move_uploaded_file failed. last_error: ' . print_r(error_get_last(), true));
        throw new Exception('File upload failed. Could not move file.');
    }

    // Ensure the uploaded file is readable by the webserver (safe default)
    @chmod($target_path, 0644);

    // ✅ Return ONLY the filename to store in the DB
    $response = [
        'status' => 'success',
        'message' => 'Image uploaded successfully.',
        'file_path' => $filename  // <-- Only filename
    ];
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
exit();
?>
