<?php
session_start();
include("dbconfig.inc.php");
require_once("layout.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['flat_details'] = [
        'location' => $_POST['location'],
        'address' => $_POST['address'],
        'price' => $_POST['price'],
        'start_date' => $_POST['start_date'],
        'end_date' => $_POST['end_date'],
        'bedrooms' => $_POST['bedrooms'],
        'bathrooms' => $_POST['bathrooms'],
        'size' => $_POST['size'],
        'rental_conditions' => $_POST['rental_conditions'],
        'heating' => isset($_POST['heating']) ? 1 : 0,
        'air_conditioning' => isset($_POST['air_conditioning']) ? 1 : 0,
        'access_control' => isset($_POST['access_control']) ? 1 : 0,
        'parking' => isset($_POST['parking']) ? 1 : 0,
        'backyard' => $_POST['backyard'],
        'playground' => isset($_POST['playground']) ? 1 : 0,
        'storage' => isset($_POST['storage']) ? 1 : 0
    ];

    $_SESSION['flat_images'] = $_FILES['images'];

    header("Location: Offer_Flat2.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Offer Flat - Step 1</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>Step 1: Flat Details</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Location:</label>
            <input type="text" name="location" required><br>

            <label>Address:</label>
            <input type="text" name="address" required><br>

            <label>Monthly Rent:</label>
            <input type="number" name="price" step="0.01" required><br>

            <label>Start Date:</label>
            <input type="date" name="start_date" required><br>

            <label>End Date:</label>
            <input type="date" name="end_date" required><br>

            <label>Bedrooms:</label>
            <input type="number" name="bedrooms" required><br>

            <label>Bathrooms:</label>
            <input type="number" name="bathrooms" required><br>

            <label>Size (sqm):</label>
            <input type="number" name="size" required><br>

            <label>Rent Conditions:</label>
            <textarea name="rental_conditions"></textarea><br>

            <label><input type="checkbox" name="heating"> Heating</label><br>
            <label><input type="checkbox" name="air_conditioning"> Air Conditioning</label><br>
            <label><input type="checkbox" name="access_control"> Access Control</label><br>
            <label><input type="checkbox" name="parking"> Parking</label><br>

            <label>Backyard:</label>
            <select name="backyard">
                <option value="individual">Individual</option>
                <option value="shared">Shared</option>
                <option value="none">None</option>
            </select><br>

            <label><input type="checkbox" name="playground"> Playground</label><br>
            <label><input type="checkbox" name="storage"> Storage</label><br>

            <label>Flat Images (at least 3):</label>
            <input type="file" name="images[]" multiple required><br>

            <button type="submit">Next</button>
        </form>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
