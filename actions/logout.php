<?php
// POST logout — détruit la session

session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

csrf_check();

session_destroy();
redirect(APP_URL . '/nbadmin/login');
