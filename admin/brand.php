<?php
require_once '../settings/core.php';

if (!isLoggedIn()) {
    header("Location: ../login/login.php");
    exit();
}
if (!isAdmin()) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Brand Management - EventLink Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      min-height: 100vh;
    }
    .admin-nav {
      background: white;
      box-shadow: 0 2px 10px rgba(43, 58, 103, 0.08);
    }
    .logo-circle {
      width: 48px;
      height: 48px;
      background: linear-gradient(135deg, var(--el-gold), #f4d03f);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: 700;
      font-size: 24px;
    }
    .sidebar {
      background: var(--el-navy);
      min-height: calc(100vh - 76px);
      padding: 0;
      border-radius: 16px;
    }
    .sidebar-item {
      padding: 16px 24px;
      color: white;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 12px;
      transition: all 0.3s;
      border-left: 4px solid transparent;
    }
    .sidebar-item:hover {
      background: rgba(255, 255, 255, 0.1);
      color: white;
    }
    .sidebar-item.active {
      background: var(--el-gold);
      border-left-color: #f4d03f;
      color: white;
    }
    .btn-primary {
      background: linear-gradient(135deg, var(--el-gold), #f4d03f);
      border: none;
      color: white;
      font-weight: 600;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #f4d03f, var(--el-gold));
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
    }
    .table {
      background: white;
    }
    .table thead {
      background: var(--el-navy);
      color: white;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--el-gold);
      box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
    }
    .card {
      border: none;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
    }
    .card-title {
      color: var(--el-navy);
      font-weight: 600;
    }
  </style>
</head>
<body>
<!-- Top Navigation -->
<nav class="admin-nav py-3 mb-4">
  <div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <div class="logo-circle">E</div>
        <div>
          <h5 class="mb-0" style="color: var(--el-navy); font-weight: 700;">EventLink</h5>
          <small class="text-muted">Connecting Ghana's Events Digitally</small>
        </div>
      </div>
      <div class="d-flex gap-3">
        <a href="../index.php" class="btn btn-outline-secondary">Home</a>
        <a href="../view/all_product.php" class="btn btn-outline-secondary">Events</a>
        <a href="../login/login.php" class="btn btn-outline-secondary">Login</a>
        <a href="../login/logout.php" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </div>
</nav>

<div class="container-fluid px-4">
  <div class="row">
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 mb-4">
      <div class="sidebar">
        <div class="p-3">
          <h6 style="color: var(--el-gold); font-weight: 600; margin-bottom: 20px;">Organizer Panel</h6>
        </div>
        <a href="dashboard.php" class="sidebar-item">
          <span></span> Overview
        </a>
        <a href="category.php" class="sidebar-item">
          <span></span> Manage Categories
        </a>
        <a href="brand.php" class="sidebar-item active">
          <span></span> Manage Brands
        </a>
        <a href="product.php" class="sidebar-item">
          <span></span> Manage Events
        </a>
        <a href="analytics.php" class="sidebar-item">
          <span></span> Analytics
        </a>
        <?php if (function_exists('isSuperAdmin') && isSuperAdmin()): ?>
        <a href="payment_requests.php" class="sidebar-item">
          <span></span> Payment Requests
        </a>
        <a href="payment_approvals.php" class="sidebar-item">
          <span></span> Payment Approvals
        </a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10">
      <h2 style="color: var(--el-navy); font-weight: 700; margin-bottom: 24px;">Brand Management</h2>

      <!-- Add Brand -->
      <div class="card mb-4">
        <div class="card-body">
          <h5 class="card-title mb-3">Add a New Brand</h5>
      <form id="addBrandForm" class="row g-3">
        <div class="col-md-6">
          <label for="brand_name" class="form-label">Brand Name</label>
          <input type="text" id="brand_name" name="brand_name" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label for="brand_cat" class="form-label">Category</label>
          <select id="brand_cat" name="brand_cat" class="form-select" required>
            <!-- populated by JS -->
          </select>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary w-100">Add Brand</button>
        </div>
      </form>
    </div>
  </div>

      <!-- Brands Table -->
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3">Existing Brands</h5>
          <table class="table table-striped align-middle" id="brandTable">
            <thead>
              <tr>
                <th style="width: 90px">ID</th>
                <th>Brand</th>
                <th>Category</th>
                <th style="width: 130px">Actions</th>
              </tr>
            </thead>
            <tbody><!-- rows via JS --></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editBrandForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" style="color: var(--el-navy);">Edit Brand</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edit_brand_id" name="brand_id">
        <div class="mb-3">
          <label for="edit_brand_name" class="form-label">Brand Name</label>
          <input type="text" id="edit_brand_name" name="brand_name" class="form-control" required>
        </div>
        <p class="text-muted mb-0">Note: Category is not editable for this lab.</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../js/brand.js"></script>
</body>
</html>
