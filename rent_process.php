<?php
session_start();
require_once("dbconfig.inc.php");
require_once("layout.php");

$pdo = db_connect();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flat_id = intval($_POST['flat_id']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // جلب بيانات الشقة
    $stmt = $pdo->prepare("SELECT * FROM flat WHERE flat_id = :flat_id");
    $stmt->execute(['flat_id' => $flat_id]);
    $flat = $stmt->fetch();

    if (!$flat) {
        echo "Flat not found.";
        exit();
    }

    // حساب المدة بالأشهر (تقريبية)
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $months = ($interval->y * 12) + $interval->m;
    if ($interval->d > 0) $months++; // لو فيه أيام إضافية بنضيف شهر

    if ($months <= 0) {
        echo "Invalid rental period.";
        exit();
    }

    $total_amount = $months * $flat['price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Rent</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>Confirm Your Rent</h2>
        <form method="POST" action="rent_confirm.php">
            <input type="hidden" name="flat_id" value="<?php echo $flat_id; ?>">
            <input type="hidden" name="start_date" value="<?php echo $start_date; ?>">
            <input type="hidden" name="end_date" value="<?php echo $end_date; ?>">
            <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>">

            <p><strong>Flat Reference:</strong> <?php echo htmlspecialchars($flat['flat_ref']); ?></p>
            <p><strong>Start Date:</strong> <?php echo $start_date; ?></p>
            <p><strong>End Date:</strong> <?php echo $end_date; ?></p>
            <p><strong>Monthly Rent:</strong> <?php echo $flat['price']; ?></p>
            <p><strong>Total Amount:</strong> <?php echo $total_amount; ?></p>

            <h3>Payment Information</h3>
            <label>Credit Card Number (9 digits):</label>
            <input type="text" name="card_number" pattern="\d{9}" required><br>

            <label>Expiry Date:</label>
            <input type="month" name="expiry_date" required><br>

            <label>Name on Card:</label>
            <input type="text" name="card_name" required><br>

            <button type="submit">Confirm Rent</button>
        </form>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
