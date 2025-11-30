<?php
require_once '../settings/core.php';

// Restrict to admins only
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../controllers/payment_request_controller.php';

// Get all payment requests
$all_requests = get_all_payment_requests_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Approvals - EventLink Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
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
    .table-card {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
    }
    .badge-pending { background: #ffc107; color: #000; padding: 6px 12px; border-radius: 20px; }
    .badge-processing { background: #0dcaf0; color: #000; padding: 6px 12px; border-radius: 20px; }
    .badge-paid { background: #198754; color: #fff; padding: 6px 12px; border-radius: 20px; }
    .badge-rejected { background: #dc3545; color: #fff; padding: 6px 12px; border-radius: 20px; }
    .action-btn {
      padding: 4px 12px;
      font-size: 0.85rem;
      border-radius: 6px;
      border: none;
      font-weight: 500;
    }
    .btn-approve {
      background: #198754;
      color: white;
    }
    .btn-approve:hover {
      background: #146c43;
      color: white;
    }
    .btn-reject {
      background: #dc3545;
      color: white;
    }
    .btn-reject:hover {
      background: #bb2d3b;
      color: white;
    }
    .stats-row {
      display: flex;
      gap: 16px;
      margin-bottom: 24px;
    }
    .stat-box {
      flex: 1;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(43, 58, 103, 0.05);
    }
    .stat-box h6 {
      color: #6c757d;
      font-size: 0.9rem;
      margin-bottom: 8px;
    }
    .stat-box .value {
      font-size: 1.8rem;
      font-weight: 700;
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
          <small class="text-muted">Super Admin - Payment Approvals</small>
        </div>
      </div>
      <div class="d-flex gap-3">
        <a href="../index.php" class="btn btn-outline-secondary">Home</a>
        <a href="dashboard.php" class="btn btn-outline-secondary">Dashboard</a>
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
          <h6 style="color: var(--el-gold); font-weight: 600; margin-bottom: 20px;">Admin Panel</h6>
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
        <a href="analytics.php" class="sidebar-item">
          <span>📈</span> Analytics
        </a>
        <a href="payment_requests.php" class="sidebar-item">
          <span>💰</span> My Payments
        </a>
        <a href="payment_approvals.php" class="sidebar-item active">
          <span>✅</span> Payment Approvals
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10">
      <h2 style="color: var(--el-navy); font-weight: 700; margin-bottom: 24px;">Payment Request Approvals</h2>

      <!-- Statistics -->
      <div class="stats-row">
        <div class="stat-box">
          <h6>Pending Requests</h6>
          <div class="value" id="pendingCount">0</div>
        </div>
        <div class="stat-box">
          <h6>Total Amount Pending</h6>
          <div class="value" id="pendingAmount">GH₵ 0.00</div>
        </div>
        <div class="stat-box">
          <h6>Paid This Month</h6>
          <div class="value" id="paidCount">0</div>
        </div>
        <div class="stat-box">
          <h6>Total Paid Amount</h6>
          <div class="value" id="paidAmount">GH₵ 0.00</div>
        </div>
      </div>

      <!-- Filter Tabs -->
      <ul class="nav nav-tabs mb-3" id="statusTabs">
        <li class="nav-item">
          <a class="nav-link active" data-filter="all" href="#">All Requests</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-filter="pending" href="#">Pending</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-filter="processing" href="#">Processing</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-filter="paid" href="#">Paid</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" data-filter="rejected" href="#">Rejected</a>
        </li>
      </ul>

      <!-- Requests Table -->
      <div class="table-card">
        <div class="table-responsive">
          <table class="table table-hover align-middle" id="requestsTable">
            <thead class="table-light">
              <tr>
                <th>Request ID</th>
                <th>Organizer</th>
                <th>Request Date</th>
                <th>Tickets</th>
                <th>Gross Revenue</th>
                <th>Commission (5%)</th>
                <th>Net Amount</th>
                <th>Payment Method</th>
                <th>Account Details</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="11" class="text-center text-muted">Loading...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
let allRequests = [];
let currentFilter = 'all';

$(document).ready(function() {
  loadAllRequests();

  // Tab filtering
  $('.nav-link[data-filter]').on('click', function(e) {
    e.preventDefault();
    $('.nav-link').removeClass('active');
    $(this).addClass('active');
    currentFilter = $(this).data('filter');
    renderTable();
  });
});

function loadAllRequests() {
  $.ajax({
    url: '../actions/fetch_all_payment_requests_action.php',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        allRequests = response.data || [];
        calculateStats();
        renderTable();
      } else {
        $('#requestsTable tbody').html('<tr><td colspan="11" class="text-center text-muted">No requests found</td></tr>');
      }
    },
    error: function() {
      $('#requestsTable tbody').html('<tr><td colspan="11" class="text-center text-danger">Failed to load requests</td></tr>');
    }
  });
}

function calculateStats() {
  let pendingCount = 0;
  let pendingAmount = 0;
  let paidCount = 0;
  let paidAmount = 0;

  allRequests.forEach(req => {
    if (req.request_status === 'pending') {
      pendingCount++;
      pendingAmount += parseFloat(req.net_amount);
    } else if (req.request_status === 'paid') {
      paidCount++;
      paidAmount += parseFloat(req.net_amount);
    }
  });

  $('#pendingCount').text(pendingCount);
  $('#pendingAmount').text('GH₵ ' + pendingAmount.toFixed(2));
  $('#paidCount').text(paidCount);
  $('#paidAmount').text('GH₵ ' + paidAmount.toFixed(2));
}

function renderTable() {
  let filtered = currentFilter === 'all'
    ? allRequests
    : allRequests.filter(req => req.request_status === currentFilter);

  if (filtered.length === 0) {
    $('#requestsTable tbody').html('<tr><td colspan="11" class="text-center text-muted">No requests found</td></tr>');
    return;
  }

  let rows = '';
  filtered.forEach(req => {
    const statusBadge = getStatusBadge(req.request_status);
    const requestDate = new Date(req.request_date).toLocaleDateString();
    const isPending = req.request_status === 'pending';
    const isProcessing = req.request_status === 'processing';

    let actions = '';
    if (isPending) {
      actions = `
        <button class="action-btn btn-approve me-1" onclick="updateStatus(${req.request_id}, 'processing', '${req.organizer_name}', ${req.net_amount})">
          Process
        </button>
        <button class="action-btn btn-reject" onclick="rejectRequest(${req.request_id}, '${req.organizer_name}')">
          Reject
        </button>
      `;
    } else if (isProcessing) {
      actions = `
        <button class="action-btn btn-approve me-1" onclick="updateStatus(${req.request_id}, 'paid', '${req.organizer_name}', ${req.net_amount})">
          Mark Paid
        </button>
        <button class="action-btn btn-reject" onclick="rejectRequest(${req.request_id}, '${req.organizer_name}')">
          Reject
        </button>
      `;
    } else {
      actions = '<span class="text-muted">No actions</span>';
    }

    rows += `<tr>
      <td><strong>#${req.request_id}</strong></td>
      <td>${escapeHtml(req.organizer_name)}</td>
      <td>${requestDate}</td>
      <td>${req.total_tickets_sold}</td>
      <td>GH₵ ${parseFloat(req.gross_revenue).toFixed(2)}</td>
      <td class="text-danger">- GH₵ ${parseFloat(req.commission_amount).toFixed(2)}</td>
      <td class="text-success fw-bold">GH₵ ${parseFloat(req.net_amount).toFixed(2)}</td>
      <td>${escapeHtml(req.payment_method)}</td>
      <td><small>${escapeHtml(req.account_details)}</small></td>
      <td>${statusBadge}</td>
      <td>${actions}</td>
    </tr>`;
  });

  $('#requestsTable tbody').html(rows);
}

function updateStatus(requestId, newStatus, organizerName, netAmount) {
  const statusText = newStatus === 'processing' ? 'process' : 'mark as paid';

  Swal.fire({
    title: `Confirm ${statusText.charAt(0).toUpperCase() + statusText.slice(1)}`,
    html: `Are you sure you want to ${statusText} payment request for <strong>${organizerName}</strong>?<br><br>Amount: <strong>GH₵ ${netAmount.toFixed(2)}</strong>`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Confirm',
    confirmButtonColor: '#198754'
  }).then((result) => {
    if (result.isConfirmed) {
      performStatusUpdate(requestId, newStatus);
    }
  });
}

function rejectRequest(requestId, organizerName) {
  Swal.fire({
    title: 'Reject Payment Request',
    html: `Enter reason for rejecting payment request for <strong>${organizerName}</strong>:`,
    input: 'textarea',
    inputPlaceholder: 'Enter rejection reason...',
    showCancelButton: true,
    confirmButtonText: 'Reject',
    confirmButtonColor: '#dc3545',
    inputValidator: (value) => {
      if (!value) {
        return 'Please provide a reason for rejection';
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      performStatusUpdate(requestId, 'rejected', result.value);
    }
  });
}

function performStatusUpdate(requestId, status, notes = '') {
  $.ajax({
    url: '../actions/update_payment_status_action.php',
    type: 'POST',
    data: {
      request_id: requestId,
      status: status,
      admin_notes: notes
    },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        Swal.fire({
          title: 'Success!',
          text: response.message,
          icon: 'success',
          confirmButtonColor: '#D4AF37'
        }).then(() => {
          loadAllRequests(); // Reload data
        });
      } else {
        Swal.fire('Error', response.message, 'error');
      }
    },
    error: function() {
      Swal.fire('Error', 'Failed to update payment status.', 'error');
    }
  });
}

function getStatusBadge(status) {
  const badges = {
    'pending': '<span class="badge-pending">Pending</span>',
    'processing': '<span class="badge-processing">Processing</span>',
    'paid': '<span class="badge-paid">Paid</span>',
    'rejected': '<span class="badge-rejected">Rejected</span>'
  };
  return badges[status] || status;
}

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
}
</script>
</body>
</html>
