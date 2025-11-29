<?php
require_once '../settings/core.php';
require_login('../login/login.php');

// Check if cart is not empty
require_once '../controllers/cart_controller.php';
$customer_id = get_user_id();
$client_ip = get_client_ip();
$cart_items = cart_items_ctr($customer_id, $client_ip);

if (!$cart_items || count($cart_items) == 0) {
    header('Location: cart.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - EventLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .checkout-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .page-header {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
            margin-bottom: 30px;
            border-top: 4px solid var(--el-gold);
        }

        .page-title {
            color: var(--el-navy);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .checkout-section {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
            margin-bottom: 20px;
        }

        .section-title {
            color: var(--el-navy);
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f8f9fa;
        }

        .checkout-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            border-bottom: 1px solid #f8f9fa;
            align-items: center;
            justify-content: space-between;
        }

        .checkout-item:last-child {
            border-bottom: none;
        }

        .item-details {
            flex: 1;
        }

        .item-title {
            font-weight: 600;
            color: var(--el-navy);
            margin-bottom: 5px;
        }

        .item-info {
            font-size: 14px;
            color: #6c757d;
        }

        .item-price {
            font-weight: 700;
            color: var(--el-gold);
            font-size: 1.2rem;
        }

        .summary-total {
            font-size: 2rem;
            font-weight: 700;
            color: var(--el-navy);
            padding: 25px 0;
            text-align: center;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            margin: 20px 0;
            border: 2px solid var(--el-gold);
        }

        .summary-total-amount {
            color: var(--el-gold);
            font-size: 2.5rem;
        }

        .btn-checkout {
            width: 100%;
            padding: 18px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            background: linear-gradient(135deg, var(--el-gold), #f4d03f);
            color: white;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .modal-content {
            background: white;
            max-width: 500px;
            width: 90%;
            padding: 40px;
            border-radius: 20px;
            position: relative;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-content::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--el-gold), #f4d03f);
            border-radius: 20px 20px 0 0;
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            cursor: pointer;
            color: #6c757d;
            background: none;
            border: none;
        }

        .modal-close:hover {
            color: var(--el-navy);
        }

        .modal-title {
            font-size: 1.8rem;
            color: var(--el-navy);
            margin-bottom: 20px;
            text-align: center;
            font-weight: 700;
        }

        .payment-amount-display {
            text-align: center;
            margin: 30px 0;
        }

        .payment-amount-label {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
        }

        .payment-amount {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--el-gold);
        }

        .paystack-badge {
            background: linear-gradient(135deg, var(--el-navy), var(--el-dark-navy));
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(43, 58, 103, 0.2);
        }

        .badge-label {
            font-size: 11px;
            margin-bottom: 10px;
            opacity: 0.8;
            letter-spacing: 1px;
        }

        .badge-title {
            font-size: 1.2rem;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .badge-description {
            font-size: 12px;
            opacity: 0.8;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .modal-buttons button {
            flex: 1;
            padding: 14px;
            border-radius: 50px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-modal-cancel {
            background: white;
            color: var(--el-navy);
            border: 2px solid var(--el-navy);
        }

        .btn-modal-cancel:hover {
            background: var(--el-navy);
            color: white;
        }

        .btn-modal-confirm {
            background: linear-gradient(135deg, var(--el-gold), #f4d03f);
            color: white;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-modal-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }
    </style>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg bg-white shadow-sm el-navbar">
        <div class="container py-2">
            <a class="navbar-brand d-flex align-items-center gap-2" href="../index.php">
                <div class="el-footer-logo" style="border-radius:50%;">E</div>
                <div class="d-flex flex-column lh-1">
                    <span class="fw-bold el-navy">EventLink</span>
                    <small class="text-muted">Connecting Ghana's Events Digitally</small>
                </div>
            </a>
            <div style="display: flex; gap: 20px;">
                <a href="cart.php" class="btn btn-outline-secondary">← Back to Cart</a>
            </div>
        </div>
    </nav>

    <div class="checkout-container">
        <div class="page-header">
            <h1 class="page-title">Checkout</h1>
            <p style="color: #6c757d; font-size: 1.1rem;">Review your order and complete payment</p>
        </div>

        <div class="checkout-section">
            <h2 class="section-title">Order Summary</h2>
            <div id="checkoutItemsContainer"></div>

            <div class="summary-total">
                Total: <span class="summary-total-amount" id="checkoutTotal">GHS 0.00</span>
            </div>

            <button onclick="showPaymentModal()" class="btn-checkout">💳 Proceed to Secure Payment</button>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closePaymentModal()">&times;</button>
            <h2 class="modal-title">Secure Payment via Paystack</h2>

            <div class="payment-amount-display">
                <div class="payment-amount-label">Amount to Pay</div>
                <div class="payment-amount" id="paymentAmount"></div>
            </div>

            <div class="paystack-badge">
                <div class="badge-label">SECURED PAYMENT</div>
                <div class="badge-title"> Powered by Paystack</div>
                <div class="badge-description">Your payment information is 100% secure and encrypted</div>
            </div>

            <p style="text-align: center; color: #6c757d; font-size: 14px; margin-bottom: 20px;">
                You will be redirected to Paystack's secure payment gateway
            </p>

            <div class="modal-buttons">
                <button onclick="closePaymentModal()" class="btn-modal-cancel">Cancel</button>
                <button onclick="processCheckout()" id="confirmPaymentBtn" class="btn-modal-confirm">💳 Pay Now</button>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer class="el-footer pt-5 pb-3 mt-5" style="background: var(--el-navy); color: white;">
        <div class="container">
            <div class="text-center">
                <small>© 2025 EventLink | Powered by ADF Support</small>
            </div>
        </div>
    </footer>

    <script src="../js/checkout.js?v=2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
