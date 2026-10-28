<?php
// PrimeVest Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'primevest');
define('DB_USER', 'root');
define('DB_PASS', '');
define('SITE_NAME', 'PrimeVest');
define('SITE_URL', 'http://localhost/primevest'); // Change this on hosting
define('CURRENCY', 'USD');
define('CURRENCY_SYMBOL', '$');

// Session settings
ini_set('session.cookie_httponly', 1);
session_start();

// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
