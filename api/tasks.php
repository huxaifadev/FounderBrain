<?php
require_once __DIR__ . '/../config.php';
requireLogin();
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'),true);
$action = $input['action']??'';
$uid = $_SESSION['user_id'];
if ($action==='save') {
    $st = db()->prepare('INSERT INTO tasks(user_id,title,action,category,priority,deadline)VALUES(?,?,?,?,?,?)');
    foreach ($input['tasks']??[] as $t) $st->execute([$uid,substr($t['title']??'',0,490),$t['action']??'',$t['category']??'admin',$t['priority']??'medium',$t['deadline']??'no-deadline']);
    echo json_encode(['success'=>true]);
} elseif ($action==='done') {
    db()->prepare('UPDATE tasks SET done=1 WHERE id=? AND user_id=?')->execute([(int)($input['id']??0),$uid]);
    echo json_encode(['success'=>true]);
} else { echo json_encode(['error'=>'Unknown action']); }
