<?php
require_once("dbconfig.inc.php");
require_once("layout.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: main.php?page=login");
    exit();
}

$pdo = db_connect();
$user_id = $_SESSION['user_id'];

 $sortBy = isset($_GET["sortBy"]) ? $_GET["sortBy"]: "price";
$sortOrder = isset($_GET["sortOrder"]) ? $_GET["sortOrder"]: "asc";

// جلب الشقق المستأجرة
$query = "
    SELECT 
    f.flat_id, 
    f.flat_ref, 
    f.price, 
    r.start_date, 
    r.end_date, 
    f.location, 
    u.fullname AS owner_name, 
    u.telephone, 
    u.email, 
    u.user_id AS owner_user_id
FROM rentals r
JOIN flat f ON r.flat_id = f.flat_id
JOIN owners o ON f.owner_id = o.owner_id
JOIN users u ON o.user_id = u.user_id
WHERE r.customer_id = :customer_id 

";
$query .= " ORDER BY $sortBy $sortOrder";
$stmt = $pdo->prepare($query);
$stmt->execute([':customer_id' => $user_id]);
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
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Rented Flats</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>

<div class="container">
    <?php showSidebar(); ?>

    <main class="main-content">
        <h2>My Rented Flats</h2>
        <?php if ($flats && count($flats) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><a href='viewRentedFlats.php?sortBy=flat_ref&sortOrder=<?php echo getColumnNewSortOrder("flat_ref"); ?>'>Flat Reference<?php echo getSortArrow("flat_ref"); ?></a></th>
                            <th><a href='viewRentedFlats.php?sortBy=price&sortOrder=<?php echo getColumnNewSortOrder("price"); ?>'>Monthly Rent<?php echo getSortArrow("price"); ?></a></th>
                            <th><a href='viewRentedFlats.php?sortBy=start_date&sortOrder=<?php echo getColumnNewSortOrder("start_date"); ?>'>Start Date<?php echo getSortArrow("start_date"); ?></a></th>
                            <th><a href='viewRentedFlats.php?sortBy=end_date&sortOrder=<?php echo getColumnNewSortOrder("end_date"); ?>'>End Date<?php echo getSortArrow("end_date"); ?></a></th>
                            <th>Location</th>
                            <th>Owner Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $today = date('Y-m-d');
                        foreach ($flats as $flat):
                            $statusClass = ($flat['end_date'] >= $today) ? 'current-rental' : 'past-rental';
                            ?>
                            <tr class="<?php echo $statusClass; ?>">
                                <td>
                                    <a class="button-link" href="details.php?flat_id=<?php echo $flat['flat_id']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($flat['flat_ref']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($flat['price']); ?></td>
                                <td><?php echo htmlspecialchars($flat['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($flat['end_date']); ?></td>
                                <td><?php echo htmlspecialchars($flat['location']); ?></td>
                                <td>
                                    <a class="button-link" href="user_card.php?user_id=<?php echo $flat['owner_user_id']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($flat['owner_name']); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No rented flats found.</p>
        <?php endif; ?>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
