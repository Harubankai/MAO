# Moncada Agricultural Insurance & Farmer Records System

PHP 8+ / MySQL (PDO). Roles: `admin`, `staff`.

## Install
1. Copy folder to web root (e.g. `htdocs/moncada-farmers`).
2. Import `database/schema.sql`, then `database/upgrade_full.sql` (phpMyAdmin or `mysql -u root < file`).
3. Edit `config/database.php` (DB credentials, `MYSQLDUMP_PATH`).
4. Open `http://localhost/moncada-farmers/`.

## Accounts (change passwords before real use)
- Admin: `admin` / `Admin@123`   - Staff (demo): `staff` / `Staff@123`
- New admin password hash: `php -r "echo password_hash('NewPass', PASSWORD_BCRYPT);"` then
  `UPDATE users SET password_hash='<hash>' WHERE username='admin';`

## Permissions (edit `can()` in includes/functions.php)
Admin: everything incl. delete, activity logs, backup. Staff: add/edit farmers, farms, crops, applications, record documents, update statuses.
These are developer defaults, not confirmed MAO policy. Confirm who may approve applications.

## Demo data
5 farmers (schema.sql), 1 farm + 1 crop (upgrade_full.sql), `staff` account. Delete before production.

## Backup / restore
Admin > Backup downloads a .sql file. Restore: `mysql -u root -p moncada_agri < backup.sql`.

## Not yet built
File upload of scanned documents, PDF export (use Print > Save as PDF), user management screen, login throttling, HTTPS enforcement.
