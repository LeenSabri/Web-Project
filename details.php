<?php
include("dbconfig.inc.php");
include_once("flat.php");
require_once("layout.php");

$pdo = db_connect();

$flat_id = isset($_GET['flat_id']) && is_numeric($_GET['flat_id']) ? $_GET['flat_id'] : 0;

if ($flat_id > 0) {
    $query = "SELECT * FROM flat WHERE flat_id = :flat_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':flat_id', $flat_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
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
    $row['image'],
    $row['rental_conditions'],
    $row['access_control'],
    $row['backyard'],
    $row['playground'],
    $row['storage']
);

    } else {
        $flat = null;
    }
} else {
    $flat = null;
}


$query2 = "SELECT image FROM flat_Images WHERE id = :flat_id";
$stmt2 = $pdo->prepare($query2);
$stmt2->bindValue(':flat_id', $flat_id, PDO::PARAM_INT);
$stmt2->execute();
$images = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$query3 = "SELECT name, location_url FROM flat_marketing_info WHERE flat_id = :flat_id";
$stmt3 = $pdo->prepare($query3);
$stmt3->bindValue(':flat_id', $flat_id, PDO::PARAM_INT);
$stmt3->execute();
$marketingInfo = $stmt3->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flat Details</title>
    <link rel="stylesheet" href="test.css">
    <link rel="stylesheet" href="flat_details.css">
</head>
<body>
<?php showHeader(); ?>

<div class="container">
    <?php showSidebar(); ?>

    <main>
        <?php
        if ($flat) {
            echo "<div class=\"flat_details\">";
             echo "<div><img src='image/" . htmlspecialchars($flat->getImage()) . "' alt='Flat Image'></div>";
           
            echo "<div >";
                foreach ($images as $img) {
                 echo "<img src='image/" . htmlspecialchars($img['image']) . "' width='150' height='100' style='margin:5px;'>";
                }
            echo "</div>";

            echo "<div class=\"details\">";
           echo "<table>";
echo "<tr><td>Flat ID:</td><td>" . htmlspecialchars($flat->getFlatId()) . "</td></tr>";
echo "<tr><td>Reference:</td><td>" . htmlspecialchars($flat->getFlatRef()) . "</td></tr>";
echo "<tr><td>Owner ID:</td><td>" . htmlspecialchars($flat->getOwnerId()) . "</td></tr>";
echo "<tr><td>Location:</td><td>" . htmlspecialchars($flat->getLocation()) . "</td></tr>";
echo "<tr><td>Address:</td><td>" . htmlspecialchars($flat->getAddress()) . "</td></tr>";
echo "<tr><td>Price (monthly):</td><td>" . htmlspecialchars($flat->getPrice()) . " $</td></tr>";
echo "<tr><td>Start Date:</td><td>" . htmlspecialchars($flat->getStartDate()) . "</td></tr>";
echo "<tr><td>End Date:</td><td>" . htmlspecialchars($flat->getEndDate()) . "</td></tr>";
echo "<tr><td>Bedrooms:</td><td>" . htmlspecialchars($flat->getBedrooms()) . "</td></tr>";
echo "<tr><td>Bathrooms:</td><td>" . htmlspecialchars($flat->getBathrooms()) . "</td></tr>";
echo "<tr><td>Size:</td><td>" . htmlspecialchars($flat->getSize()) . " sqm</td></tr>";
echo "<tr><td>Furnished:</td><td>" . ($flat->getFurnished() ? "Yes" : "No") . "</td></tr>";
echo "<tr><td>Heating:</td><td>" . ($flat->getHeating() ? "Yes" : "No") . "</td></tr>";
echo "<tr><td>Parking:</td><td>" . ($flat->getParking() ? "Yes" : "No") . "</td></tr>";
echo "<tr><td>Approved:</td><td>" . ($flat->getIsApproved() ? "Yes" : "No") . "</td></tr>";
echo "<tr><td>Rented:</td><td>" . ($flat->getIsRented() ? "Yes" : "No") . "</td></tr>";
// الأعمدة الجديدة
echo "<tr><td>Rental Conditions:</td><td>" . htmlspecialchars($flat->getRentalConditions()) . "</td></tr>";
echo "<tr><td>Access Control:</td><td>" . ($flat->getAccessControl() ? "Yes" : "No") . "</td></tr>";
echo "<tr><td>Backyard:</td><td>" . htmlspecialchars($flat->getBackyard()) . "</td></tr>";
echo "<tr><td>Playground:</td><td>" . ($flat->getPlayground() ? "Yes" : "No") . "</td></tr>";
echo "<tr><td>Storage:</td><td>" . ($flat->getStorage() ? "Yes" : "No") . "</td></tr>";
echo "</table>";


            echo "<div class=\"action-links\">";
            echo "<a href='request_viewing.php?flat_id={$flat_id}' class='btn'>Request a Viewing</a>";
            echo "<a href=\"#\">Rent this Flat</a>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        } else {
            echo "<p>Flat not found or invalid ID.</p>";
        }
        ?>

        <aside>
        <div class="marketing-info">
        <h3>Nearby Landmarks & Important Places</h3>
        <ul>
            <?php
            if ($marketingInfo && count($marketingInfo) > 0) {
                foreach ($marketingInfo as $info) {
                    if (!empty($info['location_url'])) {
                        echo "<li><a href='" . htmlspecialchars($info['location_url']) . "' target='_blank'>" . htmlspecialchars($info['name']) . "</a></li>";
                    } else {
                        echo "<li>" . htmlspecialchars($info['name']) . "</li>";
                    }
                }
            } else {
                echo "<li>No marketing info available.</li>";
            }
            ?>
        </ul>
    </div>
</aside>
    </main>
    
</div>



<?php showFooter(); ?>
</body>
</html>
