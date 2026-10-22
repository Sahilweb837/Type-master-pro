<?php
/**
 * Custom Type Master Pro
 * User Logout
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

logout_user();

// Start temporary session just to set flash message
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
set_flash('info', 'You have been safely signed out. See you next practice session!');

header('Location: ' . BASE_URL . '/login.php');
exit;
