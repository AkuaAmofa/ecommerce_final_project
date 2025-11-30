<?php
require_once '../settings/core.php';

// Restrict to admins only
if (!isLoggedIn() || !isAdmin()) {
    header("Location: ../login/login.php");
    exit();
}

// Fetch organizer's revenue data
require_once '../controllers/order_controller.php';
require_once '../controllers/payment_request_controller.php';

$organizer_id = $_SESSION['user_id'] ?? 0;
$organizer_name = $_SESSION['name'] ?? '';

$total_tickets = get_total_tickets_sold_by_organizer_ctr($organizer_id);
$gross_revenue = get_total_revenue_by_organizer_ctr($organizer_id);
$commission = $gross_revenue * 0.05; // 5% commission
$net_amount = $gross_revenue - $commission;

// Check if there's a pending request
$has_pending = has_pending_payment_request_ctr($organizer_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Requests - EventLink Admin</title>
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
    .revenue-card {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
      margin-bottom: 24px;
    }
    .info-box {
      background: #fff3cd;
      border-left: 4px solid var(--el-gold);
      padding: 16px;
      border-radius: 8px;
      margin-bottom: 24px;
    }
    .table-card {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
    }
    .badge-pending { background: #ffc107; color: #000; }
    .badge-processing { background: #0dcaf0; color: #000; }
    .badge-paid { background: #198754; color: #fff; }
    .badge-rejected { background: #dc3545; color: #fff; }
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
        <a href="analytics.php" class="sidebar-item">
          <span>📈</span> Analytics
        </a>
        <a href="payment_requests.php" class="sidebar-item active">
          <span>💰</span> Payment Requests
        </a>
        <a href="payment_approvals.php" class="sidebar-item">
          <span>✅</span> Payment Approvals
        </a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-9 col-lg-10">
      <h2 style="color: var(--el-navy); font-weight: 700; margin-bottom: 24px;">Payment Requests</h2>

      <!-- Commission Info -->
      <div class="info-box">
        <h6 style="color: var(--el-navy); font-weight: 600; margin-bottom: 8px;">💡 Commission Information</h6>
        <p class="mb-0">A 5% commission will be deducted from each ticket sold on your behalf to cover platform fees and payment processing.</p>
      </div>

      <!-- Revenue Summary -->
      <div class="row mb-4">
        <div class="col-md-3 mb-3">
          <div class="revenue-card">
            <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 8px;">Total Tickets Sold</div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--el-navy);"><?php echo number_format($total_tickets); ?></div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="revenue-card">
            <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 8px;">Gross Revenue</div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--el-navy);">GH₵ <?php echo number_format($gross_revenue, 2); ?></div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="revenue-card">
            <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 8px;">Commission (5%)</div>
            <div style="font-size: 2rem; font-weight: 700; color: #dc3545;">- GH₵ <?php echo number_format($commission, 2); ?></div>
          </div>
        </div>
        <div class="col-md-3 mb-3">
          <div class="revenue-card">
            <div style="font-size: 0.9rem; color: #6c757d; margin-bottom: 8px;">Net Amount</div>
            <div style="font-size: 2rem; font-weight: 700; color: #198754;">GH₵ <?php echo number_format($net_amount, 2); ?></div>
          </div>
        </div>
      </div>

      <!-- Request Payment Form -->
      <div class="revenue-card">
        <h5 style="color: var(--el-navy); font-weight: 600; margin-bottom: 20px;">Request Payment</h5>

        <?php if ($gross_revenue <= 0): ?>
          <div class="alert alert-info">
            <strong>No Revenue Yet</strong><br>
            You need to have ticket sales before you can request payment.
          </div>
        <?php elseif ($has_pending): ?>
          <div class="alert alert-warning">
            <strong>Pending Request</strong><br>
            You already have a pending payment request. Please wait for it to be processed before submitting a new one.
          </div>
        <?php else: ?>
          <form id="paymentRequestForm">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                <select id="payment_method" name="payment_method" class="form-select" required>
                  <option value="">Select payment method</option>
                  <option value="Mobile Money">Mobile Money (MTN/Vodafone/AirtelTigo)</option>
                  <option value="Bank Transfer">Bank Transfer</option>
                  <option value="PayPal">PayPal</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="account_details" class="form-label">Account Details <span class="text-danger">*</span></label>
                <input type="text" id="account_details" name="account_details" class="form-control"
                  placeholder="Phone number / Account number / Email" required>
                <small class="text-muted">Enter your mobile money number, bank account, or PayPal email</small>
              </div>
            </div>

            <div class="alert alert-light border">
              <strong>Payment Summary:</strong><br>
              Gross Revenue: <strong>GH₵ <?php echo number_format($gross_revenue, 2); ?></strong><br>
              Less Commission (5%): <strong class="text-danger">- GH₵ <?php echo number_format($commission, 2); ?></strong><br>
              <hr>
              You will receive: <strong class="text-success" style="font-size: 1.2rem;">GH₵ <?php echo number_format($net_amount, 2); ?></strong>
            </div>

            <button type="submit" class="btn el-btn-gold px-5">Submit Payment Request</button>
          </form>
        <?php endif; ?>
      </div>

      <!-- Payment Request History -->
      <div class="table-card mt-4">
        <h5 style="color: var(--el-navy); font-weight: 600; margin-bottom: 20px;">Request History</h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle" id="requestsTable">
            <thead class="table-light">
              <tr>
                <th>Request Date</th>
                <th>Tickets Sold</th>
                <th>Gross Revenue</th>
                <th>Commission</th>
                <th>Net Amount</th>
                <th>Payment Method</th>
                <th>Status</th>
                <th>Processed Date</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="8" class="text-center text-muted">Loading...</td></tr>
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
$(document).ready(function() {
  // Load payment requests history
  loadPaymentRequests();

  // Submit payment request form
  $('#paymentRequestForm').on('submit', function(e) {
    e.preventDefault();

    const paymentMethod = $('#payment_method').val();
    const accountDetails = $('#account_details').val();

    if (!paymentMethod || !accountDetails) {
      Swal.fire('Error', 'Please fill in all required fields.', 'error');
      return;
    }

    // Confirm before submitting
    Swal.fire({
      title: 'Confirm Payment Request',
      html: `You are requesting payment of <strong>GH₵ <?php echo number_format($net_amount, 2); ?></strong><br>to your ${paymentMethod} account.`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Submit',
      confirmButtonColor: '#D4AF37'
    }).then((result) => {
      if (result.isConfirmed) {
        submitPaymentRequest(paymentMethod, accountDetails);
      }
    });
  });
});

function submitPaymentRequest(paymentMethod, accountDetails) {
  $.ajax({
    url: '../actions/submit_payment_request_action.php',
    type: 'POST',
    data: {
      payment_method: paymentMethod,
      account_details: accountDetails
    },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        Swal.fire({
          title: 'Success!',
          html: `Payment request submitted successfully!<br><br>
                 <strong>Gross Revenue:</strong> GH₵ ${response.data.gross_revenue}<br>
                 <strong>Commission (5%):</strong> - GH₵ ${response.data.commission}<br>
                 <strong>Net Amount:</strong> GH₵ ${response.data.net_amount}<br><br>
                 Your request is now pending review.`,
          icon: 'success',
          confirmButtonColor: '#D4AF37'
        }).then(() => {
          location.reload();
        });
      } else {
        Swal.fire('Error', response.message, 'error');
      }
    },
    error: function() {
      Swal.fire('Error', 'Failed to submit payment request.', 'error');
    }
  });
}

function loadPaymentRequests() {
  $.ajax({
    url: '../actions/fetch_payment_requests_action.php',
    type: 'GET',
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success' && response.data.length > 0) {
        let rows = '';
        response.data.forEach(req => {
          const statusBadge = getStatusBadge(req.request_status);
          const processedDate = req.processed_date ? new Date(req.processed_date).toLocaleDateString() : '-';

          rows += `<tr>
            <td>${new Date(req.request_date).toLocaleDateString()}</td>
            <td>${req.total_tickets_sold}</td>
            <td>GH₵ ${parseFloat(req.gross_revenue).toFixed(2)}</td>
            <td class="text-danger">- GH₵ ${parseFloat(req.commission_amount).toFixed(2)}</td>
            <td class="text-success fw-bold">GH₵ ${parseFloat(req.net_amount).toFixed(2)}</td>
            <td>${req.payment_method}</td>
            <td>${statusBadge}</td>
            <td>${processedDate}</td>
          </tr>`;
        });
        $('#requestsTable tbody').html(rows);
      } else {
        $('#requestsTable tbody').html('<tr><td colspan="8" class="text-center text-muted">No payment requests yet</td></tr>');
      }
    },
    error: function() {
      $('#requestsTable tbody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load requests</td></tr>');
    }
  });
}

function getStatusBadge(status) {
  const badges = {
    'pending': '<span class="badge badge-pending">Pending</span>',
    'processing': '<span class="badge badge-processing">Processing</span>',
    'paid': '<span class="badge badge-paid">Paid</span>',
    'rejected': '<span class="badge badge-rejected">Rejected</span>'
  };
  return badges[status] || status;
}
</script>
</body>
</html>
