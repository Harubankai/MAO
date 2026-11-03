// Delete modal controller
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

// Logout modal controller
(function () {
  var modal   = document.getElementById('logoutModal');
  var openBtn = document.getElementById('logoutBtn');
  var cancel  = document.getElementById('lm-cancel');
  if (!modal || !openBtn) return;

  function openModal()  { modal.hidden = false; if (cancel) cancel.focus(); }
  function closeModal() { modal.hidden = true;  }

  openBtn.addEventListener('click', openModal);
  if (cancel) cancel.addEventListener('click', closeModal);
  modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !modal.hidden) closeModal(); });
})();
// Generic modal controller: [data-modal-open="id"] opens, [data-modal-close] closes
(function () {
  function closeModal(m) { m.hidden = true; }
  document.querySelectorAll('[data-modal-open]').forEach(function (b) {
    var modal = document.getElementById(b.dataset.modalOpen);
    if (!modal) return;
    b.addEventListener('click', function () {
      modal.hidden = false;
      var first = modal.querySelector('textarea, input:not([type=hidden]), [data-modal-close]');
      if (first) first.focus();
    });
    modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(modal); });
    modal.querySelectorAll('[data-modal-close]').forEach(function (c) {
      c.addEventListener('click', function () { closeModal(modal); });
    });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.modal:not([hidden])').forEach(closeModal);
  });
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
