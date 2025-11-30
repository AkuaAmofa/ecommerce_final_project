<?php
require_once dirname(__DIR__) . '/settings/db_class.php';


class Brand extends db_connection
{
    public function __construct() {
        $this->db_connect();
    }

    // CREATE
    public function addBrand($name, $cat_id, $organizer_id = null) {
        $name = mysqli_real_escape_string($this->db, trim($name));
        $cat  = (int)$cat_id;
        $organizer_id = (int)$organizer_id;

        // enforce uniqueness at code-level too (for this organizer)
        $check = $this->db_fetch_one(
            "SELECT brand_id FROM brands WHERE brand_cat = $cat AND brand_name = '$name' AND organizer_id = $organizer_id LIMIT 1"
        );
        if ($check) return ['ok'=>false,'msg'=>'Brand already exists in this category'];

        $sql = "INSERT INTO brands (brand_cat, brand_name, organizer_id) VALUES ($cat, '$name', $organizer_id)";
        return $this->db_write_query($sql)
            ? ['ok'=>true,'id'=>$this->last_insert_id()]
            : ['ok'=>false,'msg'=>'DB insert failed'];
    }

    // RETRIEVE (flat with category name, ordered by category then name)
    public function getAllBrandsWithCategory() {
        $sql = "SELECT b.brand_id, b.brand_name, b.brand_cat, c.cat_name
                FROM brands b
                LEFT JOIN categories c ON c.cat_id = b.brand_cat
                ORDER BY c.cat_name IS NULL, c.cat_name, b.brand_name";
        return $this->db_fetch_all($sql) ?: [];
    }

    // RETRIEVE brands by organizer
    public function getBrandsByOrganizer($organizer_id) {
        $organizer_id = (int)$organizer_id;
        $sql = "SELECT b.brand_id, b.brand_name, b.brand_cat, c.cat_name
                FROM brands b
                LEFT JOIN categories c ON c.cat_id = b.brand_cat
                WHERE b.organizer_id = $organizer_id
                ORDER BY c.cat_name IS NULL, c.cat_name, b.brand_name";
        return $this->db_fetch_all($sql) ?: [];
    }

    // UPDATE (name only, per spec)
    public function updateBrand($id, $new_name) {
        $id   = (int)$id;
        $name = mysqli_real_escape_string($this->db, trim($new_name));

        // get current category to re-check uniqueness
        $curr = $this->db_fetch_one("SELECT brand_cat FROM brands WHERE brand_id=$id");
        if (!$curr) return false;

        $cat  = (int)$curr['brand_cat'];
        $dup  = $this->db_fetch_one(
          "SELECT brand_id FROM brands WHERE brand_cat=$cat AND brand_name='$name' AND brand_id<>$id LIMIT 1"
        );
        if ($dup) return false;

        $sql = "UPDATE brands SET brand_name='$name' WHERE brand_id=$id";
        return $this->db_write_query($sql);
    }

    // DELETE
    public function deleteBrand($id) {
        $id = (int)$id;
        $sql = "DELETE FROM brands WHERE brand_id=$id";
        return $this->db_write_query($sql);
    }
}
