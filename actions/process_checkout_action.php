<?php
// actions/process_checkout_action.php
header('Content-Type: application/json');

require_once dirname(__DIR__) . '/settings/core.php';
require_once dirname(__DIR__) . '/controllers/cart_controller.php';
require_once dirname(__DIR__) . '/controllers/order_controller.php';

try {
    // Logged in → use user_id; guest → null (orders.customer_id allows NULL)
    $customer_id = isLoggedIn() ? (int)$_SESSION['user_id'] : null;
    $ip_add      = $customer_id ? null : get_client_ip();

    // Get cart items using your unified getter
    $cartItems = cart_items_ctr($customer_id, $ip_add);
    if (!$cartItems || count($cartItems) === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Your cart is empty']); exit;
    }

    // Compute total from items (we already joined product_price)
    $total = 0.00;
    foreach ($cartItems as $item) {
        $price = (float)($item['product_price'] ?? 0);
        $qty   = (int)($item['qty'] ?? 1);
        $total += ($price * $qty);
    }

    $invoice_no = 'INV-' . strtoupper(bin2hex(random_bytes(4)));

    // Create order with customer_id column
    $order_id = create_order_ctr($customer_id, $invoice_no, 'Paid'); 
    if (!$order_id) throw new Exception('Order creation failed');

    // Insert orderdetails
    foreach ($cartItems as $line) {
        $pid   = (int)$line['p_id'];
        $qty   = (int)$line['qty'];
        $price = (float)$line['product_price'];
        if (!add_order_details_ctr($order_id, $pid, $qty, $price)) {
            throw new Exception('Failed to add order detail');
        }
    }

    // Record payment (payments.customer_id)
    if (!record_payment_ctr($order_id, $customer_id, $total, 'GHS')) {
        throw new Exception('Payment record failed');
    }

    // Clear cart (by customer or by ip)
    if (!cart_clear_ctr($customer_id, $ip_add)) {
        throw new Exception('Could not clear cart after checkout');
    }

    echo json_encode([
        'status'   => 'success',
        'order_id' => $order_id,
        'invoice'  => $invoice_no,
        'amount'   => number_format($total, 2, '.', ''),
        'message'  => 'Payment confirmed. Order created successfully.'
    ]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
