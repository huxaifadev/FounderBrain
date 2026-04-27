<?php
require_once __DIR__ . '/../config.php';
requireLogin();
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'),true);
if (!empty($input['content'])) {
    db()->prepare('INSERT INTO briefings(user_id,content)VALUES(?,?)')->execute([$_SESSION['user_id'],$input['content']]);
}
echo json_encode(['success'=>true]);
