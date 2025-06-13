<?php
session_start();
require_once("dbconfig.inc.php");
require_once("layout.php");

$pdo = db_connect();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flat_id = intval($_POST['flat_id']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $total_amount = $_POST['total_amount'];
    $card_number = $_POST['card_number'];
    $expiry_date = $_POST['expiry_date'];
    $card_name = $_POST['card_name'];

    if (!preg_match('/^\d{9}$/', $card_number)) {
        echo "Invalid credit card number.";
        exit();
    }

    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = :username");
    $stmt->execute(['username' => $_SESSION['username']]);
    $customer = $stmt->fetch();
    if (!$customer) {
        echo "Customer not found.";
        exit();
    }

    $stmt = $pdo->prepare("
        INSERT INTO rentals (flat_id, customer_id, start_date, end_date)
        VALUES (:flat_id, :customer_id, :start_date, :end_date)
    ");
    $stmt->execute([
        'flat_id' => $flat_id,
        'customer_id' => $customer['user_id'],
        'start_date' => $start_date,
        'end_date' => $end_date
    ]);

    $pdo->prepare("UPDATE flat SET is_rented = 1 WHERE flat_id = :flat_id")
        ->execute(['flat_id' => $flat_id]);

    // جلب بيانات المالك
    $stmt = $pdo->prepare("
        SELECT u.user_id, u.fullname, u.mobile
        FROM users u
        JOIN flat f ON f.owner_id = u.user_id
        WHERE f.flat_id = :flat_id
    ");
    $stmt->execute(['flat_id' => $flat_id]);
    $owner = $stmt->fetch();

    // إرسال رسالة للمالك
    $message = "A customer has rented your flat (ID: $flat_id) from $start_date to $end_date.";
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, title, message, sent_date, is_read)
        VALUES (:sender_id, :receiver_id, :title, :message, NOW(), 0)
    ");
    $stmt->execute([
        'sender_id' => $customer['user_id'],
        'receiver_id' => $owner['user_id'],
        'title' => 'New Rent Request',
        'message' => $message
    ]);

    // عرض رسالة التأكيد للمستأجر
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Rent Confirmation</title>
        <link rel='stylesheet' href='test.css'>
    </head>
    <body>";
    showHeader();
    echo "<div class='container'>";
    showSidebar();
    echo "<main class='main-content'>
        <h2>Rent Confirmed!</h2>
        <p>Your flat has been successfully rented. You can collect the key from the owner:</p>
        <p><strong>Owner Name:</strong> " . htmlspecialchars($owner['fullname']) . "</p>
        <p><strong>Mobile Number:</strong> " . htmlspecialchars($owner['mobile']) . "</p>
        <p><strong>Key Pickup:</strong> On the flat address on the starting date: <b>$start_date</b></p>
        <a href='main.php'>Return to Home</a>
    </main></div>";
    showFooter();
    echo "</body></html>";
}
?>
