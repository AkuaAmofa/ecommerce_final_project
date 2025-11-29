<?php
require_once '../settings/core.php';
require_login('../login/login.php');

// Get order details from URL parameters
$invoice_no = isset($_GET['invoice']) ? htmlspecialchars($_GET['invoice']) : '';
$reference = isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : '';
$amount = isset($_GET['amount']) ? htmlspecialchars($_GET['amount']) : '0.00';
$order_date = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : date('F j, Y');
$item_count = isset($_GET['items']) ? htmlspecialchars($_GET['items']) : '0';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed - EventLink</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }

        .success-container {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .success-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(43, 58, 103, 0.1);
            max-width: 600px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: checkmark 0.6s ease-in-out;
        }

        @keyframes checkmark {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        .success-icon svg {
            width: 50px;
            height: 50px;
            stroke: #28a745;
            stroke-width: 3;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .checkmark-path {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: drawCheck 0.8s ease-out 0.3s forwards;
        }

        @keyframes drawCheck {
            to { stroke-dashoffset: 0; }
        }

        .success-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .success-subtitle {
            font-size: 1.1rem;
            opacity: 0.95;
        }

        .order-details {
            padding: 30px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #6c757d;
            font-weight: 500;
        }

        .detail-value {
            color: var(--el-navy);
            font-weight: 600;
        }

        .invoice-highlight {
            background: linear-gradient(135deg, var(--el-gold), #f4d03f);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            text-align: center;
        }

        .invoice-number {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .action-buttons {
            padding: 30px;
            background: #f8f9fa;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-action {
            flex: 1;
            min-width: 200px;
            padding: 14px 24px;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            font-size: 1rem;
        }

        .btn-primary-gold {
            background: linear-gradient(135deg, var(--el-gold), #f4d03f);
            color: white;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }

        .btn-primary-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            color: white;
        }

        .btn-secondary-outline {
            background: white;
            color: var(--el-navy);
            border: 2px solid var(--el-navy);
        }

        .btn-secondary-outline:hover {
            background: var(--el-navy);
            color: white;
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            pointer-events: none;
            z-index: 9999;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
        }

        .info-box-text {
            color: #084298;
            font-size: 0.95rem;
            margin: 0;
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
        </div>
    </nav>

    <div class="success-container">
        <div class="success-card">
            <!-- Success Header -->
            <div class="success-header">
                <div class="success-icon">
                    <svg viewBox="0 0 52 52">
                        <path class="checkmark-path" d="M14 27l7.5 7.5L38 18"/>
                    </svg>
                </div>
                <h1 class="success-title">Payment Successful!</h1>
                <p class="success-subtitle">Your order has been confirmed and is being processed</p>
            </div>

            <!-- Order Details -->
            <div class="order-details">
                <div class="invoice-highlight">
                    <div style="font-size: 0.9rem; margin-bottom: 5px; opacity: 0.9;">🎟️ YOUR TICKET ID</div>
                    <div class="invoice-number"><?php echo $invoice_no; ?></div>
                    <div style="font-size: 0.85rem; margin-top: 10px; opacity: 0.95;">
                        Show this ID at the event entrance
                    </div>
                </div>

                <div class="info-box" style="background: #fff3cd; border-left-color: #ffc107; margin-top: 20px;">
                    <p class="info-box-text" style="color: #856404;">
                        <strong>⚠️ IMPORTANT:</strong> Your Ticket ID is <strong><?php echo $invoice_no; ?></strong>.
                        Please present this number at the event entrance to gain entry.
                        Save this number or take a screenshot for your records.
                    </p>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Order Date</span>
                    <span class="detail-value"><?php echo $order_date; ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Total Amount Paid</span>
                    <span class="detail-value">GHS <?php echo $amount; ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Number of Items</span>
                    <span class="detail-value"><?php echo $item_count; ?> item(s)</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Payment Reference</span>
                    <span class="detail-value" style="font-size: 0.85rem; word-break: break-all;"><?php echo $reference; ?></span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">Paystack (Card/Mobile Money)</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Order Status</span>
                    <span class="detail-value text-success">✓ Paid & Confirmed</span>
                </div>

                <div class="info-box">
                    <p class="info-box-text">
                        <strong>📧 Confirmation Email:</strong> A confirmation email with your order details has been sent to your email address.
                        Please check your inbox (and spam folder if needed).
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="all_product.php" class="btn btn-action btn-secondary-outline">
                    Continue Shopping
                </a>
                <a href="../index.php" class="btn btn-action btn-primary-gold">
                    Go to Home
                </a>
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

    <script>
        // Confetti animation on page load
        function createConfetti() {
            const colors = ['#D4AF37', '#f4d03f', '#28a745', '#20c997', '#0d6efd'];
            const confettiCount = 60;

            for (let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.cssText = `
                        left: ${Math.random() * 100}%;
                        top: -10px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        opacity: ${0.6 + Math.random() * 0.4};
                        transform: rotate(${Math.random() * 360}deg);
                    `;

                    document.body.appendChild(confetti);

                    const duration = 2000 + Math.random() * 1000;
                    const startTime = Date.now();

                    function animateConfetti() {
                        const elapsed = Date.now() - startTime;
                        const progress = elapsed / duration;

                        if (progress < 1) {
                            const top = progress * (window.innerHeight + 50);
                            const wobble = Math.sin(progress * 10) * 30;

                            confetti.style.top = top + 'px';
                            confetti.style.left = `calc(${confetti.style.left} + ${wobble}px)`;
                            confetti.style.opacity = 1 - progress;
                            confetti.style.transform = `rotate(${progress * 720}deg)`;

                            requestAnimationFrame(animateConfetti);
                        } else {
                            confetti.remove();
                        }
                    }

                    animateConfetti();
                }, i * 20);
            }
        }

        // Trigger confetti on page load
        window.addEventListener('load', () => {
            setTimeout(createConfetti, 300);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
