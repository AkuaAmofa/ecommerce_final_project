<?php
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';
require_once '../controllers/category_controller.php'; // to fetch categories

if (!isAdmin()) {
    echo json_encode(['status'=>'error','message'=>'Access denied']);
    exit;
}

/**
 * Return both:
 *  - brands (flat list with brand_cat & cat_name) - filtered by organizer
 *  - categories (id+name) to populate the dropdown - filtered by organizer
 */
$organizer_id = $_SESSION['user_id'] ?? 0;
$brands = get_brands_by_organizer_ctr($organizer_id);
$cats   = get_categories_by_organizer_ctr($organizer_id);

echo json_encode([
    'status' => 'success',
    'data'   => [
        'brands'     => $brands ?: [],
        'categories' => $cats   ?: []
    ]
]);
?>