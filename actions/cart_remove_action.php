<?php
header('Content-Type: application/json');
require_once '../controllers/cart_controller.php';

$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
if ($cart_id <= 0) { echo json_encode(['status'=>'error','message'=>'Invalid cart line']); exit; }

$ok = cart_remove_ctr($cart_id);
echo json_encode($ok ? ['status'=>'success'] : ['status'=>'error','message'=>'Remove failed']);
?>