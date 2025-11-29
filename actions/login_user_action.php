<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
session_start();

require_once '../controllers/user_controller.php';

$response = [];

// Collect input
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Validate required fields
if (empty($email) || empty($password)) {
    $response['status']  = 'error';
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status']  = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Call controller
$user = login_user_ctr($email, $password);

if ($user) {
    // Set session variables
    $_SESSION['user_id'] = $user['customer_id'];
    $_SESSION['name']    = $user['customer_name'];
    $_SESSION['role']    = $user['user_role'];
    $_SESSION['email']   = $user['customer_email'];

    $response['status']  = 'success';
    $response['message'] = 'Login successful';
    $response['role']    = $user['user_role']; // Return role for redirect
    $response['is_admin'] = ($user['user_role'] == 1); // 1 = admin
} else {
    $response['status']  = 'error';
    $response['message'] = 'Invalid email or password';
}

echo json_encode($response);
exit();