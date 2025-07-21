<?php
define('APP_NAME', 'Sports Shop Management');
define('APP_VERSION', '1.0.0');
define('BASE_URL', 'http://localhost/sports-shop/');

// Session configuration
session_start();

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}