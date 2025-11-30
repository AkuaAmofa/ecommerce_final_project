<?php
// classes/category_class.php
require_once dirname(__DIR__) . '/settings/db_class.php';


class Category extends db_connection
{
    /**
     * Add new category
     * @param string $name
     * @param int $organizer_id
     * @return bool
     */
    public function addCategory($name, $organizer_id = null)
    {
        if ($this->db === null) {
            $this->db_connect();
        }

        $name = mysqli_real_escape_string($this->db, $name);
        $organizer_id = (int)$organizer_id;

        // Ensure category name is unique FOR THIS ORGANIZER
        $checkSql = "SELECT * FROM categories WHERE cat_name = '$name' AND organizer_id = $organizer_id LIMIT 1";
        $exists = $this->db_fetch_one($checkSql);

        if ($exists) {
            return false; // Category already exists for this organizer
        }

        $sql = "INSERT INTO categories (cat_name, organizer_id) VALUES ('$name', $organizer_id)";
        return $this->db_write_query($sql);
    }

    /**
     * Update category by ID
     * @param int $id
     * @param string $name
     * @return bool
     */
    public function updateCategory($id, $name)
    {
        if ($this->db === null) {
            $this->db_connect();
        }

        $id   = (int)$id;
        $name = mysqli_real_escape_string($this->db, $name);

        $sql = "UPDATE categories SET cat_name = '$name' WHERE cat_id = $id";
        return $this->db_write_query($sql);
    }

    /**
     * Delete category by ID
     * @param int $id
     * @return bool
     */
    public function deleteCategory($id)
    {
        if ($this->db === null) {
            $this->db_connect();
        }

        $id  = (int)$id;
        $sql = "DELETE FROM categories WHERE cat_id = $id";
        return $this->db_write_query($sql);
    }

    /**
     * Get single category by ID
     * @param int $id
     * @return array|false
     */
    public function getCategory($id)
    {
        if ($this->db === null) {
            $this->db_connect();
        }

        $id  = (int)$id;
        $sql = "SELECT * FROM categories WHERE cat_id = $id LIMIT 1";
        return $this->db_fetch_one($sql);
    }

    /**
     * Get all categories
     * @return array|false
     */
    public function getAllCategories()
    {
        if ($this->db === null) {
            $this->db_connect();
        }

        $sql = "SELECT * FROM categories ORDER BY cat_id DESC";
        return $this->db_fetch_all($sql);
    }

    /**
     * Get categories by organizer
     * @param int $organizer_id
     * @return array|false
     */
    public function getCategoriesByOrganizer($organizer_id)
    {
        if ($this->db === null) {
            $this->db_connect();
        }

        $organizer_id = (int)$organizer_id;
        $sql = "SELECT * FROM categories WHERE organizer_id = $organizer_id ORDER BY cat_id DESC";
        return $this->db_fetch_all($sql);
    }
}
?>
