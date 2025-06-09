<?php
require_once("dbconfig.inc.php");
require_once("layout.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = db_connect();

// التحقق من أن المستخدم مسجل دخول كـ customer فقط
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: main.php?page=login");
    exit();
}

$flat_id = isset($_GET['flat_id']) ? intval($_GET['flat_id']) : 0;
$user_id = $_SESSION['user_id']; // المستخدم الحالي من الجلسة

$success = "";
$error = "";

// معالجة الطلب
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $preferred_date = trim($_POST['preferred_date']);
    $preferred_time = trim($_POST['preferred_time']);
    $notes = trim($_POST['notes']);

    if (empty($preferred_date) || empty($preferred_time)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO viewing_requests (flat_id, user_id, preferred_date, preferred_time, notes) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$flat_id, $user_id, $preferred_date, $preferred_time, $notes]);
            $success = "Your request has been submitted successfully!";
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Request Viewing</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>Request a Flat Viewing</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" class="requestForm">
            <input type="hidden" name="flat_id" value="<?php echo htmlspecialchars($flat_id); ?>">

            <label for="preferred_date">Preferred Date:</label>
            <input type="date" name="preferred_date" id="preferred_date" required><br>

            <label for="preferred_time">Preferred Time:</label>
            <input type="time" name="preferred_time" id="preferred_time" required><br>

            <label for="notes">Notes (optional):</label>
            <textarea name="notes" id="notes" rows="3"></textarea><br>

            <button type="submit">Submit Request</button>
        </form>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
