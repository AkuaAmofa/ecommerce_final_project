<?php
session_start();
require_once '../settings/core.php';
$logged_in = isLoggedIn();
$is_admin = isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Details | EventLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
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
        <li class="nav-item">
          <a class="nav-link el-nav-link" href="about.php">About</a>
        </li>
      </ul>

      <ul class="navbar-nav mb-2 mb-lg-0">
        <?php if (!$logged_in): ?>
          <li class="nav-item me-2">
            <a href="../login/login.php" class="btn btn-link text-decoration-none el-navy fw-semibold">Login</a>
          </li>
          <li class="nav-item">
            <a href="../login/register.php" class="btn el-btn-gold px-4 rounded-3">Sign Up</a>
          </li>
        <?php else: ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle el-nav-link fw-semibold" href="#" data-bs-toggle="dropdown">
              <?= htmlspecialchars($_SESSION['name']); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item text-danger" href="../login/logout.php">Logout</a></li>
              <?php if ($is_admin): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="../admin/category.php">Manage Categories</a></li>
                <li><a class="dropdown-item" href="../admin/brand.php">Manage Brands</a></li>
                <li><a class="dropdown-item" href="../admin/product.php">Manage Events</a></li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO IMAGE -->
<div id="eventHero" class="event-hero-section">
  <div class="event-hero-overlay"></div>
</div>

<!-- EVENT DETAILS -->
<div class="container event-details-container pb-5">
  <a href="all_product.php" class="btn btn-back">← Back to Events</a>
  
  <div class="row g-4">
    <!-- LEFT COLUMN: Event Details -->
    <div class="col-lg-8">
      <div class="event-details-card">
        <div id="eventContent">
          <p class="text-center text-muted">Loading event details...</p>
        </div>
      </div>
    </div>

    <!-- RIGHT SIDEBAR: Price & Actions -->
    <div class="col-lg-4">
      <div id="eventSidebar">
        <!-- Price card will be injected here -->
      </div>
    </div>
  </div>

  <!-- SIMILAR EVENTS -->
  <div class="similar-events-section">
    <h5>Similar Events</h5>
    <div id="similarEvents" class="row g-4">
      <!-- Similar events will be loaded here -->
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
          <li><a href="about.php">About Us</a></li>
        </ul>
      </div>

      <div class="col-md-3 mb-3">
        <h6 class="fw-semibold mb-3">For Organizers</h6>
        <ul class="list-unstyled">
          <li>Create Event</li>
          <li>Dashboard</li>
          <li>Pricing</li>
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="../js/event-details.js"></script>
</body>
</html>