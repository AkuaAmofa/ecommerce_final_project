<?php
declare(strict_types=1);

// Always return clean JSON
if (ob_get_level()) { ob_end_clean(); }
header('Content-Type: application/json; charset=utf-8');

try {
    require_once __DIR__ . '/../settings/core.php';
    require_once __DIR__ . '/../controllers/cart_controller.php';

    // Provide local fallbacks if your core.php doesn't define these
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

    // Validate input
    $p_id = isset($_POST['p_id']) ? (int)$_POST['p_id'] : 0;
    $qty  = isset($_POST['qty'])  ? (int)$_POST['qty']  : 1;
    if ($p_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']); exit;
    }
    $qty = max(1, $qty);

    // User context (logged-in vs guest)
    $c_id = current_user_id();
    $ip   = $c_id ? null : get_client_ip();

    // Do the add
    $ok = cart_add_ctr($p_id, $qty, $c_id, $ip);

    echo json_encode($ok
        ? ['status' => 'success']
        : ['status' => 'error', 'message' => 'Could not add to cart']
    );
} catch (Throwable $e) {
    // Never leak PHP warnings/notices; return controlled JSON
    echo json_encode(['status' => 'error', 'message' => 'Server exception']);
}
exit;
?>