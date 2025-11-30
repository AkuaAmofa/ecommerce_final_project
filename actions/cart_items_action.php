<?php
declare(strict_types=1);

if (ob_get_level()) { ob_end_clean(); }
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../settings/core.php';
    require_once __DIR__ . '/../controllers/cart_controller.php';

    // Fallback helpers 
    if (!function_exists('current_user_id')) {
        function current_user_id() {
            return (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id']))
                ? (int)$_SESSION['user_id'] : null;
        }
    }
    if (!function_exists('get_client_ip')) {
        function get_client_ip() {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }

    $c_id = current_user_id();
    $ip   = $c_id ? null : get_client_ip();

    $items = cart_items_ctr($c_id, $ip);
    if (!is_array($items)) { $items = []; }

    // Subtotal and total quantity
    $subtotal = 0.0;
    $totalQty = 0;
    foreach ($items as &$it) {
        $price = (float)($it['product_price'] ?? 0);
        $qty   = (int)($it['qty'] ?? 0);
        $itemSubtotal = $price * $qty;
        $it['subtotal'] = $itemSubtotal; // Add subtotal to each item
        $subtotal += $itemSubtotal;
        $totalQty += $qty;
    }
    unset($it); // Break reference

    echo json_encode([
        'status' => 'success',
        'items'  => $items,
        'totals' => [
            'count'     => $totalQty,       // total units, not just rows
            'subtotal'  => $subtotal
        ]
    ]);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server exception']);
}
exit;
?>