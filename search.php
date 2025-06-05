<?php
require_once("dbconfig.inc.php");
include_once("flat.php");
require_once("layout.php");
// session_start();
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

<main>
    <section>
    <fieldset>
        <legend>Search Flats</legend>
        <form method="GET" action="search.php">
            <label for="price">Price:</label>
            <input type="number" name="price" id="price" min="0"><br>

            <label for="location">Location:</label>
            <input type="text" name="location" id="location"><br>

            <label for="bedrooms">Number of Bedrooms:</label>
            <input type="number" name="bedrooms" id="bedrooms" min="1"><br>

            <label for="bathrooms">Number of Bathrooms:</label>
            <input type="number" name="bathrooms" id="bathrooms" min="1"><br>

            <label for="furnished">Furnished:</label>
            <input type="checkbox" name="furnished" id="furnished" value="1"><br>

            <button type="submit">Search</button>
        </form>
    </fieldset>
</section>
    <?php
    if (!empty($_GET)) {
        $query = "SELECT * FROM flat WHERE 1=1 AND is_rented=0";

        if (!empty($_GET['price'])) {
            $query .= " AND price <= :price";
        }
        if (!empty($_GET['location'])) {
            $query .= " AND location LIKE :location";
        }
        if (!empty($_GET['bedrooms'])) {
            $query .= " AND bedrooms >= :bedrooms";
        }
        if (!empty($_GET['bathrooms'])) {
            $query .= " AND bathrooms >= :bathrooms";
        }
        if (isset($_GET['furnished'])) {
            $query .= " AND furnished = :furnished";
        }
        $query .= " ORDER BY price ASC";

        $stmt = $pdo->prepare($query);

        if (!empty($_GET['price'])) {
            $stmt->bindValue(':price', $_GET['price'], PDO::PARAM_INT);
        }
        if (!empty($_GET['location'])) {
            $stmt->bindValue(':location', '%' . $_GET['location'] . '%', PDO::PARAM_STR);
        }
        if (!empty($_GET['bedrooms'])) {
            $stmt->bindValue(':bedrooms', $_GET['bedrooms'], PDO::PARAM_INT);
        }
        if (!empty($_GET['bathrooms'])) {
            $stmt->bindValue(':bathrooms', $_GET['bathrooms'], PDO::PARAM_INT);
        }
        if (isset($_GET['furnished'])) {
            $stmt->bindValue(':furnished', 1, PDO::PARAM_BOOL);
        }

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
                    $row['image'] 
                );
                echo $flat->displayInTable();
            }
            echo "</tbody></table>";
            echo" <div >";
        } else {
            echo "<p>No matching flats found.</p>";
        }
    }
    ?>
</main>


</body>
</html>
