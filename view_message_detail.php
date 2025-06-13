<?php
session_start();
require_once("dbconfig.inc.php");
require_once("layout.php");

$pdo = db_connect();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = strtolower($_SESSION['role']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid message ID.";
    exit();
}

$message_id = intval($_GET['id']);

// جلب الرسالة
$stmt = $pdo->prepare("SELECT m.*, u.fullname AS sender_name FROM messages m 
    JOIN users u ON m.sender_id = u.user_id 
    WHERE m.message_id = :id AND m.receiver_id = :uid");
$stmt->execute([':id' => $message_id, ':uid' => $user_id]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$message) {
    echo "Message not found.";
    exit();
}

// تعليم كمقروء
$pdo->prepare("UPDATE messages SET is_read = 1 WHERE message_id = :id")
    ->execute([':id' => $message_id]);

$response = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'], ['accept', 'reject'])) {
    $action = $_POST['action'] === 'accept' ? 'accepted' : 'rejected';

    // إرسال رسالة رد للطرف الآخر
    $reply = "Your request regarding \"" . $message['title'] . "\" has been $action.";
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, title, message, sent_date, is_read)
                           VALUES (:sid, :rid, :title, :msg, NOW(), 0)");
    $stmt->execute([
        ':sid' => $user_id,
        ':rid' => $message['sender_id'],
        ':title' => 'Response to: ' . $message['title'],
        ':msg' => $reply
    ]);

    $response = "You have $action the request and a notification was sent.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Message Details</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
<?php showSidebar(); ?>
<main class="main-content">
    <h2>Message Details</h2>

    <p><strong>Title:</strong> <?= htmlspecialchars($message['title']) ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($message['sent_date']) ?></p>
    <p><strong>From:</strong> <?= htmlspecialchars($message['sender_name']) ?></p>
    <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($message['message'])) ?></p>

    <?php if (!empty($response)): ?>
        <div class="success"><?= $response ?></div>
    <?php endif; ?>

    <?php if ($role === 'owner' || $role === 'manager'): ?>
        <form method="POST">
            <button type="submit" name="action" value="accept">✅ Accept</button>
            <button type="submit" name="action" value="reject">❌ Reject</button>
        </form>
    <?php endif; ?>

    <br><a href="view_messages.php" class="button-link">Back to Inbox</a>
</main>
</div>
<?php showFooter(); ?>
</body>
</html>
