<?php
session_start();
include("dbconfig.inc.php");
require_once("layout.php");

$pdo = db_connect();
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flatDetails = $_SESSION['flat_details'];
    $marketingInfo = $_SESSION['marketing_info'];
    $images = $_SESSION['flat_images'];

    $stmt = $pdo->prepare("INSERT INTO flat 
        (owner_id, location, address, price, start_date, end_date, bedrooms, bathrooms, size, rental_conditions, heating, access_control, parking, backyard, playground, storage, is_approved)
        VALUES 
        (:owner_id, :location, :address, :price, :start_date, :end_date, :bedrooms, :bathrooms, :size, :rental_conditions, :heating, :access_control, :parking, :backyard, :playground, :storage, 0)
    ");
    $stmt->execute([
        ':owner_id' => $_SESSION['user_id'],
        ':location' => $flatDetails['location'],
        ':address' => $flatDetails['address'],
        ':price' => $flatDetails['price'],
        ':start_date' => $flatDetails['start_date'],
        ':end_date' => $flatDetails['end_date'],
        ':bedrooms' => $flatDetails['bedrooms'],
        ':bathrooms' => $flatDetails['bathrooms'],
        ':size' => $flatDetails['size'],
        ':rental_conditions' => $flatDetails['rental_conditions'],
        ':heating' => $flatDetails['heating'],
        ':access_control' => $flatDetails['access_control'],
        ':parking' => $flatDetails['parking'],
        ':backyard' => $flatDetails['backyard'],
        ':playground' => $flatDetails['playground'],
        ':storage' => $flatDetails['storage']
    ]);

    $flat_id = $pdo->lastInsertId();

    // الصور
    $targetDir = "image/";
    foreach ($images['tmp_name'] as $index => $tmpName) {
        $filename = uniqid() . "_" . basename($images['name'][$index]);
        move_uploaded_file($tmpName, $targetDir . $filename);
        $stmt = $pdo->prepare("INSERT INTO flat_images (id, image) VALUES (:flat_id, :image_path)");
        $stmt->execute([':flat_id' => $flat_id, ':image_path' => $filename]);
    }

    // التسويق
    if (!empty($marketingInfo['title'])) {
        $stmt = $pdo->prepare("INSERT INTO flat_marketing_info (flat_id, name, description, location_url) VALUES (:flat_id, :title, :description, :url)");
        $stmt->execute([
            ':flat_id' => $flat_id,
            ':title' => $marketingInfo['title'],
            ':description' => $marketingInfo['description'],
            ':url' => $marketingInfo['url']
        ]);
    }

    // المواعيد
    foreach ($_POST['day_of_week'] as $index => $day) {
        $time_slot = $_POST['time_slot'][$index];
        $contact_number = $_POST['contact_number'][$index];
        $stmt = $pdo->prepare("INSERT INTO flat_viewing_schedule (flat_id, day_of_week, time_slot, contact_number) VALUES (:flat_id, :day, :time_slot, :contact_number)");
        $stmt->execute([
            ':flat_id' => $flat_id,
            ':day' => $day,
            ':time_slot' => $time_slot,
            ':contact_number' => $contact_number
        ]);
    }

    session_unset();
    $success = "Flat has been submitted successfully for manager approval!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Offer Flat - Step 3</title>
    <link rel="stylesheet" href="test.css">
    <style>
        .slot-group {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
    </style>
    <script>
        // دالة لإضافة موعد جديد
        function addSlot() {
            const container = document.getElementById('slots-container');
            const firstSlot = document.querySelector('.slot-group');
            const newSlot = firstSlot.cloneNode(true);

            // تفريغ القيم
            newSlot.querySelectorAll('input, select').forEach(input => {
                input.value = '';
            });

            container.appendChild(newSlot);
        }
    </script>
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>Step 3: Add Viewing Schedule</h2>
        <?php if (!empty($success)): ?>
            <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
            <a href="main.php">Go to Home</a>
        <?php else: ?>
        <form method="POST" action="">
            <div id="slots-container">
                <div class="slot-group">
                    <label>Day of Week:</label>
                    <select name="day_of_week[]" required>
                        <option value="">Select a day</option>
                        <option value="Sunday">Sunday</option>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                    </select><br>

                    <label>Time Slot:</label>
                    <input type="time" name="time_slot[]" required><br>

                    <label>Contact Number:</label>
                    <input type="text" name="contact_number[]" required><br>
                </div>
            </div>

            <button type="button" onclick="addSlot()">Add Another Slot</button><br><br>
            <button type="submit">Submit</button>
        </form>
        <?php endif; ?>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
