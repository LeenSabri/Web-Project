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

// التحقق من وصول flat_id
if (!isset($_GET['flat_id'])) {
    echo "No flat selected.";
    exit();
}

$flat_id = intval($_GET['flat_id']);

// جلب جدول المواعيد
$stmt = $pdo->prepare("
    SELECT id, day_of_week, time_slot, contact_number
    FROM flat_viewing_schedule
    WHERE flat_id = :flat_id
");
$stmt->execute(['flat_id' => $flat_id]);
$schedule = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Request Flat Preview Appointment</title>
    <link rel="stylesheet" href="test.css">
    <style>
        .booked { background-color: #ccc; color: #999; }
    </style>
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>Available Preview Appointments</h2>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>Day</th>
                <th>Time</th>
                <th>Contact</th>
                <th>Action</th>
            </tr>
            <?php
            foreach ($schedule as $slot) {
                $day = $slot['day_of_week'];
                $time = $slot['time_slot'];
                $contact = $slot['contact_number'];

                // تحويل اليوم والوقت لتاريخ فعلي (مؤقت)
                $today = date('Y-m-d');
                $preferred_date = date('Y-m-d', strtotime("next $day"));
                $preferred_time = $time;

                // التحقق من انتهاء الموعد
                $slot_datetime = strtotime("$preferred_date $preferred_time");
                $is_expired = ($slot_datetime < strtotime(date('Y-m-d H:i:s')));

                // التحقق من حجز الموعد
                $stmt2 = $pdo->prepare("
                    SELECT COUNT(*) 
                    FROM viewing_requests 
                    WHERE flat_id = :flat_id 
                      AND preferred_date = :preferred_date 
                      AND preferred_time = :preferred_time
                ");
                $stmt2->execute([
                    'flat_id' => $flat_id,
                    'preferred_date' => $preferred_date,
                    'preferred_time' => $preferred_time
                ]);
                $is_booked = ($stmt2->fetchColumn() > 0);

                echo "<tr";
                if ($is_booked) echo " class='booked'";
                echo ">";

                echo "<td>" . htmlspecialchars($day) . "</td>";
                echo "<td>" . htmlspecialchars($time) . "</td>";
                echo "<td>" . htmlspecialchars($contact) . "</td>";
                echo "<td>";
                if ($is_expired) {
                    echo "<span style='color: red;'>Expired</span>";
                } elseif ($is_booked) {
                    echo "<span>Booked</span>";
                } else {
                    echo "<a href='request_viewing_confirm.php?slot_id={$slot['id']}&flat_id={$flat_id}'>Book</a>";
                }
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
