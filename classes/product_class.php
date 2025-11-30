<?php
require_once dirname(__DIR__) . '/settings/db_class.php';

class product_class extends db_connection
{
    /** ----------------------------------------------------------------
     * ADD PRODUCT
     * ----------------------------------------------------------------
     */
    public function add_product($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $location, $event_date, $event_time)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "INSERT INTO products
                (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords, product_location, event_date, event_time)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("iisdssssss",
            $cat_id,
            $brand_id,
            $title,
            $price,
            $desc,
            $image,
            $keywords,
            $location,
            $event_date,
            $event_time
        );

        $ok = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /** ----------------------------------------------------------------
     * UPDATE PRODUCT (for edit)
     * ----------------------------------------------------------------
     */
    public function update_product($product_id, $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $location, $event_date, $event_time)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "UPDATE products
                   SET product_cat = ?,
                       product_brand = ?,
                       product_title = ?,
                       product_price = ?,
                       product_desc = ?,
                       product_image = ?,
                       product_keywords = ?,
                       product_location = ?,
                       event_date = ?,
                       event_time = ?
                 WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("iisdssssssi",
            $cat_id,
            $brand_id,
            $title,
            $price,
            $desc,
            $image,
            $keywords,
            $location,
            $event_date,
            $event_time,
            $product_id
        );

        $ok = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /** ----------------------------------------------------------------
     * GET ONE PRODUCT BY ID
     * ----------------------------------------------------------------
     */
    public function get_product_by_id($product_id)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "SELECT p.*, c.cat_name, b.brand_name
                  FROM products p
                  JOIN categories c ON p.product_cat = c.cat_id
                  JOIN brands b     ON p.product_brand = b.brand_id
                 WHERE p.product_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $res;
    }

    /** ----------------------------------------------------------------
     * GET ALL PRODUCTS
     * ----------------------------------------------------------------
     */
    public function get_all_products()
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "SELECT p.*, c.cat_name, b.brand_name
                  FROM products p
                  JOIN categories c ON p.product_cat = c.cat_id
                  JOIN brands b     ON p.product_brand = b.brand_id
              ORDER BY p.product_id DESC";

        $result = $conn->query($sql);
        if (!$result) return false;

        $data = $result->fetch_all(MYSQLI_ASSOC);
        $conn->close();
        return $data;
    }

    /** ----------------------------------------------------------------
     * DELETE PRODUCT
     * ----------------------------------------------------------------
     */
    public function delete_product($product_id)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "DELETE FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("i", $product_id);
        $ok = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $ok;
    }

    /** ----------------------------------------------------------------
     * VIEW ALL PRODUCTS (for customers)
     * ----------------------------------------------------------------
     */
    public function view_all_products()
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "SELECT p.*, c.cat_name, b.brand_name
                  FROM products p
                  JOIN categories c ON p.product_cat = c.cat_id
                  JOIN brands b ON p.product_brand = b.brand_id
              ORDER BY p.product_id DESC";

        $result = $conn->query($sql);
        if (!$result) return false;

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /** ----------------------------------------------------------------
     * VIEW SINGLE PRODUCT
     * ----------------------------------------------------------------
     */
    public function view_single_product($id)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "SELECT p.*, c.cat_name, b.brand_name
                  FROM products p
                  JOIN categories c ON p.product_cat = c.cat_id
                  JOIN brands b ON p.product_brand = b.brand_id
                 WHERE p.product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $data;
    }

    /** ----------------------------------------------------------------
     * SEARCH PRODUCTS (by title or keyword)
     * ----------------------------------------------------------------
     */
    public function search_products($query)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $search = "%$query%";
        $sql = "SELECT p.*, c.cat_name, b.brand_name
                  FROM products p
                  JOIN categories c ON p.product_cat = c.cat_id
                  JOIN brands b ON p.product_brand = b.brand_id
                 WHERE p.product_title LIKE ? OR p.product_keywords LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $data;
    }

    /** ----------------------------------------------------------------
     * FILTER BY CATEGORY
     * ----------------------------------------------------------------
     */
    public function filter_products_by_category($cat_id)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "SELECT p.*, c.cat_name, b.brand_name
                  FROM products p
                  JOIN categories c ON p.product_cat = c.cat_id
                  JOIN brands b ON p.product_brand = b.brand_id
                 WHERE p.product_cat = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $data;
    }

    /** ----------------------------------------------------------------
     * FILTER BY BRAND
     * ----------------------------------------------------------------
     */
    public function filter_products_by_brand($brand_id)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "SELECT p.*, c.cat_name, b.brand_name
                  FROM products p
                  JOIN categories c ON p.product_cat = c.cat_id
                  JOIN brands b ON p.product_brand = b.brand_id
                 WHERE p.product_brand = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $data;
    }
}
?>
