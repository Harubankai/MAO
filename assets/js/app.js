// Modal controller
(function () {
  var modal = document.getElementById('deleteModal');
  if (!modal) return;
  function close() { modal.hidden = true; }
  document.querySelectorAll('[data-delete]').forEach(function (b) {
    b.addEventListener('click', function () {
      document.getElementById('dm-id').value = b.dataset.delete;
      document.getElementById('dm-name').textContent = b.dataset.name;
      modal.hidden = false;
      document.getElementById('dm-cancel').focus();
    });
  });
  document.getElementById('dm-cancel').addEventListener('click', close);
  modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
})();

// Form confirmation
document.querySelectorAll('form[data-confirm]').forEach(function (f) {
  f.addEventListener('submit', function (e) { if (!confirm(f.dataset.confirm)) e.preventDefault(); });
});

// Mobile sidebar controller
(function () {
  var sidebar = document.getElementById('sidebar');
  var backdrop = document.getElementById('sidebarBackdrop');
  var menuBtn = document.getElementById('menuBtn');

  if (!sidebar) return;

  function openSidebar() {
    sidebar.classList.add('open');
    if (backdrop) {
      backdrop.hidden = false;
      requestAnimationFrame(function () {
        backdrop.classList.add('open');
      });
    }
  }

  function closeSidebar() {
    sidebar.classList.remove('open');
    if (backdrop) {
      backdrop.classList.remove('open');
      setTimeout(function () {
        if (!sidebar.classList.contains('open')) {
          backdrop.hidden = true;
        }
      }, 200);
    }
  }

  function toggleSidebar() {
    if (sidebar.classList.contains('open')) {
      closeSidebar();
    } else {
      openSidebar();
    }
  }

  if (menuBtn) {
    menuBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      toggleSidebar();
    });
  }

  if (backdrop) {
    backdrop.addEventListener('click', closeSidebar);
  }

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && sidebar.classList.contains('open')) {
      closeSidebar();
    }
  });

  sidebar.querySelectorAll('nav a').forEach(function (link) {
    link.addEventListener('click', function () {
      if (window.innerWidth <= 900) {
        closeSidebar();
      }
    });
  });
})();
