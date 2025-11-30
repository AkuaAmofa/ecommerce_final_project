<?php

header('Content-Type: application/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$response = [];

echo "<h3>Debug Information</h3>";

// Test 1: Check if files exist
echo "<h4>1. File Existence Check:</h4>";
$files_to_check = [
    '../controllers/user_controller.php',
    '../classes/user_class.php',
    '../settings/db_class.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "$file exists<br>";
    } else {
        echo "$file NOT FOUND<br>";
    }
}

// Test 2: Try to include the controller
echo "<h4>2. Controller Include Test:</h4>";
try {
    require_once '../controllers/user_controller.php';
    echo " Controller included successfully<br>";
} catch (Exception $e) {
    echo " Controller include failed: " . $e->getMessage() . "<br>";
}

// Test 3: Check if functions exist
echo "<h4>3. Function Existence Check:</h4>";
if (function_exists('register_user_ctr')) {
    echo " register_user_ctr function exists<br>";
} else {
    echo " register_user_ctr function NOT FOUND<br>";
}

if (function_exists('check_email_ctr')) {
    echo "check_email_ctr function exists<br>";
} else {
    echo "check_email_ctr function NOT FOUND<br>";
}

// Test 4: Database connection test
echo "<h4>4. Database Connection Test:</h4>";
try {
    require_once '../settings/db_class.php';
    $db = new db_connection();
    $db->db_connect();
    echo " Database connection successful<br>";
} catch (Exception $e) {
    echo " Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 5: POST data check
echo "<h4>5. POST Data:</h4>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Test 6: Try a simple registration test
if (!empty($_POST)) {
    echo "<h4>6. Registration Test:</h4>";
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $country = $_POST['country'] ?? '';
    $city = $_POST['city'] ?? '';
    $contact = $_POST['contact'] ?? '';
    
    echo "Received data:<br>";
    echo "Name: $name<br>";
    echo "Email: $email<br>";
    echo "Password: " . (empty($password) ? 'EMPTY' : 'PROVIDED') . "<br>";
    echo "Country: $country<br>";
    echo "City: $city<br>";
    echo "Contact: $contact<br>";
    
    if (function_exists('register_user_ctr')) {
        try {
            $result = register_user_ctr($name, $email, $password, $country, $city, $contact, 2, null);
            echo "Registration result: " . ($result ? 'SUCCESS' : 'FAILED') . "<br>";
        } catch (Exception $e) {
            echo "Registration error: " . $e->getMessage() . "<br>";
        }
    }
}
?>