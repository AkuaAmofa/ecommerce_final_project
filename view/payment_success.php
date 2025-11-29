<?php
require_once '../settings/core.php';
require_once '../controllers/order_controller.php';

require_login('../login/login.php');

$customer_id = get_user_id();
$invoice_no = isset($_GET['invoice']) ? htmlspecialchars($_GET['invoice']) : '';
$reference = isset($_GET['reference']) ? htmlspecialchars($_GET['reference']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - EventLink</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); }

        .navbar { background: white; padding: 20px 0; box-shadow: 0 4px 30px rgba(43, 58, 103, 0.08); }
        .nav-container { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 40px; }
        .logo { font-family: 'Inter', sans-serif; font-size: 24px; background: linear-gradient(135deg, var(--el-gold), #f4d03f); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-decoration: none; font-weight: 700; }
        
        .container { max-width: 900px; margin: 60px auto; padding: 0 20px; }
        
        .success-box {
            background: white;
            border: 2px solid var(--el-gold);
            border-radius: 20px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(43, 58, 103, 0.1);
        }
        
        .success-icon { 
            font-size: 80px; 
            margin-bottom: 20px; 
            animation: bounce 1s ease-in-out;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        h1 {
            font-family: 'Inter', sans-serif;
            font-size: 3rem;
            color: var(--el-navy);
            margin-bottom: 10px;
            font-weight: 700;
        }

        .subtitle {
            font-size: 18px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        .order-details { 
            background: white; 
            padding: 30px; 
            border-radius: 12px; 
            margin: 30px 0; 
            text-align: left;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .detail-row { 
            display: flex; 
            justify-content: space-between; 
            padding: 12px 0; 
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
        }
        
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; }
        .detail-value { color: #6b7280; word-break: break-all; }
        
        .btn { 
            padding: 16px 40px; 
            border: none; 
            border-radius: 50px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.4s ease; 
            text-decoration: none; 
            display: inline-block;
            margin: 0 10px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--el-gold), #f4d03f);
            color: white;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(212, 175, 55, 0.4);
        }
        
        .btn-secondary {
            background: white;
            color: var(--el-navy);
            border: 2px solid var(--el-navy);
        }

        .btn-secondary:hover {
            background: var(--el-navy);
            color: white;
        }

        .buttons-container {
            display: flex;
            justify-content: center;
            margin-top: 40px;
            flex-wrap: wrap;
        }

        .confirmation-message {
            background: linear-gradient(135deg, #fff3cd, #ffeeba);
            border: 2px solid var(--el-gold);
            padding: 20px;
            border-radius: 12px;
            color: var(--el-navy);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="../index.php" class="logo">EventLink</a>
            <div style="display: flex; gap: 20px;">
                <a href="all_product.php" style="color: var(--el-navy); text-decoration: none; font-weight: 600;">← Continue Shopping</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="success-box">
            <div class="success-icon">🎉</div>
            <h1>Order Successful!</h1>
            <p class="subtitle">Your payment has been processed successfully</p>
            
            <div class="confirmation-message">
                <strong>✓ Payment Confirmed</strong><br>
                Thank you for your purchase! Your order has been confirmed and will be processed shortly.
            </div>
            
            <div class="order-details">
                <div class="detail-row">
                    <span class="detail-label">Invoice Number</span>
                    <span class="detail-value"><?php echo $invoice_no; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Reference</span>
                    <span class="detail-value"><?php echo $reference; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Date</span>
                    <span class="detail-value"><?php echo date('F j, Y'); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value" style="color: #059669; font-weight: 600;">Paid ✓</span>
                </div>
            </div>
            
            <div class="buttons-container">
                <a href="orders.php" class="btn btn-primary">📦 View My Orders</a>
                <a href="all_product.php" class="btn btn-secondary">Continue Shopping</a>
            </div>
        </div>
    </div>

    <script>
        // Confetti effect
        function createConfetti() {
            const colors = ['#D4AF37', '#f4d03f', '#2B3A67', '#20c997', '#ffc107'];
            const confettiCount = 50;
            
            for (let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.cssText = `
                        position: fixed;
                        width: 10px;
                        height: 10px;
                        background: ${colors[Math.floor(Math.random() * colors.length)]};
                        left: ${Math.random() * 100}%;
                        top: -10px;
                        opacity: 1;
                        transform: rotate(${Math.random() * 360}deg);
                        z-index: 10001;
                        pointer-events: none;
                    `;
                    
                    document.body.appendChild(confetti);
                    
                    const duration = 2000 + Math.random() * 1000;
                    const startTime = Date.now();
                    
                    function animateConfetti() {
                        const elapsed = Date.now() - startTime;
                        const progress = elapsed / duration;
                        
                        if (progress < 1) {
                            const top = progress * (window.innerHeight + 50);
                            const wobble = Math.sin(progress * 10) * 50;
                            
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
                }, i * 30);
            }
        }
        
        // Trigger confetti on page load
        window.addEventListener('load', createConfetti);
    </script>
</body>
</html>
