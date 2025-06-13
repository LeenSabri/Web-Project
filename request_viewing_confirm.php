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

// التحقق من وصول slot_id و flat_id
if (!isset($_GET['slot_id']) || !isset($_GET['flat_id'])) {
    echo "Invalid slot.";
    exit();
}

$slot_id = intval($_GET['slot_id']);
$flat_id = intval($_GET['flat_id']);

// جلب بيانات الموعد من جدول flat_viewing_schedule
$stmt = $pdo->prepare("
    SELECT day_of_week, time_slot, contact_number
    FROM flat_viewing_schedule
    WHERE id = :slot_id AND flat_id = :flat_id
");
$stmt->execute([
    'slot_id' => $slot_id,
    'flat_id' => $flat_id
]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    echo "Invalid slot.";
    exit();
}

// تحويل اليوم والوقت لتاريخ فعلي
$preferred_date = date('Y-m-d', strtotime("next " . $slot['day_of_week']));
$preferred_time = $slot['time_slot'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // تسجيل الطلب في viewing_requests
    $stmt = $pdo->prepare("
        INSERT INTO viewing_requests (flat_id, user_id, preferred_date, preferred_time)
        VALUES (:flat_id, :user_id, :preferred_date, :preferred_time)
    ");
    $stmt->execute([
        ':flat_id' => $flat_id,
        ':user_id' => $_SESSION['user_id'],
        ':preferred_date' => $preferred_date,
        ':preferred_time' => $preferred_time
    ]);

    // 🔥 إضافة رسالة للمالك
    // جلب owner_id
    $stmt_owner = $pdo->prepare("
        SELECT owner_id
        FROM flat
        WHERE flat_id = :flat_id
    ");
    $stmt_owner->execute(['flat_id' => $flat_id]);
    $owner_id = $stmt_owner->fetchColumn();

    if ($owner_id) {
        $customer_name = $_SESSION['fullname'];
        $message = "New viewing request for your flat (ID: $flat_id) from $customer_name. Please review and accept the request.";

        // إرسال الرسالة للمالك
        $stmt_msg = $pdo->prepare("
            INSERT INTO messages (sender_id, receiver_id, message, is_read)
            VALUES (:sender_id, :receiver_id, :message, 0)
        ");
        $stmt_msg->execute([
            ':sender_id' => $_SESSION['user_id'],
            ':receiver_id' => $owner_id,
            ':message' => $message
        ]);
    }

    // رسالة تأكيد للعميل
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Appointment Confirmed</title>
        <link rel='stylesheet' href='test.css'>
    </head>
    <body>";
    showHeader();
    echo "<div class='container'>";
    showSidebar();
    echo "<main class='main-content'>
        <h2>Appointment Requested!</h2>
        <p>Your request has been sent to the owner and is awaiting approval.</p>
        <a href='main.php'>Back to Home</a>
    </main></div>";
    showFooter();
    echo "</body></html>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Viewing Appointment</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>Confirm Your Appointment</h2>
        <p><strong>Day:</strong> <?php echo htmlspecialchars($slot['day_of_week']); ?></p>
        <p><strong>Time:</strong> <?php echo htmlspecialchars($slot['time_slot']); ?></p>
        <p><strong>Contact:</strong> <?php echo htmlspecialchars($slot['contact_number']); ?></p>

        <form method="POST" action="">
            <button type="submit">Confirm Booking</button>
        </form>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
