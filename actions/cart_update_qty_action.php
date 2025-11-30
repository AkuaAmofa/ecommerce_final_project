<?php
header('Content-Type: application/json');
require_once '../controllers/cart_controller.php';

$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$qty     = isset($_POST['qty']) ? (int)$_POST['qty'] : 1;

if ($cart_id <= 0) { echo json_encode(['status'=>'error','message'=>'Invalid cart line']); exit; }

$ok = cart_update_qty_ctr($cart_id, max(1,$qty));
echo json_encode($ok ? ['status'=>'success'] : ['status'=>'error','message'=>'Update failed']);
?>