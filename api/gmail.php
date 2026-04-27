<?php
require_once __DIR__ . '/../config.php';
requireLogin();
header('Content-Type: application/json');

$token  = getGoogleToken();
if (!$token) { echo json_encode(['error'=>'Google not connected. Click "Connect Google" in the sidebar.']); exit; }

$action = $_GET['action'] ?? (json_decode(file_get_contents('php://input'),true)['action'] ?? 'list');
$input  = json_decode(file_get_contents('php://input'),true) ?? [];

// ── LIST INBOX ──────────────────────────────────────────────
if ($action === 'list') {
    $ch = curl_init('https://gmail.googleapis.com/gmail/v1/users/me/messages?maxResults=20&labelIds=INBOX');
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$token]]);
    $res = json_decode(curl_exec($ch),true); curl_close($ch);

    $emails = [];
    foreach (array_slice($res['messages']??[],0,15) as $msg) {
        $email = fetchEmailDetail($msg['id'], $token);
        if ($email) $emails[] = $email;
    }
    echo json_encode(['emails'=>$emails]);
    exit;
}

// ── SCAN FOLLOW-UPS ─────────────────────────────────────────
if ($action === 'scan_followups') {
    // Get sent mail from last 14 days
    $since = strtotime('-14 days');
    $ch = curl_init('https://gmail.googleapis.com/gmail/v1/users/me/messages?maxResults=50&labelIds=INBOX&q=is:unread older_than:2d');
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$token]]);
    $res = json_decode(curl_exec($ch),true); curl_close($ch);

    $stale = [];
    foreach (array_slice($res['messages']??[],0,10) as $msg) {
        $email = fetchEmailDetail($msg['id'], $token);
        if (!$email) continue;
        $emailTs = $email['timestamp'] ?? 0;
        $daysSince = $emailTs ? round((time()-$emailTs)/86400) : 0;
        if ($daysSince >= 2) {
            $email['needsFollowup']  = true;
            $email['followupReason'] = "You forgot to reply to this " . ($daysSince>1?"$daysSince days ago":"yesterday") . " — don't let this lead go cold.";
            $stale[] = $email;
        }
    }
    echo json_encode(['count'=>count($stale),'emails'=>$stale]);
    exit;
}

// ── SEND EMAIL ───────────────────────────────────────────────
if ($action === 'send') {
    $to      = $input['to']      ?? '';
    $subject = $input['subject'] ?? '';
    $body    = $input['body']    ?? '';
    if (!$to || !$body) { echo json_encode(['error'=>'Missing to/body.']); exit; }

    $raw     = "To: $to\r\nSubject: $subject\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n$body";
    $encoded = rtrim(strtr(base64_encode($raw),'+/','-_'),'=');

    $ch = curl_init('https://gmail.googleapis.com/gmail/v1/users/me/messages/send');
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,
        CURLOPT_POSTFIELDS=>json_encode(['raw'=>$encoded]),
        CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$token,'Content-Type: application/json']]);
    $res  = json_decode(curl_exec($ch),true);
    $code = curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);

    if ($code===200||$code===201) {
        // Log followup to DB
        try { db()->prepare('INSERT INTO followup_log(user_id,to_email,subject,body)VALUES(?,?,?,?)')->execute([$_SESSION['user_id'],$to,$subject,$body]); } catch(Exception $e){}
        logActivity('email_sent');
        echo json_encode(['success'=>true]);
    } else {
        echo json_encode(['error'=>'Gmail API error '.$code.'. Make sure gmail.send scope is granted.']);
    }
    exit;
}

echo json_encode(['error'=>'Unknown action.']);

// ── HELPERS ─────────────────────────────────────────────────
function fetchEmailDetail(string $id, string $token): ?array {
    $ch = curl_init("https://gmail.googleapis.com/gmail/v1/users/me/messages/{$id}?format=full");
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$token]]);
    $d = json_decode(curl_exec($ch),true); curl_close($ch);
    if (!$d) return null;

    $headers  = $d['payload']['headers'] ?? [];
    $getH     = fn($n) => collect_header($headers,$n);
    $from     = $getH('From');
    $fromName = preg_match('/^(.+?)\s*</', $from, $m) ? trim($m[1],'" ') : $from;
    $dateStr  = $getH('Date');
    $ts       = $dateStr ? strtotime($dateStr) : 0;

    return [
        'id'           => $id,
        'threadId'     => $d['threadId']??'',
        'from'         => $from,
        'from_name'    => $fromName,
        'subject'      => $getH('Subject') ?: '(no subject)',
        'date'         => $ts ? date('M j, g:ia',$ts) : $dateStr,
        'timestamp'    => $ts,
        'snippet'      => $d['snippet']??'',
        'body'         => extractBody($d['payload']??[]),
        'unread'       => in_array('UNREAD',$d['labelIds']??[]),
        'needsFollowup'=> false,
        'urgency'      => '',
    ];
}

function collect_header(array $headers, string $name): string {
    foreach ($headers as $h) { if (strcasecmp($h['name'],$name)===0) return $h['value']; }
    return '';
}

function extractBody(array $payload): string {
    if (!empty($payload['body']['data']) && $payload['body']['size']>0)
        return mb_substr(base64_decode(strtr($payload['body']['data'],'-_','+/')),0,3000);
    foreach ($payload['parts']??[] as $part) {
        if ($part['mimeType']==='text/plain' && !empty($part['body']['data']))
            return mb_substr(base64_decode(strtr($part['body']['data'],'-_','+/')),0,3000);
    }
    foreach ($payload['parts']??[] as $part) { $r=extractBody($part); if($r) return $r; }
    return '';
}
