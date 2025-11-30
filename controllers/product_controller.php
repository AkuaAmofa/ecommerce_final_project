<?php
require_once dirname(__DIR__) . '/classes/product_class.php';

/**
 * Add a new product
 */
function add_product_ctr($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $location, $event_date, $event_time, $organizer_id, $organizer_name, $ticket_quantity = 0) {
    $product = new product_class();
    return $product->add_product($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $location, $event_date, $event_time, $organizer_id, $organizer_name, $ticket_quantity);
}

/**
 * Update an existing product
 */
function update_product_ctr($product_id, $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $location, $event_date, $event_time, $organizer_id, $organizer_name, $ticket_quantity = 0) {
    $product = new product_class();
    return $product->update_product($product_id, $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $location, $event_date, $event_time, $organizer_id, $organizer_name, $ticket_quantity);
}

/**
 * Get a single product by ID
 */
function get_product_by_id_ctr($product_id) {
    $product = new product_class();
    return $product->get_product_by_id($product_id);
}

/**
 * Get all products (for admin)
 */
function get_all_products_ctr() {
    $product = new product_class();
    return $product->get_all_products();
}

/**
 * Delete a product
 */
function delete_product_ctr($product_id) {
    $product = new product_class();
    return $product->delete_product($product_id);
}

/* ============================================================
   WEEK 7: CUSTOMER-FACING FUNCTIONS
   ============================================================ */

/**
 * View all products (for storefront)
 */
function view_all_products_ctr() {
    $product = new product_class();
    return $product->view_all_products();
}

/**
 * View a single product
 */
function view_single_product_ctr($id) {
    $product = new product_class();
    return $product->view_single_product($id);
}

/**
 * Search products by title or keyword
 */
function search_products_ctr($query) {
    $product = new product_class();
    return $product->search_products($query);
}

/**
 * Filter products by category
 */
function filter_products_by_category_ctr($cat_id) {
    $product = new product_class();
    return $product->filter_products_by_category($cat_id);
}

/**
 * Filter products by brand
 */
function filter_products_by_brand_ctr($brand_id) {
    $product = new product_class();
    return $product->filter_products_by_brand($brand_id);
}

/**
 * Get products by organizer (for admin filtering)
 */
function get_products_by_organizer_ctr($organizer_id) {
    $product = new product_class();
    return $product->get_products_by_organizer($organizer_id);
}

/**
 * Decrease ticket quantity when tickets are purchased
 */
function decrease_ticket_quantity_ctr($product_id, $quantity) {
    $product = new product_class();
    return $product->decrease_ticket_quantity($product_id, $quantity);
}
?>
