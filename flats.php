<?php
require_once("dbconfig.inc.php");
include_once("flat.php");
require_once("layout.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pdo = db_connect();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BZU Flat Rent</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>

<main class="main-content">
   
    <?php
    
        $query = "SELECT * FROM flat WHERE is_rented=0";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $flats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($flats && count($flats) > 0) {
           echo" <div class='table-container'>";
            echo "<table>";
            echo "<thead>
                    <tr>
                    
                        <th>Flat ID</th>
                        <th>Reference</th>
                        <th>Image</th>
                        <th>Owner ID</th>
                        <th>Location</th>
                        <th>Address</th>
                        <th>Price</th>
                        <th>start_date</th>
                        <th>end_date</th>
                        <th>Bedrooms</th>
                        <th>Bathrooms</th>
                        <th>Size</th>
                        <th>furnished</th>
                        <th>heating</th>
                        <th>parking</th>
                        <th>is_approved</th>
                       
                    </tr>
                  </thead>";
            echo "<tbody>";
            foreach ($flats as $row) {
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
                echo $flat->displayInTable();
            }
            echo "</tbody></table>";
            echo" <div >";
        } else {
            echo "<p>No matching flats found.</p>";
        }
    
    ?>
</main>
    </div>
<?php showFooter(); ?>
</body>
</html>
