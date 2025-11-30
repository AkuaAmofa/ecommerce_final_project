<?php
// Settings/core.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();

$response = [];

// Debug: Log incoming POST data
error_log("POST data: " . print_r($_POST, true));

// Prevent registration if already logged in
if (isset($_SESSION['user_id'])) {
    $response['status']  = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/user_controller.php';

// Collect form data safely
$name     = isset($_POST['name']) ? trim($_POST['name']) : '';
$email    = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$country  = isset($_POST['country']) ? trim($_POST['country']) : '';
$city     = isset($_POST['city']) ? trim($_POST['city']) : '';
$contact  = isset($_POST['contact']) ? trim($_POST['contact']) : '';
$role     = isset($_POST['role']) ? (int)$_POST['role'] : 2;   // default 2 = Customer
$image    = null; 

// Debug: Log processed data
error_log("Processed data - Name: $name, Email: $email, Country: $country, City: $city, Contact: $contact, Role: $role");

// Step 1: Validate required fields
if (empty($name) || empty($email) || empty($password) || empty($country) || empty($city) || empty($contact)) {
    $response['status']  = 'error';
    $response['message'] = 'All fields are required';
    $response['debug'] = [
        'name' => $name,
        'email' => $email,
        'password' => !empty($password) ? 'provided' : 'empty',
        'country' => $country,
        'city' => $city,
        'contact' => $contact
    ];
    echo json_encode($response);
    exit();
}

// Step 2: Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status']  = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Step 3: Check if email already exists
try {
    if (check_email_ctr($email)) {
        $response['status']  = 'error';
        $response['message'] = 'Email already exists';
        echo json_encode($response);
        exit();
    }
} catch (Exception $e) {
    error_log("Error checking email: " . $e->getMessage());
    $response['status']  = 'error';
    $response['message'] = 'Database error while checking email';
    echo json_encode($response);
    exit();
}

// Step 4: Attempt registration
try {
    $result = register_user_ctr($name, $email, $password, $country, $city, $contact, $role, $image);
    
    if ($result) {
        $response['status']  = 'success';
        $response['message'] = 'Registered successfully';
        error_log("User registered successfully: $email");
    } else {
        $response['status']  = 'error';
        $response['message'] = 'Failed to register - database operation failed';
        error_log("Registration failed for email: $email");
    }
} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    $response['status']  = 'error';
    $response['message'] = 'Registration failed due to server error';
}

echo json_encode($response);
exit();
?>