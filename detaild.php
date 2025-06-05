<?php
require_once("dbconfig.inc.php");
include_once("flat.php");
// session_start();

$pdo = db_connect();

$if (isset($_GET['flat_id']) && is_numeric($_GET['flat_id'])) {
    $flat_id = $_GET['flat_id'];
} else {
    $flat_id = 0;
}


if ($flat_id > 0) {
    $query = "SELECT * FROM flat WHERE flat_id = :flat_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':flat_id', $flat_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // أنشيء الكائن Flat
        $flat = new Flat(
            $row['flat_id'],
            $row['flat_ref'],
            $row['owner_id'],
            $row['location'],
            $row['address'],
            $row['price'],
            $row['start_date'],
            $row['end_date'],
            $row['bedrooms'],
            $row['bathrooms'],
            $row['size'],
            $row['furnished'],
            $row['heating'],
    
            $row['parking'],
            $row['is_approved'],
            $row['is_rented'],
            $row['image']
        );
    } else {
        $flat = null;
    }
} else {
    $flat = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flat Details</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<header>
    <h1>Flat Details</h1>
</header>

<main>
    <?php
    if ($flat) {
        echo "<h2>Flat Reference: " . htmlspecialchars($flat->getFlatRef()) . "</h2>";
        echo "<img src='images/" . htmlspecialchars($flat->getImage()) . "' width='200' onerror=\"this.onerror=null;this.src='images/default.png';\"><br><br>";
        echo "<p><strong>Location:</strong> " . htmlspecialchars($flat->getLocation()) . "</p>";
        echo "<p><strong>Address:</strong> " . htmlspecialchars($flat->getAddress()) . "</p>";
        echo "<p><strong>Price (monthly):</strong> " . htmlspecialchars($flat->getPrice()) . " $</p>";
        echo "<p><strong>Bedrooms:</strong> " . htmlspecialchars($flat->getBedrooms()) . "</p>";
        echo "<p><strong>Bathrooms:</strong> " . htmlspecialchars($flat->getBathrooms()) . "</p>";
        echo "<p><strong>Size:</strong> " . htmlspecialchars($flat->getSize()) . " sqm</p>";
        echo "<p><strong>Start Date:</strong> " . htmlspecialchars($flat->getStartDate()) . "</p>";
        echo "<p><strong>End Date:</strong> " . htmlspecialchars($flat->getEndDate()) . "</p>";
        echo "<p><strong>Furnished:</strong> " . ($flat->getFurnished() ? "Yes" : "No") . "</p>";
        echo "<p><strong>Heating:</strong> " . ($flat->getHeating() ? "Yes" : "No") . "</p>";
    
        echo "<p><strong>Parking:</strong> " . ($flat->getParking() ? "Yes" : "No") . "</p>";
       
    } else {
        echo "<p>Flat not found or invalid ID.</p>";
    }
    ?>
</main>

<footer>
    <p>&copy; 2025 BZU Flat Rent - All rights reserved</p>
</footer>
</body>
</html>
