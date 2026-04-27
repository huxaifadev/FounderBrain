<?php
require_once __DIR__ . '/../config.php';
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;
header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
    'client_id'=>GOOGLE_CLIENT_ID,'redirect_uri'=>GOOGLE_REDIRECT_URI,
    'response_type'=>'code','scope'=>GOOGLE_SCOPES,
    'access_type'=>'offline','prompt'=>'consent','state'=>$state,
])); exit;
