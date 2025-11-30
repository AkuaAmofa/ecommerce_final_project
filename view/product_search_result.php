<?php
// view/product_search_result.php
include_once '../controllers/category_controller.php';
include_once '../controllers/brand_controller.php';
$categories = get_all_categories_ctr();
$brands = get_all_brands_ctr();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Search Results - EventLink</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <style>
    body {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      min-height: 100vh;
    }
    .search-header {
      background: white;
      border-radius: 16px;
      padding: 24px;
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
      margin-bottom: 24px;
    }
    .form-control:focus, .form-select:focus {
      border-color: var(--el-gold);
      box-shadow: 0 0 0 0.25rem rgba(212, 175, 55, 0.25);
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
    .card {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      transition: transform 0.3s, box-shadow 0.3s;
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
    }
    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 30px rgba(43, 58, 103, 0.15);
    }
    .card-title {
      color: var(--el-navy);
      font-weight: 600;
    }
    .btn-outline-success {
      border-color: var(--el-gold);
      color: var(--el-gold);
    }
    .btn-outline-success:hover {
      background: var(--el-gold);
      border-color: var(--el-gold);
      color: white;
    }
    h5 {
      color: var(--el-navy);
      font-weight: 700;
    }
    .pagination-controls {
      background: white;
      border-radius: 12px;
      padding: 16px;
      box-shadow: 0 4px 20px rgba(43, 58, 103, 0.08);
    }
    .btn-outline-secondary {
      border-color: var(--el-navy);
      color: var(--el-navy);
    }
    .btn-outline-secondary:hover {
      background: var(--el-navy);
      border-color: var(--el-navy);
      color: white;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <a href="all_product.php" class="btn btn-outline-secondary mb-4">← Back to Events</a>

  <div class="search-header">
    <h4 class="mb-4" style="color: var(--el-navy); font-weight: 700;">Search Events</h4>
    <div class="row g-3">
      <div class="col-md-4">
        <input type="text" id="searchBox" class="form-control" placeholder="Search events...">
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
          <option value="">All Organizers</option>
          <?php foreach ($brands as $brand): ?>
            <option value="<?= $brand['brand_id'] ?>"><?= htmlspecialchars($brand['brand_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2 d-grid">
        <button id="applyFilters" class="btn btn-primary">Search</button>
      </div>
    </div>
  </div>

  <h5 id="searchTitle" class="mb-4"></h5>
  <div id="searchResults" class="row g-4"></div>

  <div class="pagination-controls d-flex justify-content-between align-items-center mt-4">
    <button id="prevPage" class="btn btn-outline-secondary">← Previous</button>
    <div id="pageInfo" class="text-muted fw-semibold"></div>
    <button id="nextPage" class="btn btn-outline-secondary">Next →</button>
  </div>
</div>

<script>
$(function(){
  const params = new URLSearchParams(location.search);
  $('#searchBox').val(params.get('q') || '');

  let page = 1, perPage = 10;

  function buildUrl(){
    const q = $('#searchBox').val().trim();
    const cat = $('#filterCategory').val();
    const brand = $('#filterBrand').val();

    const p = new URLSearchParams();
    p.set('page', page);
    p.set('per_page', perPage);

    if (q === '' && !cat && !brand) {
      p.set('action', 'all');
    } else {
      p.set('action', 'search_advanced');
      if (q) p.set('q', q);
      if (cat) p.set('cat', cat);
      if (brand) p.set('brand', brand);
    }
    return '../actions/product_actions.php?' + p.toString();
  }

  function load(){
    const q = $('#searchBox').val().trim();
    $('#searchTitle').text(q ? 'Search results for "' + q + '"' : 'All Events');

    $.getJSON(buildUrl(), function(payload){
      if (!payload || payload.status === 'error') {
        $('#searchResults').html('<p class="text-center text-muted mt-5">' + (payload?.message || 'No events found') + '</p>');
        $('#pageInfo').text('');
        return;
      }
      render(payload.items || []);
      renderPager(payload);
    }).fail(function(){
      $('#searchResults').html('<p class="text-center text-danger mt-5">Error loading results.</p>');
      $('#pageInfo').text('');
    });
  }

  function render(items){
    if (!items.length) {
      $('#searchResults').html('<p class="text-center text-muted mt-5">No events found.</p>');
      return;
    }

    let html = '';
    items.forEach(p => {
      html += `
      <div class="col-md-3">
        <div class="card h-100 shadow-sm">
          <img src="../uploads/${p.product_image || ''}" class="card-img-top" style="height:200px;object-fit:cover;" alt="Event">
          <div class="card-body d-flex flex-column">
            <h6 class="card-title mb-1">${p.product_title}</h6>
            <p class="text-muted small mb-2">${p.brand_name} • ${p.cat_name}</p>
            <p class="fw-bold mb-3">GHS ${parseFloat(p.product_price).toFixed(2)}</p>
            <div class="mt-auto">
              <a href="single_product.php?id=${p.product_id}" class="btn btn-sm btn-primary w-100">View Details</a>
            </div>
          </div>
        </div>
      </div>`;
    });
    $('#searchResults').html(html);
  }

  function renderPager(payload){
    const total = payload.total || 0;
    const pages = Math.max(1, Math.ceil(total / (payload.per_page || 10)));
    $('#pageInfo').text(`Page ${payload.page} of ${pages} • ${total} item(s)`);
    $('#prevPage').prop('disabled', payload.page <= 1);
    $('#nextPage').prop('disabled', payload.page >= pages);
  }

  $('#applyFilters').on('click', function(){ page = 1; load(); });
  $('#searchBox').on('keyup', function(e){ if (e.key==='Enter') { page = 1; load(); } });
  $('#filterCategory, #filterBrand').on('change', function(){ page = 1; load(); });
  $('#prevPage').on('click', function(){ if (page > 1){ page--; load(); } });
  $('#nextPage').on('click', function(){ page++; load(); });

  load();
});
</script>
</body>
</html>
