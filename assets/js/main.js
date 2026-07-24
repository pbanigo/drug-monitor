(function () {
  'use strict';

  // ---- Mobile navigation toggle ----
  var toggle = document.getElementById('navToggle');
  var links = document.getElementById('navLinks');
  if (toggle && links) {
    toggle.addEventListener('click', function () {
      var open = links.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // ---- Confirm before deleting a drug ----
  document.querySelectorAll('.delete-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      var name = form.getAttribute('data-name') || 'this drug';
      if (!confirm('Delete ' + name + '? This cannot be undone.')) {
        event.preventDefault();
      }
    });
  });

  // ---- Add/Edit form: toggle blister vs bottle fields ----
  var unitSelect = document.getElementById('unit_type');
  if (unitSelect) {
    var blister = document.querySelector('.js-blister-fields');
    var bottle = document.querySelector('.js-bottle-fields');
    var applyUnit = function () {
      var isBottle = unitSelect.value === 'bottle';
      if (blister) blister.style.display = isBottle ? 'none' : '';
      if (bottle) bottle.style.display = isBottle ? '' : 'none';
    };
    unitSelect.addEventListener('change', applyUnit);
    applyUnit();
  }

  // ---- Purchase page: build a shareable list ----
  function buildPurchaseText() {
    var rows = document.querySelectorAll('#purchaseTable tbody tr[data-tobuy]');
    var daysEl = document.getElementById('days');
    var days = daysEl ? daysEl.value : '';
    var lines = ['Purchase list' + (days ? ' (' + days + ' days)' : ''), ''];
    var count = 0;
    rows.forEach(function (row) {
      if (parseInt(row.getAttribute('data-tobuy'), 10) > 0) {
        lines.push('- ' + row.getAttribute('data-name') + ': ' + row.getAttribute('data-qty'));
        count++;
      }
    });
    if (!count) lines.push('- Nothing to buy, everything is in stock.');
    return lines.join('\n');
  }

  function copyText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(
        function () { alert('Purchase list copied to clipboard.'); },
        function () { window.prompt('Copy the list below:', text); }
      );
    } else {
      window.prompt('Copy the list below:', text);
    }
  }

  var copyBtn = document.getElementById('copyList');
  if (copyBtn) copyBtn.addEventListener('click', function () { copyText(buildPurchaseText()); });

  var shareBtn = document.getElementById('shareList');
  if (shareBtn) {
    shareBtn.addEventListener('click', function () {
      var text = buildPurchaseText();
      if (navigator.share) {
        navigator.share({ title: 'Purchase list', text: text }).catch(function () {});
      } else {
        copyText(text);
      }
    });
  }

  // ---- Manage page: expandable drug details (photos + label warnings) ----
  document.querySelectorAll('.expand-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var id = btn.getAttribute('data-id');
      var detail = document.getElementById('detail-' + id);
      if (!detail) return;
      var showing = detail.style.display !== 'none' && detail.style.display !== '';
      detail.style.display = showing ? 'none' : 'table-row';
      btn.classList.toggle('open', !showing);

      if (!showing && btn.getAttribute('data-loaded') !== '1') {
        btn.setAttribute('data-loaded', '1');
        loadWarnings(btn.getAttribute('data-name'), detail.querySelector('.warnings-box'));
      }
    });
  });

  function loadWarnings(name, box) {
    if (!box) return;
    box.textContent = 'Checking openFDA for label warnings...';
    fetch('api/label.php?name=' + encodeURIComponent(name))
      .then(function (r) { return r.json(); })
      .then(function (data) {
        if (data && data.found && data.warnings) {
          box.textContent = data.warnings;
        } else {
          box.innerHTML = '<span class="cell-muted">No label warnings found in openFDA for this drug.</span>';
        }
      })
      .catch(function () {
        box.innerHTML = '<span class="cell-muted">Could not reach the label service.</span>';
      });
  }
})();
