# Manual test checklist
- Auth: wrong password rejected; logout ends session; direct URL access while logged out redirects to login.
- Roles: log in as staff - no Delete buttons, /logs.php and /backup.php return 403.
- CRUD: add/edit/view/delete farmer; farm and crop linked; deleting farmer with farms is blocked.
- Applications: farm list follows farmer, crop list follows farm; Returned requires remark; each change appears in Status history.
- Documents: missing list shrinks as required documents are recorded; Rejected docs count as missing.
- Search/reports: filter by name, ID, app no., status, dates; CSV opens in Excel; Print hides sidebar.
- Security: submit forms without the csrf token (blocked); try `' OR 1=1 --` in search (no effect); `<script>` in names shows as text.
- Backup: download, restore into a test database, compare row counts.
- Responsive: check at 375px, 768px, 1280px widths.
