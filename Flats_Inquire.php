<?php
require_once("dbconfig.inc.php");
require_once("layout.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: main.php?page=login");
    exit();
}

$pdo = db_connect();

// تجهيز الكويري
$query = "
    SELECT 
        f.flat_id, 
        f.flat_ref, 
        f.price, 
        r.start_date, 
        r.end_date, 
        f.location, 
        u_owner.fullname AS owner_name, 
        u_owner.user_id AS owner_user_id,
        u_customer.fullname AS customer_name, 
        u_customer.user_id AS customer_user_id
    FROM rentals r
    JOIN flat f ON r.flat_id = f.flat_id
    JOIN owners o ON f.owner_id = o.owner_id
    JOIN users u_owner ON o.user_id = u_owner.user_id
    JOIN users u_customer ON r.customer_id = u_customer.user_id
    WHERE 1=1
";

// إضافة الفلاتر:
if (!empty($_GET['available_date'])) {
    $query .= " AND r.start_date <= :available_date AND r.end_date >= :available_date";
}
if (!empty($_GET['start_date'])) {
    $query .= " AND r.start_date >= :start_date";
}
if (!empty($_GET['end_date'])) {
    $query .= " AND r.end_date <= :end_date";
}
if (!empty($_GET['location'])) {
    $query .= " AND f.location LIKE :location";
}
if (!empty($_GET['owner_id'])) {
    $query .= " AND o.owner_id = :owner_id";
}
if (!empty($_GET['customer_id'])) {
    $query .= " AND r.customer_id = :customer_id";
}

// إضافة الترتيب
$sortBy = isset($_GET["sortBy"]) ? $_GET["sortBy"] : "r.start_date";
$sortOrder = isset($_GET["sortOrder"]) ? $_GET["sortOrder"] : "desc";
$query .= " ORDER BY $sortBy $sortOrder";

// تجهيز الاستعلام
$stmt = $pdo->prepare($query);

// الربط بالقيم
if (!empty($_GET['available_date'])) {
    $stmt->bindValue(':available_date', $_GET['available_date'], PDO::PARAM_STR);
}
if (!empty($_GET['start_date'])) {
    $stmt->bindValue(':start_date', $_GET['start_date'], PDO::PARAM_STR);
}
if (!empty($_GET['end_date'])) {
    $stmt->bindValue(':end_date', $_GET['end_date'], PDO::PARAM_STR);
}
if (!empty($_GET['location'])) {
    $stmt->bindValue(':location', '%' . $_GET['location'] . '%', PDO::PARAM_STR);
}
if (!empty($_GET['owner_id'])) {
    $stmt->bindValue(':owner_id', $_GET['owner_id'], PDO::PARAM_INT);
}
if (!empty($_GET['customer_id'])) {
    $stmt->bindValue(':customer_id', $_GET['customer_id'], PDO::PARAM_INT);
}

// تنفيذ الاستعلام
$stmt->execute();
$flats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// دوال السورت
function getColumnNewSortOrder($column)
{
    global $sortBy, $sortOrder;
    if ($column == $sortBy) {
        return $sortOrder == "asc" ? "desc" : "asc";
    }
    return "asc";
}

function getSortArrow($column)
{
    global $sortBy, $sortOrder;
    if ($column == $sortBy) {
        return $sortOrder == "asc" ? " ▲" : " ▼";
    }
    return "";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Flats Inquiry</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>

<div class="container">
    <?php showSidebar(); ?>

    <main class="main-content">
        <h2>Flats Inquiry</h2>

        <!-- Search Form -->
        <form method="GET" action="Flats_Inquire.php">
            <fieldset>
                <legend>Filter Flats</legend>
                <label for="available_date">Available on Date:</label>
                <input type="date" name="available_date" id="available_date" value="<?php echo isset($_GET['available_date']) ? htmlspecialchars($_GET['available_date']) : ''; ?>">

                <label for="start_date">Start Date From:</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>">

                <label for="end_date">End Date To:</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>">

                <label for="location">Location:</label>
                <input type="text" name="location" id="location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">

                <label for="owner_id">Owner ID:</label>
                <input type="number" name="owner_id" id="owner_id" value="<?php echo isset($_GET['owner_id']) ? htmlspecialchars($_GET['owner_id']) : ''; ?>">

                <label for="customer_id">Customer ID:</label>
                <input type="number" name="customer_id" id="customer_id" value="<?php echo isset($_GET['customer_id']) ? htmlspecialchars($_GET['customer_id']) : ''; ?>">

                <button type="submit">Search</button>
            </fieldset>
        </form>

        <?php if ($flats && count($flats) > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><a href="?sortBy=flat_ref&sortOrder=<?php echo getColumnNewSortOrder("flat_ref"); ?>">Flat Reference<?php echo getSortArrow("flat_ref"); ?></a></th>
                            <th><a href="?sortBy=price&sortOrder=<?php echo getColumnNewSortOrder("price"); ?>">Monthly Rent<?php echo getSortArrow("price"); ?></a></th>
                            <th><a href="?sortBy=start_date&sortOrder=<?php echo getColumnNewSortOrder("start_date"); ?>">Start Date<?php echo getSortArrow("start_date"); ?></a></th>
                            <th><a href="?sortBy=end_date&sortOrder=<?php echo getColumnNewSortOrder("end_date"); ?>">End Date<?php echo getSortArrow("end_date"); ?></a></th>
                            <th>Location</th>
                            <th>Owner Name</th>
                            <th>Customer Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($flats as $flat): ?>
                            <tr>
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
                                <td>
                                    <a class="button-link" href="user_card.php?user_id=<?php echo $flat['customer_user_id']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($flat['customer_name']); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No flats found.</p>
        <?php endif; ?>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
