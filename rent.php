<?php
session_start();
require_once("dbconfig.inc.php");
require_once("layout.php");

$pdo = db_connect();

// التحقق من تسجيل الدخول
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'customer') {
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}



// التحقق من وصول flat_id
if (!isset($_GET['flat_id'])) {
    echo "No flat selected.";
    exit();
}

$flat_id = intval($_GET['flat_id']);


// جلب بيانات الشقة
$stmt = $pdo->prepare("
    SELECT f.*, u.fullname as owner_name, u.address as owner_address, u.unique_id as owner_unique_id
    FROM flat f
    JOIN users u ON f.owner_id = u.user_id
    WHERE f.flat_id = :flat_id
");
$stmt->execute(['flat_id' => $flat_id]);
$flat = $stmt->fetch();

if (!$flat) {
    echo "Flat not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rent Flat</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>Rent This Flat</h2>
        <form method="POST" action="rent_process.php">
            <input type="hidden" name="flat_id" value="<?php echo $flat['flat_id']; ?>">
            <p><strong>Flat Reference:</strong> <?php echo htmlspecialchars($flat['flat_ref']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($flat['location']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($flat['address']); ?></p>
            <p><strong>Bedrooms:</strong> <?php echo $flat['bedrooms']; ?></p>
            <p><strong>Bathrooms:</strong> <?php echo $flat['bathrooms']; ?></p>
            <p><strong>Price per Month:</strong> <?php echo $flat['price']; ?></p>
            <h3>Owner Info:</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($flat['owner_name']); ?></p>
            <p><strong>ID:</strong> <?php echo htmlspecialchars($flat['owner_unique_id']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($flat['owner_address']); ?></p>

            <label>Start Date:</label>
            <input type="date" name="start_date" required 
                min="<?php echo $flat['start_date']; ?>" 
                max="<?php echo $flat['end_date']; ?>"><br>

            <label>End Date:</label>
            <input type="date" name="end_date" required 
                min="<?php echo $flat['start_date']; ?>" 
                max="<?php echo $flat['end_date']; ?>"><br>


            <button type="submit">Proceed to Confirmation</button>
        </form>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
