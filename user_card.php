<?php
require_once("dbconfig.inc.php");
require_once("layout.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['user_id'])) {
    echo "User ID not provided.";
    exit();
}

$user_id = intval($_GET['user_id']);

$pdo = db_connect();
$stmt = $pdo->prepare("
    SELECT fullname, address AS city, telephone, email
    FROM users
    WHERE user_id = :user_id
");
$stmt->execute([':user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Card - <?php echo htmlspecialchars($user['fullname']); ?></title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>

<div class="container">
    <?php showSidebar(); ?>

    <main class="main-content">

        <div class="user-card">
            <h2><?php echo htmlspecialchars($user['fullname']); ?></h2>
            <p><strong>City:</strong> <?php echo htmlspecialchars($user['city']); ?></p>
            <p>
                <strong>Telephone:</strong>
                📞 <?php echo htmlspecialchars($user['telephone']); ?>
            </p>
            <p>
                <strong>Email:</strong>
                ✉️ <a href="mailto:<?php echo htmlspecialchars($user['email']); ?>">
                    <?php echo htmlspecialchars($user['email']); ?>
                </a>
            </p>
        </div>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
