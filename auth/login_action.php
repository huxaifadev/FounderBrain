<?php
require_once __DIR__ . '/../config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: '.BASE_URL.'/login.php'); exit; }
$u = trim($_POST['username'] ?? '');
$p = $_POST['password'] ?? '';
if (!$u || !$p) { $_SESSION['login_error']='Enter username and password.'; header('Location: '.BASE_URL.'/login.php'); exit; }
try {
    $st = db()->prepare('SELECT * FROM users WHERE username=? LIMIT 1');
    $st->execute([$u]); $user = $st->fetch();
    if (!$user || !password_verify($p, $user['password'])) {
        $_SESSION['login_error']='Invalid username or password.';
        header('Location: '.BASE_URL.'/login.php'); exit;
    }
    $_SESSION['user_id'] = $user['id'];
    logActivity('login');
    header('Location: '.BASE_URL.'/index.php'); exit;
} catch (Exception $e) {
    $_SESSION['login_error']='Database error.';
    header('Location: '.BASE_URL.'/login.php'); exit;
}
