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
document.querySelectorAll('form[data-confirm]').forEach(function (f) {
  f.addEventListener('submit', function (e) { if (!confirm(f.dataset.confirm)) e.preventDefault(); });
});
