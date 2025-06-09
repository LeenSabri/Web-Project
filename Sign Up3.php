<?php
session_start();
include("dbconfig.inc.php");
require_once("layout.php");

$pdo = db_connect();
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_SESSION['role'];
    $national_id = $_SESSION['national_id'];
    $fullname = $_SESSION['fullname'];
    $address = $_SESSION['address'];
    $dob = $_SESSION['dob'];
    $email = $_SESSION['email'];
    $mobile = $_SESSION['mobile'];
    $telephone = $_SESSION['telephone'];
    $username = $_SESSION['username'];
    $password = password_hash($_SESSION['password'], PASSWORD_DEFAULT);

    // Generate unique 9-digit ID
    do {
        $uniqueId = mt_rand(100000000, 999999999);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE unique_id = :unique_id");
        $stmt->execute([':unique_id' => $uniqueId]);
    } while ($stmt->fetchColumn() > 0);

    // Insert user
    $sql = "INSERT INTO users 
        (national_id, fullname, address, dob, email, mobile, telephone, username, password, role, unique_id)
        VALUES 
        (:national_id, :fullname, :address, :dob, :email, :mobile, :telephone, :username, :password, :role, :unique_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':national_id' => $national_id,
        ':fullname' => $fullname,
        ':address' => $address,
        ':dob' => $dob,
        ':email' => $email,
        ':mobile' => $mobile,
        ':telephone' => $telephone,
        ':username' => $username,
        ':password' => $password,
        ':role' => $role,
        ':unique_id' => $uniqueId
    ]);

    // Insert bank details if owner
    if ($role === 'owner') {
        $stmt = $pdo->prepare("INSERT INTO owners 
            (user_id, bank_name, bank_branch, account_number) 
            VALUES (
                (SELECT user_id FROM users WHERE username = :username), 
                :bank_name, 
                :bank_branch, 
                :account_number
            )");
        $stmt->execute([
            ':username' => $username,
            ':bank_name' => $_SESSION['bank_name'],
            ':bank_branch' => $_SESSION['bank_branch'],
            ':account_number' => $_SESSION['account_number']
        ]);
    }

    session_destroy();
    $success = "Registration complete! Welcome, $fullname. Your " . ucfirst($role) . " ID is: $uniqueId";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Step 3: Confirm Details</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>

    <main class="main-content">
        <h2>Step 3: Confirm Details</h2>
        <?php if (!empty($success)): ?>
            <p style="color:green;"><?php echo $success; ?></p>
            <a href="login.php">Click here to Login</a>
        <?php else: ?>
            <form method="POST" action="">
                <p><strong>Role:</strong> <?php echo ucfirst($_SESSION['role']); ?></p>
                <p><strong>National ID:</strong> <?php echo htmlspecialchars($_SESSION['national_id']); ?></p>
                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($_SESSION['fullname']); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($_SESSION['address']); ?></p>
                <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($_SESSION['dob']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                <p><strong>Mobile:</strong> <?php echo htmlspecialchars($_SESSION['mobile']); ?></p>
                <p><strong>Telephone:</strong> <?php echo htmlspecialchars($_SESSION['telephone']); ?></p>
                <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                <?php if ($_SESSION['role'] === 'owner'): ?>
                    <p><strong>Bank Name:</strong> <?php echo htmlspecialchars($_SESSION['bank_name']); ?></p>
                    <p><strong>Bank Branch:</strong> <?php echo htmlspecialchars($_SESSION['bank_branch']); ?></p>
                    <p><strong>Account Number:</strong> <?php echo htmlspecialchars($_SESSION['account_number']); ?></p>
                <?php endif; ?>
                <button type="submit">Confirm</button>
            </form>
        <?php endif; ?>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
