<?php
session_start();
include("dbconfig.inc.php");
require_once("layout.php");

$pdo = db_connect();

// التحقق من أن المستخدم مدير
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

// معالجة الموافقة أو الرفض
// معالجة الموافقة أو الرفض
if (isset($_GET['action']) && isset($_GET['flat_id'])) {
    $flat_id = intval($_GET['flat_id']);
    if ($_GET['action'] === 'approve') {
        $stmt = $pdo->prepare("UPDATE flat SET is_approved = 1 WHERE flat_id = :flat_id");
        $stmt->execute([':flat_id' => $flat_id]);
        $message = "Flat ID $flat_id has been approved.";
    } elseif ($_GET['action'] === 'reject') {
        // ما نحذف، بس نخلي is_approved = 2
        $stmt = $pdo->prepare("UPDATE flat SET is_approved = 2 WHERE flat_id = :flat_id");
        $stmt->execute([':flat_id' => $flat_id]);
        $message = "Flat ID $flat_id has been rejected.";
    }
}


// جلب الشقق غير المعتمدة
$stmt = $pdo->query("SELECT * FROM flat WHERE is_approved = 0");
$flats = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Approve Flats</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>

    <main class="main-content">
        <h2>Flats Awaiting Approval</h2>
        <?php if (!empty($message)): ?>
            <p style="color:green;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if (count($flats) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Flat ID</th>
                        <th>Location</th>
                        <th>Address</th>
                        <th>Price</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Owner ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($flats as $flat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($flat['flat_id']); ?></td>
                            <td><?php echo htmlspecialchars($flat['location']); ?></td>
                            <td><?php echo htmlspecialchars($flat['address']); ?></td>
                            <td><?php echo htmlspecialchars($flat['price']); ?></td>
                            <td><?php echo htmlspecialchars($flat['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($flat['end_date']); ?></td>
                            <td><?php echo htmlspecialchars($flat['owner_id']); ?></td>
                            <td>
                                <a class="button-link" href="?action=approve&flat_id=<?php echo $flat['flat_id']; ?>">Approve</a>
                                <a class="button-link" href="?action=reject&flat_id=<?php echo $flat['flat_id']; ?>" style="color:red;">Reject</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No flats pending approval.</p>
        <?php endif; ?>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
