<?php require_once __DIR__.'/../config.php'; logActivity('logout'); session_destroy(); header('Location: '.BASE_URL.'/login.php'); exit;
