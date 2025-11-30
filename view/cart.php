<?php /* view/cart.php */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart | EventLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/cart.css">
</head>
<body class="bg-light">

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

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-between" id="mainNav">
      <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link el-nav-link" href="../index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link el-nav-link" href="all_product.php">Events</a>
        </li>
      </ul>

      <ul class="navbar-nav mb-2 mb-lg-0">
        <li class="nav-item">
          <a href="cart.php" class="nav-link el-nav-link active fw-semibold">Cart</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

  <div class="container py-4">
    <div class="cart-header d-flex justify-content-between align-items-center mb-3">
      <h3 class="mb-0">Your Cart</h3>
      <div>
        <a href="all_product.php" class="btn btn-outline-secondary">Continue Shopping</a>
        <a href="checkout.php" class="btn btn-primary ms-2" id="goCheckout">Proceed to Checkout</a>
        <button id="btn-empty" class="btn btn-outline-danger ms-2">Empty Cart</button>
      </div>
    </div>

    <!-- flash area -->
    <div id="flash" class="alert alert-success py-2 d-none" role="alert">Item added to cart.</div>

    <div class="card shadow-sm">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th style="width:55%">Product</th>
                <th style="width:10%">Price</th>
                <th style="width:15%">Qty</th>
                <th style="width:10%">Subtotal</th>
                <th style="width:10%"></th>
              </tr>
            </thead>
            <tbody id="cart-body">
              <tr><td colspan="5" class="text-center py-4 text-muted">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer d-flex justify-content-between">
        <div class="text-muted" id="cart-count"></div>
        <h5 class="mb-0">Total: <span id="cart-total">GHS 0.00</span></h5>
      </div>
    </div>
  </div>

<!-- FOOTER -->
<footer class="el-footer pt-5 pb-3 mt-5">
  <div class="container">
    <div class="row mb-4">
      <div class="col-md-4 mb-3">
        <div class="d-flex align-items-center mb-3">
          <div class="el-footer-logo">E</div>
          <div class="ms-2">
            <div class="fw-semibold">EventLink</div>
            <small>Connecting Ghana's Events Digitally</small>
          </div>
        </div>
      </div>

      <div class="col-md-3 mb-3">
        <h6 class="fw-semibold mb-3">Quick Links</h6>
        <ul class="list-unstyled">
          <li><a href="../index.php">Home</a></li>
          <li><a href="all_product.php">Browse Events</a></li>
        </ul>
      </div>

      <div class="col-md-3 mb-3">
        <h6 class="fw-semibold mb-3">For Organizers</h6>
        <ul class="list-unstyled">
          <li><a href="../admin/product.php">Create Event</a></li>
          <li><a href="../admin/dashboard.php">Dashboard</a></li>
          <li><a href="../login/login.php">Login</a></li>
        </ul>
      </div>

      <div class="col-md-2 mb-3">
        <h6 class="fw-semibold mb-3">Connect</h6>
        <div class="d-flex">
          <div class="footer-icon">f</div>
          <div class="footer-icon">t</div>
          <div class="footer-icon">in</div>
        </div>
      </div>
    </div>

    <hr class="border-secondary">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-2">
      <small>© 2025 EventLink | Powered by ADF Support</small>
      <div class="mt-2 mt-md-0">
        <a href="#">Privacy Policy</a>
        <span class="mx-2">|</span>
        <a href="#">Terms of Service</a>
      </div>
    </div>
  </div>
</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // flash notice if redirected with ?added=1
    (function () {
      const params = new URLSearchParams(location.search);
      if (params.get('added') === '1') {
        const el = document.getElementById('flash');
        el.classList.remove('d-none');
        setTimeout(() => el.classList.add('d-none'), 1800);
      }
    })();

    // prevent checkout when empty
    document.getElementById('goCheckout').addEventListener('click', async (e) => {
      try {
        const res = await fetch('../actions/cart_items_action.php');
        const text = await res.text();
        const data = JSON.parse(text);
        if (!data.items || data.items.length === 0) {
          e.preventDefault();
          alert('Your cart is empty.');
        }
      } catch (_) {
        // if endpoint fails, allow default navigation so user can retry there
      }
    });
  </script>

  <script src="../js/cart.js"></script>
</body>
</html>
