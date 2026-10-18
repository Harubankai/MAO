<?php
require_once __DIR__ . '/includes/functions.php';
require_login();
require_perm('manage_users');

// Route to User Management module
redirect('users/index.php');
