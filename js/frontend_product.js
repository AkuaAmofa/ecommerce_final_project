/* Frontend product scripts for product listing and single product details
   - Fetches products from ../actions/product_actions.php
   - Hooks into IDs already present in `view/all_product.php` and `view/single_product.php`
*/
$(function () {
  const api = '../actions/product_actions.php';

  // Utility: get query param
  function qparam(name) {
    const params = new URLSearchParams(window.location.search);
    return params.get(name);
  }

  // Utility: escape HTML
  function escapeHtml(s) {
    return String(s || '').replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
  }

  // Utility: make image URL
  function makeImageUrl(imgField) {
    if (!imgField) return 'https://via.placeholder.com/300x200?text=No+Image';
    const s = String(imgField).trim();
    // Full URL
    if (/^https?:\/\//i.test(s)) return s;
    // Already contains uploads/ (absolute or relative)
    if (s.indexOf('uploads/') !== -1) {
      // If it starts with '/', make relative from view
      if (s.startsWith('/')) return '..' + s;
      // If it already starts with '../' or './', keep as-is
      if (s.startsWith('..') || s.startsWith('.')) return s;
      // Otherwise assume it's 'uploads/filename' or 'uploads/dir/filename'
      return '../' + s;
    }
    // Otherwise assume it's a bare filename
    return '../uploads/' + s;
  }

  // ---------- LIST PAGE ----------
  if ($('#productGrid').length) {
    let page = 1;
    const perPage = 9;

    function renderCard(p) {
      const img = makeImageUrl(p.product_image);
      console.debug('renderCard image ->', img, p.product_image);
      return `
        <div class="col-md-6 col-lg-4">
          <div class="card h-100">
            <img src="${img}" class="card-img-top" alt="${escapeHtml(p.product_title)}">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">${escapeHtml(p.product_title)}</h5>
              <p class="card-text text-muted mb-2">${escapeHtml(p.cat_name)} · ${escapeHtml(p.brand_name)}</p>
              <p class="card-text fw-bold mt-auto">GHS ${Number(p.product_price).toFixed(2)}</p>
              <a href="single_product.php?id=${p.product_id}" class="btn el-btn-gold mt-2">View Details</a>
            </div>
          </div>
        </div>`;
    }

    function loadProducts() {
      $('#productGrid').html('<div class="text-center py-4">Loading...</div>');
      const cat = $('#filterCategory').val() || '';
      const brand = $('#filterBrand').val() || '';
      const q = $('#searchBox').val() || '';

      // If there is a search or filters use search_advanced for simplicity
      const params = {
        action: q || cat || brand ? 'search_advanced' : 'all',
        page: page,
        per_page: perPage
      };
      if (q) params.q = q;
      if (cat) params.cat = cat;
      if (brand) params.brand = brand;

      $.getJSON(api, params).done(function (res) {
        const items = res.items || [];
        if (!items.length) {
          $('#productGrid').html('<div class="text-center py-4">No events found.</div>');
          $('#resultsCount').text('0 events found');
        } else {
          const html = items.map(renderCard).join('');
          $('#productGrid').html(html);
          $('#resultsCount').text((res.total ?? items.length) + ' events found');
        }
        const totalPages = Math.ceil((res.total || 0) / perPage) || 1;
        $('#pageInfo').text('Page ' + (res.page || page) + ' of ' + totalPages);
        $('#prevPage').prop('disabled', page <= 1);
        $('#nextPage').prop('disabled', page >= totalPages);
      }).fail(function (xhr) {
        $('#productGrid').html('<div class="text-center text-danger py-4">Error loading events.</div>');
        $('#resultsCount').text('Error');
      });
    }

    // Handlers
    $('#searchBox').on('keyup', function (e) {
      if (e.key === 'Enter') {
        page = 1;
        loadProducts();
      }
    });

    $('#filterCategory, #filterBrand').on('change', function () {
      page = 1;
      loadProducts();
    });

    $('#clearFilters').on('click', function (e) {
      e.preventDefault();
      $('#searchBox').val('');
      $('#filterCategory').val('');
      $('#filterBrand').val('');
      page = 1;
      loadProducts();
    });

    $('#prevPage').on('click', function () {
      if (page > 1) {
        page--;
        loadProducts();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });

    $('#nextPage').on('click', function () {
      page++;
      loadProducts();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // initial load
    loadProducts();
  }

  // ---------- SINGLE PAGE ----------
  if ($('#eventContent').length) {
    const id = parseInt(qparam('id') || 0, 10);
    if (!id) {
      $('#eventContent').html('<p class="text-danger">Invalid event ID.</p>');
    } else {
      $('#eventContent').html('<p class="text-center text-muted">Loading event details...</p>');
      $.getJSON(api, { action: 'single', id: id }).done(function (res) {
        if (res.status && res.status === 'error') {
          $('#eventContent').html('<p class="text-danger">' + (res.message || 'Event not found') + '</p>');
          return;
        }
        
        const p = res;
        const img = makeImageUrl(p.product_image);

        // Set hero background image
        $('#eventHero').css('background-image', 'url(' + img + ')');

        console.debug('single product image ->', img, p.product_image);

        // Format date and time
        let eventDateDisplay = '';
        let eventTimeDisplay = '';
        let locationDisplay = '';

        if (p.event_date) {
          const dateObj = new Date(p.event_date);
          eventDateDisplay = dateObj.toLocaleDateString('en-GB', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
          });
        }

        if (p.event_time) {
          const timeStr = p.event_time;
          const [hours, minutes] = timeStr.split(':');
          const timeObj = new Date();
          timeObj.setHours(parseInt(hours), parseInt(minutes));
          eventTimeDisplay = timeObj.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
          });
        }

        if (p.product_location) {
          locationDisplay = escapeHtml(p.product_location);
        }

        let eventDetailsHtml = '';
        if (eventDateDisplay || eventTimeDisplay || locationDisplay || p.organizer_name) {
          eventDetailsHtml = '<div class="event-meta-info mb-4 p-3 bg-light rounded">';
          if (p.organizer_name) {
            eventDetailsHtml += `<p class="mb-2"><strong>🎭 Organized by:</strong> ${escapeHtml(p.organizer_name)}</p>`;
          }
          if (eventDateDisplay) {
            eventDetailsHtml += `<p class="mb-2"><strong>📅 Date:</strong> ${eventDateDisplay}</p>`;
          }
          if (eventTimeDisplay) {
            eventDetailsHtml += `<p class="mb-2"><strong>🕒 Time:</strong> ${eventTimeDisplay}</p>`;
          }
          if (locationDisplay) {
            eventDetailsHtml += `<p class="mb-0"><strong>📍 Location:</strong> ${locationDisplay}</p>`;
          }
          eventDetailsHtml += '</div>';
        }

        const html = `
          <h2 class="mb-3">${escapeHtml(p.product_title)}</h2>
          <p class="text-muted mb-3">${escapeHtml(p.cat_name)} · ${escapeHtml(p.brand_name)}</p>
          <img src="${img}" class="img-fluid rounded mb-3" alt="${escapeHtml(p.product_title)}">
          ${eventDetailsHtml}
          <p>${escapeHtml(p.product_desc || 'No description available.')}</p>
        `;
        $('#eventContent').html(html);

        const priceDisplay = Number(p.product_price) === 0 ? 'Free' : 'GHS ' + Number(p.product_price).toFixed(2);

        // Check ticket availability
        const ticketsLeft = p.ticket_quantity || 0;
        let ticketWarning = '';

        if (ticketsLeft === 0) {
          ticketWarning = '<div class="alert alert-danger mb-3"><strong>SOLD OUT!</strong> No tickets available.</div>';
        } else if (ticketsLeft <= 5) {
          ticketWarning = `<div class="alert alert-warning mb-3"><strong>⚠️ Almost Sold Out!</strong> Only ${ticketsLeft} ticket${ticketsLeft > 1 ? 's' : ''} left!</div>`;
        }

        const sidebar = `
            <div class="card">
                <h4>${priceDisplay}</h4>
                <p class="text-muted mb-3">${escapeHtml(p.product_title)}</p>
                ${ticketWarning}
                ${ticketsLeft > 0 ? `<button class="btn el-btn-gold" onclick="addToCart(${p.product_id})">Buy Ticket</button>` : '<button class="btn btn-secondary" disabled>Sold Out</button>'}
            </div>`;
        $('#eventSidebar').html(sidebar);

        // Load similar events (optional)
        loadSimilarEvents(p.cat_id, p.product_id);

      }).fail(function () {
        $('#eventContent').html('<p class="text-danger">Failed to fetch event details.</p>');
      });
    }
  }

  // Load similar events for single product page
  function loadSimilarEvents(catId, currentId) {
    $.getJSON(api, { action: 'all', per_page: 3 }).done(function (res) {
      const items = (res.items || []).filter(p => p.product_id != currentId).slice(0, 3);
      if (!items.length) {
        $('#similarEvents').html('<p class="text-muted col-12">No similar events found.</p>');
        return;
      }

      const html = items.map(p => {
        const img = makeImageUrl(p.product_image);
        const price = Number(p.product_price) === 0 ? 'Free' : 'GHS ' + Number(p.product_price).toFixed(2);
        return `
          <div class="col-md-4">
            <div class="card h-100">
              <img src="${img}" class="card-img-top" style="height:200px;object-fit:cover;" alt="${escapeHtml(p.product_title)}">
              <div class="card-body">
                <h6 class="card-title">${escapeHtml(p.product_title)}</h6>
                <p class="card-text text-muted small">${escapeHtml(p.cat_name)} · ${escapeHtml(p.brand_name)}</p>
                <p class="fw-bold text-gold">${price}</p>
                <a href="single_product.php?id=${p.product_id}" class="btn el-btn-gold btn-sm w-100">View Details</a>
              </div>
            </div>
          </div>`;
      }).join('');
      $('#similarEvents').html(html);
    }).fail(function () {
      $('#similarEvents').html('<p class="text-muted col-12">Could not load similar events.</p>');
    });
  }

  // Add to cart function
  window.addToCart = async function(productId) {
    if (!productId) {
      alert('Invalid product ID');
      return;
    }

    try {
      const res = await fetch('../actions/cart_add_action.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
          p_id: productId,
          qty: 1
        })
      });

      const text = await res.text();
      let data;
      try {
        data = JSON.parse(text);
      } catch (e) {
        alert('Server returned invalid response:\n' + text);
        return;
      }

      if (data.status === 'success') {
        window.location.href = 'cart.php?added=1';
      } else {
        alert(data.message || 'Could not add ticket to cart.');
      }
    } catch (err) {
      console.error(err);
      alert('Server error. Please try again.');
    }
  };

});

