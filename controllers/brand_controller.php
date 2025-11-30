<?php
// controllers/brand_controller.php
require_once dirname(__DIR__) . '/classes/brand_class.php';

function add_brand_ctr($name, $cat_id, $organizer_id = null) {
    $b = new Brand();
    return $b->addBrand($name, $cat_id, $organizer_id);
}

function get_all_brands_ctr() {
    $b = new Brand();
    return $b->getAllBrandsWithCategory();
}

function get_brands_by_organizer_ctr($organizer_id) {
    $b = new Brand();
    return $b->getBrandsByOrganizer($organizer_id);
}

function update_brand_ctr($id, $name) {
    $b = new Brand();
    return $b->updateBrand($id, $name);
}

function delete_brand_ctr($id) {
    $b = new Brand();
    return $b->deleteBrand($id);
}
?>
