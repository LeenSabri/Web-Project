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
    $sortBy = isset($_GET["sortBy"]) ? $_GET["sortBy"]: "price";
    $sortOrder = isset($_GET["sortOrder"]) ? $_GET["sortOrder"]: "asc";
    $query = "SELECT * FROM flat WHERE 1=1 AND is_rented=0 AND is_approved = 1";
    

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
    $query .= " ORDER BY $sortBy $sortOrder";

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

    function getColumnNewSortOrder ($column){
        global $sortBy, $sortOrder;

        if($column == $sortBy){
            return $sortOrder == "asc" ? "desc" : "asc";
        }

        return "asc";
    }

    function getSortArrow($column){
         global $sortBy, $sortOrder;

        if($column == $sortBy){
            return $sortOrder == "asc" ?  " ▲" : " ▼";
        }

        return "";
    }
    
    echo" <div class='table-container'>";
    echo "<table>";
    echo "<thead>
            <tr>
            
        
                <th><a href='search.php?sortBy=flat_id &sortOrder=" . getColumnNewSortOrder("flat_id ") ."'>Flat ID" . getSortArrow("flat_id ") . "</a></th>
                <th><a href='search.php?sortBy=flat_ref &sortOrder=" . getColumnNewSortOrder("flat_ref ") ."'>Reference" . getSortArrow("flat_ref ") . "</a></th>
                <th>Image</th>
                <th>Owner ID</th>
                <th>Location</th>
                <th>Address</th>
                <th><a href='search.php?sortBy=price&sortOrder=" . getColumnNewSortOrder("price") ."'>Price" . getSortArrow("price") . "</a></th>
                <th><a href='search.php?sortBy=start_date&sortOrder=" . getColumnNewSortOrder("start_date") ."'>start_date" . getSortArrow("start_date") . "</a></th>
                <th><a href='search.php?sortBy=end_date&sortOrder=" . getColumnNewSortOrder("end_date") ."'>end_date" . getSortArrow("end_date") . "</a></th>
                <th><a href='search.php?sortBy=bedrooms&sortOrder=" . getColumnNewSortOrder("bedrooms") ."'>Bedrooms" . getSortArrow("bedrooms") . "</a></th>
                <th><a href='search.php?sortBy=bathrooms&sortOrder=" . getColumnNewSortOrder("bathrooms") ."'>Bathrooms" . getSortArrow("bathrooms") . "</a></th>
                <th><a href='search.php?sortBy=size&sortOrder=" . getColumnNewSortOrder("size") ."'>Size" . getSortArrow("size") . "</a></th>
                <th><a href='search.php?sortBy=furnished&sortOrder=" . getColumnNewSortOrder("furnished") ."'>furnished" . getSortArrow("furnished") . "</a></th>
                <th><a href='search.php?sortBy=heating&sortOrder=" . getColumnNewSortOrder("heating") ."'>heating" . getSortArrow("heating") . "</a></th>
                <th><a href='search.php?sortBy=parking&sortOrder=" . getColumnNewSortOrder("parking") ."'>parking" . getSortArrow("parking") . "</a></th>
                <th><a href='search.php?sortBy=is_approved&sortOrder=" . getColumnNewSortOrder("is_approved") ."'>is_approved" . getSortArrow("is_approved") . "</a></th>

            </tr>
            </thead>";
    echo "<tbody>";

    if ($flats && count($flats) > 0) {
        
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
        
    } else {
        echo "<p>No matching flats found.</p>";
    }

    echo "</tbody></table>";
    echo" <div >";
    
    ?>
</main>
</div>

<?php showFooter(); ?>
</body>
</html>
