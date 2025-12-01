<?php
require_once '../settings/core.php';

// Restrict to admins only (organizers + super admins)
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit();
}

// figure out role
$user_role = $_SESSION['role'] ?? null; // Note: session stores it as 'role', not 'user_role'
$is_super  = function_exists('isSuperAdmin') && isSuperAdmin();

// Fetch dashboard statistics (currently per organizer)
require_once '../controllers/product_controller.php';
require_once '../controllers/order_controller.php';

// Get logged-in organizer ID (for super_admin this may just show their own data)
$organizer_id = $_SESSION['user_id'] ?? 0;

// Get statistics filtered by organizer
$total_tickets   = get_total_tickets_sold_by_organizer_ctr($organizer_id);
$total_revenue   = get_total_revenue_by_organizer_ctr($organizer_id);
$active_events   = get_active_events_count_by_organizer_ctr($organizer_id);

// Get recent events with actual ticket counts (for this organizer only)
$recent_events   = get_recent_events_with_tickets_by_organizer_ctr($organizer_id, 3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - EventLink Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
    .stat-card {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
      transition: transform 0.3s;
    }
    .stat-card:hover {
      transform: translateY(-4px);
    }
    .stat-icon {
      width: 56px;
      height: 56px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      margin-bottom: 16px;
    }
    .stat-value {
      font-size: 2rem;
      font-weight: 700;
      color: var(--el-navy);
      margin: 8px 0;
    }
    .stat-label {
      color: #6c757d;
      font-size: 0.9rem;
    }
    .stat-change {
      font-size: 0.85rem;
      font-weight: 600;
    }
    .stat-change.positive {
      color: #10b981;
    }
    .btn-gold {
      background: linear-gradient(135deg, var(--el-gold), #f4d03f);
      border: none;
      color: white;
      font-weight: 600;
      padding: 12px 24px;
      border-radius: 50px;
    }
    .btn-gold:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
      color: white;
    }
    .quick-action-btn {
      padding: 10px 20px;
      border-radius: 50px;
      font-weight: 600;
      transition: all 0.3s;
    }
    .event-item {
      background: white;
      border-radius: 12px;
      padding: 20px;
      margin-bottom: 12px;
      box-shadow: 0 2px 10px rgba(43, 58, 103, 0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .event-item:hover {
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.1);
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
      <h6 style="color: var(--el-gold); font-weight: 600; margin-bottom: 20px;">
        <?php echo $is_super ? 'Super Admin Panel' : 'Organizer Panel'; ?>
      </h6>
    </div>

    <?php if ($is_super): ?>
      <!-- SUPER ADMIN MENU -->
      <a href="dashboard.php" class="sidebar-item active">
        <span></span> Overview
      </a>
      <a href="payment_requests.php" class="sidebar-item">
        <span></span> Payment Requests
      </a>
      <a href="payment_approvals.php" class="sidebar-item">
        <span></span> Payment Approvals
      </a>

    <?php else: ?>
      <!-- ORGANIZER MENU (role 1, not super admin) -->
      <a href="dashboard.php" class="sidebar-item active">
        <span></span> Overview
      </a>
      <a href="category.php" class="sidebar-item">
        <span></span> Manage Categories
      </a>
      <a href="brand.php" class="sidebar-item">
        <span></span> Manage Brands
      </a>
      <a href="product.php" class="sidebar-item">
        <span></span> Manage Events
      </a>
      <a href="analytics.php" class="sidebar-item">
        <span></span> Analytics
      </a>
      <a href="payment_requests.php" class="sidebar-item">
        <span></span> Payment Requests
      </a>
    <?php endif; ?>
  </div>
</div>


    <!-- Main Content -->
    <div class="col-md-9 col-lg-10">
      <h2 style="color: var(--el-navy); font-weight: 700; margin-bottom: 24px;">Dashboard Overview</h2>

      <!-- Stats Cards -->
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <div class="stat-card">
            <div class="stat-icon" style="background: #e0f2fe; color: #0369a1;">👥</div>
            <div class="stat-value"><?php echo number_format($total_tickets); ?></div>
            <div class="stat-label">Total Tickets Sold</div>
            <div class="stat-change positive">+12%</div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #d97706;">💰</div>
            <div class="stat-value">GH₵ <?php echo number_format($total_revenue); ?></div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-change positive">+23%</div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="stat-card">
            <div class="stat-icon" style="background: #ddd6fe; color: #7c3aed;">📅</div>
            <div class="stat-value"><?php echo $active_events; ?></div>
            <div class="stat-label">Active Events</div>
            <div style="color: #10b981; font-size: 0.85rem; font-weight: 600;">Active</div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
        <div class="card-body p-4">
          <h5 style="color: var(--el-navy); font-weight: 600; margin-bottom: 20px;">Quick Actions</h5>
          <div class="d-flex gap-3 flex-wrap">
            <!-- Show Payment Request button for all user_role=1 -->
            <a href="payment_requests.php" class="btn btn-gold quick-action-btn">
              Request Payment
            </a>
            <?php if (!$is_super): ?>
              <a href="product.php" class="btn btn-gold quick-action-btn">
                 Create New Event
              </a>
              <a href="analytics.php" class="btn btn-outline-secondary quick-action-btn">
                View Reports
              </a>
              <a href="category.php" class="btn btn-outline-secondary quick-action-btn">
                Manage Categories
              </a>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Recent Events -->
      <div class="card border-0 shadow-sm" style="border-radius: 16px;">
        <div class="card-body p-4">
          <h5 style="color: var(--el-navy); font-weight: 600; margin-bottom: 20px;">Recent Events</h5>

          <?php if (!empty($recent_events)): ?>
            <?php foreach ($recent_events as $event): ?>
              <div class="event-item">
                <div>
                  <h6 style="color: var(--el-navy); margin-bottom: 4px;">
                    <?php echo htmlspecialchars($event['product_title']); ?>
                  </h6>
                  <small class="text-muted">
                    <?php echo number_format($event['tickets_sold']); ?> tickets sold
                  </small>
                </div>
                <a href="product.php" class="btn btn-sm btn-outline-primary">View</a>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="text-center text-muted py-4">
              <p>No events found. <a href="product.php">Create your first event</a></p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
