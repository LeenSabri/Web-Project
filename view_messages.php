<?php
session_start();
include("dbconfig.inc.php");
require_once("layout.php");

$pdo = db_connect();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = strtolower($_SESSION['role']); // قد نستخدمه لاحقًا لو في تفصيل حسب الدور

// جلب الرسائل الموجهة لهذا المستخدم (بناءً على ID)
$stmt = $pdo->prepare("
    SELECT m.*, u.fullname AS sender_name
    FROM messages m
    JOIN users u ON m.sender_id = u.user_id
    WHERE m.receiver_id = :uid
    ORDER BY sent_date DESC
");
$stmt->execute([':uid' => $user_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Messages</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>Inbox</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Title</th>
                        <th>Date</th>
                        <th>Sender</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($messages) === 0): ?>
                        <tr><td colspan="5">No messages available.</td></tr>
                    <?php else: ?>
                        <?php foreach ($messages as $msg): ?>
                            <tr class="<?= $msg['is_read'] ? '' : 'unread-message' ?>">
                                <td><?= $msg['is_read'] ? '' : '📩' ?></td>
                                <td>
                                    <a href="view_message_detail.php?id=<?= $msg['message_id'] ?>">
                                        <?= htmlspecialchars($msg['title']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($msg['sent_date']) ?></td>
                                <td><?= htmlspecialchars($msg['sender_name']) ?></td>
                                <td>
                                    <a href="view_message_detail.php?id=<?= $msg['message_id'] ?>" class="button-link">Open</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
