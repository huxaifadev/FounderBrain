<?php
require_once __DIR__ . '/../config.php';
$code = $_GET['code'] ?? '';
if (!$code) { $_SESSION['login_error']='Google sign-in failed.'; header('Location: '.BASE_URL.'/login.php'); exit; }

$ch = curl_init('https://oauth2.googleapis.com/token');
curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>http_build_query(['code'=>$code,'client_id'=>GOOGLE_CLIENT_ID,
        'client_secret'=>GOOGLE_CLIENT_SECRET,'redirect_uri'=>GOOGLE_REDIRECT_URI,'grant_type'=>'authorization_code'])]);
$tok = json_decode(curl_exec($ch),true); curl_close($ch);
if (empty($tok['access_token'])) { $_SESSION['login_error']='Failed to get Google token.'; header('Location: '.BASE_URL.'/login.php'); exit; }

$ch2 = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
curl_setopt_array($ch2,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$tok['access_token']]]);
$profile = json_decode(curl_exec($ch2),true); curl_close($ch2);

if (!isLoggedIn()) {
    $st = db()->prepare('SELECT * FROM users WHERE email=? OR google_id=? LIMIT 1');
    $st->execute([$profile['email']??'',$profile['id']??'']);
    $user = $st->fetch();
    if (!$user) { $_SESSION['login_error']='No account found. Login with username first, then connect Google.'; header('Location: '.BASE_URL.'/login.php'); exit; }
    $_SESSION['user_id'] = $user['id'];
}

$uid = $_SESSION['user_id'];
db()->prepare('INSERT INTO oauth_tokens(user_id,access_token,refresh_token,expires_at,google_email)VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE access_token=VALUES(access_token),refresh_token=COALESCE(VALUES(refresh_token),refresh_token),expires_at=VALUES(expires_at),google_email=VALUES(google_email)')
    ->execute([$uid,$tok['access_token'],$tok['refresh_token']??null,time()+($tok['expires_in']??3600),$profile['email']??'']);
db()->prepare('UPDATE users SET google_id=?,picture=?,email=? WHERE id=?')
    ->execute([$profile['id']??'',$profile['picture']??'',$profile['email']??'',$uid]);
logActivity('google_connected');
header('Location: '.BASE_URL.'/index.php'); exit;
