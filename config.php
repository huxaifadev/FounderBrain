<?php
// ============================================================
//  FounderBrain — Config  |  founderbrain.chat
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'founderbrain');
define('DB_USER', 'YOUR_DB_USER');
define('DB_PASS', 'YOUR_DB_PASSWORD');

define('GEMINI_API_KEY', 'YOUR_GEMINI_API_KEY');
define('GEMINI_MODEL',   'gemini-1.5-flash');

define('GOOGLE_CLIENT_ID',     'YOUR_GOOGLE_CLIENT_ID');
define('GOOGLE_CLIENT_SECRET', 'YOUR_GOOGLE_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  'https://founderbrain.chat/auth/callback.php');

define('GOOGLE_SCOPES', implode(' ', [
    'openid','email','profile',
    'https://www.googleapis.com/auth/gmail.readonly',
    'https://www.googleapis.com/auth/gmail.send',
    'https://www.googleapis.com/auth/gmail.modify',
    'https://www.googleapis.com/auth/spreadsheets',
    'https://www.googleapis.com/auth/drive.readonly',
]));

define('BASE_URL', 'https://founderbrain.chat');
define('APP_NAME', 'FounderBrain');

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_lifetime', 86400);
    session_start();
}

function db(): PDO {
    static $pdo = null;
    if (!$pdo) {
        try {
            $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
                DB_USER, DB_PASS,
                [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
        } catch (PDOException $e) {
            die(json_encode(['error'=>'DB connection failed']));
        }
    }
    return $pdo;
}

function isLoggedIn(): bool { return !empty($_SESSION['user_id']); }

function requireLogin(): void {
    if (!isLoggedIn()) { header('Location: '.BASE_URL.'/login.php'); exit; }
}

function currentUser(): array {
    static $u = null;
    if (!$u && isLoggedIn()) {
        $st = db()->prepare('SELECT * FROM users WHERE id=?');
        $st->execute([$_SESSION['user_id']]);
        $u = $st->fetch() ?: [];
    }
    return $u ?? [];
}

function hasGoogleAuth(): bool {
    if (!isLoggedIn()) return false;
    static $has = null;
    if ($has === null) {
        $st = db()->prepare('SELECT id FROM oauth_tokens WHERE user_id=?');
        $st->execute([$_SESSION['user_id']]);
        $has = (bool)$st->fetch();
    }
    return $has;
}

function getGoogleToken(): ?string {
    if (!isLoggedIn()) return null;
    $st = db()->prepare('SELECT * FROM oauth_tokens WHERE user_id=?');
    $st->execute([$_SESSION['user_id']]);
    $row = $st->fetch();
    if (!$row) return null;
    if ($row['expires_at'] && $row['expires_at'] < time()+60 && $row['refresh_token']) {
        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>http_build_query([
                'client_id'=>GOOGLE_CLIENT_ID,'client_secret'=>GOOGLE_CLIENT_SECRET,
                'refresh_token'=>$row['refresh_token'],'grant_type'=>'refresh_token'])]);
        $new = json_decode(curl_exec($ch),true); curl_close($ch);
        if (!empty($new['access_token'])) {
            db()->prepare('UPDATE oauth_tokens SET access_token=?,expires_at=? WHERE user_id=?')
                ->execute([$new['access_token'],time()+($new['expires_in']??3600),$_SESSION['user_id']]);
            return $new['access_token'];
        }
    }
    return $row['access_token'];
}

function gemini(string $prompt, string $system = '', string $mode = 'chat'): string {
    $contents = [['role'=>'user','parts'=>[['text'=>$prompt]]]];
    $payload  = ['contents'=>$contents,'generationConfig'=>['temperature'=>0.7,'maxOutputTokens'=>1200]];
    if ($system) $payload['system_instruction'] = ['parts'=>[['text'=>$system]]];
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.GEMINI_MODEL.':generateContent?key='.GEMINI_API_KEY;
    for ($i=1; $i<=3; $i++) {
        $ch = curl_init($url);
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,
            CURLOPT_POSTFIELDS=>json_encode($payload),
            CURLOPT_HTTPHEADER=>['Content-Type: application/json'],CURLOPT_TIMEOUT=>30]);
        $res  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($code===200) break;
        if ($i<3) sleep(2);
    }
    if ($code!==200) return '⚠️ AI error (HTTP '.$code.'). Try again.';
    $data = json_decode($res,true);
    return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response.';
}

function logActivity(string $action): void {
    if (!isLoggedIn()) return;
    try { db()->prepare('INSERT INTO activity_log(user_id,action)VALUES(?,?)')->execute([$_SESSION['user_id'],$action]); } catch(Exception $e){}
}
