<?php
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

if (!isAdmin()) {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

$name   = isset($_POST['brand_name']) ? trim($_POST['brand_name']) : '';
$cat_id = isset($_POST['brand_cat']) ? (int)$_POST['brand_cat'] : 0;
$organizer_id = $_SESSION['user_id'] ?? 0; // Get logged-in organizer ID

if ($name === '' || $cat_id <= 0) {
    echo json_encode(['status'=>'error','message'=>'Brand name and category are required']);
    exit;
}

$res = add_brand_ctr($name, $cat_id, $organizer_id);

if ($res && $res['ok']) {
    echo json_encode(['status'=>'success','message'=>'Brand created']);
} else {
    $msg = is_array($res) && isset($res['msg']) ? $res['msg'] : 'Failed to create brand';
    echo json_encode(['status'=>'error','message'=>$msg]);
}
