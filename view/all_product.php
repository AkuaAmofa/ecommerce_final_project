<?php
session_start();
require_once '../settings/core.php';
require_once '../controllers/category_controller.php';
require_once '../controllers/brand_controller.php';

$logged_in  = isLoggedIn();
$is_admin   = isAdmin();
$categories = get_all_categories_ctr();
$brands     = get_all_brands_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Discover Events | EventLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/events.css">
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
          <a class="nav-link el-nav-link active" href="all_product.php">Events</a>
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

<!-- PAGE HEADER -->
<section class="events-page-header">
  <div class="container">
    <h2>Discover Events</h2>
    <p>Find the perfect event for you</p>
  </div>
</section>

<!-- MAIN CONTENT -->
<div class="container pb-5">
  
  <!-- FILTERS -->
  <div class="events-filter-section">
    <div class="row g-3 align-items-center">
      <div class="col-md-6">
        <input type="text" id="searchBox" class="form-control events-search-input" placeholder="Search events...">
      </div>
      <div class="col-md-3">
        <select id="filterCategory" class="form-select">
          <option value="">All Categories</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['cat_id'] ?>"><?= htmlspecialchars($cat['cat_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <select id="filterBrand" class="form-select">
          <option value="">All Locations</option>
          <?php foreach ($brands as $brand): ?>
            <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <!-- RESULTS BAR -->
  <div class="events-results-bar">
    <div class="results-count" id="resultsCount">Loading...</div>
    <a href="#" class="clear-filters" id="clearFilters">Clear Filters</a>
  </div>

  <!-- EVENTS GRID -->
  <div id="productGrid" class="row g-4"></div>

  <!-- PAGINATION -->
  <div class="events-pagination">
    <button id="prevPage">← Previous</button>
    <div class="page-info" id="pageInfo"></div>
    <button id="nextPage">Next →</button>
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
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="../js/frontend_product.js"></script>
</body>
</html>