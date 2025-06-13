<?php
session_start();
require_once("dbconfig.inc.php");

$pdo = db_connect();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// التحقق من وصول message_id
if (!isset($_GET['message_id'])) {
    header("Location: view_messages.php");
    exit();
}

$message_id = intval($_GET['message_id']);

// تحديث is_read
$stmt = $pdo->prepare("
    UPDATE messages 
    SET is_read = 1 
    WHERE message_id = :message_id 
      AND receiver_id = :receiver_id
");
$stmt->execute([
    'message_id' => $message_id,
    'receiver_id' => $_SESSION['user_id']
]);

header("Location: view_messages.php");
exit();
