<?php
require_once '../settings/core.php';

// Restrict to admins only
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit();
}

// Fetch analytics data
require_once '../controllers/product_controller.php';
require_once '../controllers/order_controller.php';

// Get logged-in organizer ID
$organizer_id = $_SESSION['user_id'] ?? 0;

// Get statistics filtered by organizer
$total_tickets = get_total_tickets_sold_by_organizer_ctr($organizer_id);
$total_revenue = get_total_revenue_by_organizer_ctr($organizer_id);
$active_events = get_active_events_count_by_organizer_ctr($organizer_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Analytics - EventLink Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    }
    .chart-card {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
      margin-bottom: 24px;
    }
    .chart-title {
      color: var(--el-navy);
      font-weight: 600;
      margin-bottom: 20px;
      font-size: 1.1rem;
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
        <a href="product.php" class="sidebar-item">
          <span>📅</span> Manage Events
        </a>
        <a href="analytics.php" class="sidebar-item active">
          <span>📈</span> Analytics
        </a>
        <a href="payment_requests.php" class="sidebar-item">
          <span>💰</span> Payment Requests
        </a>
        <a href="payment_approvals.php" class="sidebar-item">
          <span>✅</span> Payment Approvals
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10">
      <h2 style="color: var(--el-navy); font-weight: 700; margin-bottom: 24px;">Analytics & Reports</h2>

      <!-- Summary Stats -->
      <div class="row mb-4">
        <div class="col-md-4 mb-3">
          <div class="stat-card">
            <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 8px;">Total Tickets Sold</div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--el-navy);"><?php echo number_format($total_tickets); ?></div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="stat-card">
            <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 8px;">Total Revenue</div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--el-navy);">GH₵ <?php echo number_format($total_revenue, 2); ?></div>
          </div>
        </div>
        <div class="col-md-4 mb-3">
          <div class="stat-card">
            <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 8px;">Active Events</div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--el-navy);"><?php echo $active_events; ?></div>
          </div>
        </div>
      </div>

      <!-- Charts -->
      <div class="row">
        <div class="col-lg-6">
          <div class="chart-card">
            <h5 class="chart-title">Revenue Overview</h5>
            <canvas id="revenueChart"></canvas>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="chart-card">
            <h5 class="chart-title">Tickets Sold</h5>
            <canvas id="ticketsChart"></canvas>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="chart-card">
            <h5 class="chart-title">Monthly Performance</h5>
            <canvas id="monthlyChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Chart colors matching EventLink branding
const primaryColor = '#2B3A67'; // Navy
const goldColor = '#D4AF37'; // Gold
const chartColors = ['#2B3A67', '#D4AF37', '#0369a1', '#d97706', '#7c3aed', '#10b981'];

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'doughnut',
    data: {
        labels: ['Total Revenue', 'Remaining Target'],
        datasets: [{
            data: [<?php echo $total_revenue; ?>, <?php echo max(0, 200000 - $total_revenue); ?>],
            backgroundColor: [goldColor, '#e9ecef'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Tickets Chart
const ticketsCtx = document.getElementById('ticketsChart').getContext('2d');
new Chart(ticketsCtx, {
    type: 'pie',
    data: {
        labels: ['Tickets Sold', 'Remaining Capacity'],
        datasets: [{
            data: [<?php echo $total_tickets; ?>, <?php echo max(0, 1000 - $total_tickets); ?>],
            backgroundColor: [primaryColor, '#e9ecef'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Monthly Performance Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [
            {
                label: 'Revenue (GH₵)',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, <?php echo $total_revenue; ?>],
                backgroundColor: goldColor,
                borderRadius: 8
            },
            {
                label: 'Tickets Sold',
                data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, <?php echo $total_tickets; ?>],
                backgroundColor: primaryColor,
                borderRadius: 8
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>
</body>
</html>
