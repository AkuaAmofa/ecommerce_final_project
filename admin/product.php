<?php
// admin/product.php
include_once '../settings/core.php';
include_once '../controllers/category_controller.php';
include_once '../controllers/brand_controller.php';
include_once '../controllers/product_controller.php';

// Check login and admin authorization
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit();
}

// Retrieve existing categories, brands, and products
$categories = get_all_categories_ctr();
$brands     = get_all_brands_ctr();
$products   = get_all_products_ctr();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Management - EventLink Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    h2, h3, h4, h5 {
      color: var(--el-navy);
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
          <span>📊</span> Overview
        </a>
        <a href="category.php" class="sidebar-item">
          <span>🗂️</span> Manage Categories
        </a>
        <a href="brand.php" class="sidebar-item">
          <span>🏷️</span> Manage Brands
        </a>
        <a href="product.php" class="sidebar-item active">
          <span>📅</span> Manage Events
        </a>
        <a href="analytics.php" class="sidebar-item">
          <span>📈</span> Analytics
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10">
      <h2 style="color: var(--el-navy); font-weight: 700; margin-bottom: 24px;">Event Management</h2>

  <!-- Add / Edit Product Form -->
  <div class="card p-4 shadow-sm mb-5">
    <form id="productForm" enctype="multipart/form-data">
      <input type="hidden" name="product_id" id="product_id">

      <div class="row mb-3">
        <div class="col">
          <label for="product_cat" class="form-label">Category</label>
          <select id="product_cat" name="product_cat" class="form-select" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
              <option value="<?= $cat['cat_id']; ?>"><?= htmlspecialchars($cat['cat_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col">
          <label for="product_brand" class="form-label">Brand</label>
          <select id="product_brand" name="product_brand" class="form-select" required>
            <option value="">Select Brand</option>
            <?php foreach ($brands as $brand): ?>
              <option value="<?= $brand['brand_id']; ?>"><?= htmlspecialchars($brand['brand_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="mb-3">
        <label for="product_title" class="form-label">Product Title</label>
        <input type="text" id="product_title" name="product_title" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="product_price" class="form-label">Price (GHS)</label>
        <input type="number" step="0.01" id="product_price" name="product_price" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="product_desc" class="form-label">Description</label>
        <textarea id="product_desc" name="product_desc" rows="3" class="form-control"></textarea>
      </div>

      <div class="mb-3">
        <label for="product_keywords" class="form-label">Keywords</label>
        <input type="text" id="product_keywords" name="product_keywords" class="form-control" placeholder="e.g. organic, fertilizer, poultry feed">
      </div>

      <div class="mb-3">
        <label for="product_location" class="form-label">Event Location</label>
        <input type="text" id="product_location" name="product_location" class="form-control" placeholder="e.g. Accra International Conference Centre">
      </div>

      <div class="row mb-3">
        <div class="col">
          <label for="event_date" class="form-label">Event Date</label>
          <input type="date" id="event_date" name="event_date" class="form-control">
        </div>
        <div class="col">
          <label for="event_time" class="form-label">Event Time</label>
          <input type="time" id="event_time" name="event_time" class="form-control">
        </div>
      </div>

      <div class="mb-3">
        <label for="product_image" class="form-label">Product Image</label>
        <input type="file" id="product_image" name="product_image" class="form-control">
        <small class="text-muted">Allowed formats: JPG, PNG, GIF, WEBP.</small>
      </div>

      <button type="submit" class="btn btn-primary w-100" id="submitBtn">Add Product</button>
    </form>
  </div>

  <!-- Product List -->
  <h4>Existing Products</h4>
  <div class="table-responsive">
    <table class="table table-bordered table-hover bg-white align-middle">
      <thead class="table-primary text-center">
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>Category</th>
          <th>Brand</th>
          <th>Price (GHS)</th>
          <th>Image</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="productTableBody">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $p): ?>
            <tr>
              <td><?= $p['product_id']; ?></td>
              <td><?= htmlspecialchars($p['product_title']); ?></td>
              <td><?= htmlspecialchars($p['cat_name']); ?></td>
              <td><?= htmlspecialchars($p['brand_name']); ?></td>
              <td><?= number_format($p['product_price'], 2); ?></td>
              <td class="text-center">
                <?php if (!empty($p['product_image'])): ?>
                  <img src="../uploads/<?= htmlspecialchars($p['product_image']); ?>" alt="Product" style="width:60px;height:60px;object-fit:cover;">
                <?php else: ?>
                  <span class="text-muted">No image</span>
                <?php endif; ?>
              </td>
              <td class="text-center">
                <button class="btn btn-sm btn-warning edit-btn"
                        data-id="<?= $p['product_id']; ?>"
                        data-title="<?= htmlspecialchars($p['product_title']); ?>"
                        data-price="<?= $p['product_price']; ?>"
                        data-desc="<?= htmlspecialchars($p['product_desc']); ?>"
                        data-keywords="<?= htmlspecialchars($p['product_keywords']); ?>"
                        data-cat="<?= $p['product_cat']; ?>"
                        data-brand="<?= $p['product_brand']; ?>"
                        data-location="<?= htmlspecialchars($p['product_location'] ?? ''); ?>"
                        data-date="<?= htmlspecialchars($p['event_date'] ?? ''); ?>"
                        data-time="<?= htmlspecialchars($p['event_time'] ?? ''); ?>">
                        Edit
                </button>

                <button class="btn btn-sm btn-danger delete-btn"
                        data-id="<?= $p['product_id']; ?>">
                        Delete
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="7" class="text-center text-muted">No products found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
    </div>
  </div>
</div>

<script src="../js/product.js"></script>
</body>
</html>
