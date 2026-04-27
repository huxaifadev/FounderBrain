<?php
require_once __DIR__ . '/../config.php';
requireLogin();
header('Content-Type: application/json');

$token = getGoogleToken();
if (!$token) { echo json_encode(['error'=>'Google not connected.']); exit; }

$action = $_GET['action'] ?? 'list';

if ($action === 'list') {
    $ch = curl_init("https://www.googleapis.com/drive/v3/files?q=mimeType='application/vnd.google-apps.spreadsheet'&fields=files(id,name,modifiedTime)&orderBy=modifiedTime+desc&pageSize=20");
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$token]]);
    $res = json_decode(curl_exec($ch),true); curl_close($ch);
    $files = array_map(fn($f)=>['id'=>$f['id'],'name'=>$f['name'],'modified'=>date('M j',strtotime($f['modifiedTime']??'now'))], $res['files']??[]);
    echo json_encode(['files'=>$files]);
    exit;
}

if ($action === 'data') {
    $id    = $_GET['id']    ?? '';
    $range = $_GET['range'] ?? 'Sheet1';
    if (!$id) { echo json_encode(['error'=>'Sheet ID required.']); exit; }

    $ch = curl_init("https://sheets.googleapis.com/v4/spreadsheets/{$id}");
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$token]]);
    $meta = json_decode(curl_exec($ch),true);
    $code = curl_getinfo($ch,CURLINFO_HTTP_CODE); curl_close($ch);

    if ($code!==200) { echo json_encode(['error'=>'Cannot access this spreadsheet. Make sure it is shared with your Google account.']); exit; }

    $ch2 = curl_init("https://sheets.googleapis.com/v4/spreadsheets/{$id}/values/".urlencode($range));
    curl_setopt_array($ch2,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_HTTPHEADER=>['Authorization: Bearer '.$token]]);
    $data = json_decode(curl_exec($ch2),true); curl_close($ch2);

    logActivity('sheets_read');
    echo json_encode(['title'=>$meta['properties']['title']??'Sheet','values'=>$data['values']??[]]);
    exit;
}

echo json_encode(['error'=>'Unknown action.']);
