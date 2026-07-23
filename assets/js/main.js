// Mobile navigation toggle
(function () {
  var toggle = document.getElementById('navToggle');
  var links = document.getElementById('navLinks');
  if (toggle && links) {
    toggle.addEventListener('click', function () {
      var open = links.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Confirm before deleting a drug (each row has its own delete form)
  document.querySelectorAll('.delete-form').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      var name = form.getAttribute('data-name') || 'this drug';
      if (!confirm('Delete ' + name + '? This cannot be undone.')) {
        event.preventDefault();
      }
    });
  });
})();
