<?php
require_once dirname(__DIR__) . '/settings/db_class.php';

class product_class extends db_connection
{
    /** ----------------------------------------------------------------
     * ADD PRODUCT
     * ----------------------------------------------------------------
     */
    public function add_product($cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $location, $event_date, $event_time, $organizer_id, $organizer_name, $ticket_quantity = 0)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "INSERT INTO products
                (product_cat, product_brand, product_title, product_price, ticket_quantity, product_desc, product_image, product_keywords, product_location, event_date, event_time, organizer_id, organizer_name)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("iisdisssssssi",
            $cat_id,
            $brand_id,
            $title,
            $price,
            $ticket_quantity,
            $desc,
            $image,
            $keywords,
            $location,
            $event_date,
            $event_time,
            $organizer_id,
            $organizer_name
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
    public function update_product($product_id, $cat_id, $brand_id, $title, $price, $desc, $image, $keywords, $location, $event_date, $event_time, $organizer_id, $organizer_name, $ticket_quantity = 0)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "UPDATE products
                   SET product_cat = ?,
                       product_brand = ?,
                       product_title = ?,
                       product_price = ?,
                       ticket_quantity = ?,
                       product_desc = ?,
                       product_image = ?,
                       product_keywords = ?,
                       product_location = ?,
                       event_date = ?,
                       event_time = ?,
                       organizer_id = ?,
                       organizer_name = ?
                 WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("iisdisssssssii",
            $cat_id,
            $brand_id,
            $title,
            $price,
            $ticket_quantity,
            $desc,
            $image,
            $keywords,
            $location,
            $event_date,
            $event_time,
            $organizer_id,
            $organizer_name,
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
     * VIEW ALL PRODUCTS (for customers) - Only show events with available tickets
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
                 WHERE p.ticket_quantity > 0
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
     * SEARCH PRODUCTS (by title or keyword) - Only show available tickets
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
                 WHERE (p.product_title LIKE ? OR p.product_keywords LIKE ?)
                   AND p.ticket_quantity > 0";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $data;
    }

    /** ----------------------------------------------------------------
     * FILTER BY CATEGORY - Only show available tickets
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
                 WHERE p.product_cat = ?
                   AND p.ticket_quantity > 0";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cat_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $data;
    }

    /** ----------------------------------------------------------------
     * FILTER BY BRAND - Only show available tickets
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
                 WHERE p.product_brand = ?
                   AND p.ticket_quantity > 0";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $brand_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $data;
    }

    /** ----------------------------------------------------------------
     * GET PRODUCTS BY ORGANIZER (for admin panel filtering)
     * ----------------------------------------------------------------
     */
    public function get_products_by_organizer($organizer_id)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "SELECT p.*, c.cat_name, b.brand_name
                  FROM products p
                  JOIN categories c ON p.product_cat = c.cat_id
                  JOIN brands b ON p.product_brand = b.brand_id
                 WHERE p.organizer_id = ?
              ORDER BY p.product_id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $organizer_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $data;
    }

    /** ----------------------------------------------------------------
     * DECREASE TICKET QUANTITY (when tickets are purchased)
     * ----------------------------------------------------------------
     */
    public function decrease_ticket_quantity($product_id, $quantity)
    {
        $conn = $this->db_conn();
        if (!$conn) return false;

        // First check if there are enough tickets
        $check_sql = "SELECT ticket_quantity FROM products WHERE product_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $product_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result()->fetch_assoc();
        $check_stmt->close();

        if (!$result || $result['ticket_quantity'] < $quantity) {
            $conn->close();
            return false; // Not enough tickets
        }

        // Decrease the quantity
        $sql = "UPDATE products SET ticket_quantity = ticket_quantity - ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $product_id);
        $ok = $stmt->execute();
        $stmt->close();
        $conn->close();
        return $ok;
    }
}
?>
