<?php
require_once '../settings/core.php';
require_once '../controllers/cart_controller.php';

header('Content-Type: application/json');

$c_id = isLoggedIn() ? $_SESSION['user_id'] : null;
$ip   = $_SERVER['REMOTE_ADDR'];

$ok = cart_clear_ctr($c_id, $ip);

echo json_encode(
    $ok ? ['status' => 'success']
        : ['status' => 'error', 'message' => 'Could not empty cart']
);
?>