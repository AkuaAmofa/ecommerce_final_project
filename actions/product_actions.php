<?php
// actions/product_actions.php
header('Content-Type: application/json');
require_once '../controllers/product_controller.php';

// helpers
function paginate_array($items, $page, $per_page) {
    $total = is_array($items) ? count($items) : 0;
    $page = max(1, (int)$page);
    $per_page = max(1, (int)$per_page);
    $offset = ($page - 1) * $per_page;
    $slice = array_slice($items ?: [], $offset, $per_page);
    return [
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'items' => array_values($slice),
    ];
}

$action   = $_GET['action'] ?? '';
$page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

switch ($action) {

    case 'all': {
        $data = view_all_products_ctr();
        echo json_encode(paginate_array($data ?: [], $page, $per_page));
        break;
    }

    case 'single': {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']); exit;
        }
        $row = view_single_product_ctr($id);
        echo json_encode($row ?: ['status' => 'error', 'message' => 'Product not found']);
        break;
    }

    case 'search': {
        $q = trim($_GET['q'] ?? '');
        if ($q === '') {
            echo json_encode(['status' => 'error', 'message' => 'Empty search query']); exit;
        }
        $data = search_products_ctr($q) ?: [];
        echo json_encode(paginate_array($data, $page, $per_page));
        break;
    }

    case 'filter_cat': {
        $cat_id = (int)($_GET['cat'] ?? 0);
        if ($cat_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid category ID']); exit;
        }
        $data = filter_products_by_category_ctr($cat_id) ?: [];
        echo json_encode(paginate_array($data, $page, $per_page));
        break;
    }

    case 'filter_brand': {
        $brand_id = (int)($_GET['brand'] ?? 0);
        if ($brand_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid brand ID']); exit;
        }
        $data = filter_products_by_brand_ctr($brand_id) ?: [];
        echo json_encode(paginate_array($data, $page, $per_page));
        break;
    }

    
    case 'search_advanced': {
        $q         = trim($_GET['q'] ?? '');
        $cat_id    = (int)($_GET['cat'] ?? 0);
        $brand_id  = (int)($_GET['brand'] ?? 0);
        $min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : null;
        $max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : null;

        
        $data = view_all_products_ctr() ?: [];

        $data = array_filter($data, function($p) use ($q, $cat_id, $brand_id, $min_price, $max_price) {
            if ($q !== '') {
                $hay = strtolower(($p['product_title'] ?? '') . ' ' . ($p['product_keywords'] ?? ''));
                if (strpos($hay, strtolower($q)) === false) return false;
            }
            if ($cat_id > 0 && (int)$p['product_cat'] !== $cat_id) return false;
            if ($brand_id > 0 && (int)$p['product_brand'] !== $brand_id) return false;

            $price = (float)$p['product_price'];
            if ($min_price !== null && $price < $min_price) return false;
            if ($max_price !== null && $price > $max_price) return false;

            return true;
        });

        $data = array_values($data);
        echo json_encode(paginate_array($data, $page, $per_page));
        break;
    }

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}
exit;
?>