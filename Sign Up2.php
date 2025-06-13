<?php
session_start();
include("dbconfig.inc.php");
require_once("layout.php");

$pdo = db_connect();
$error = "";

if (!isset($_SESSION['role'])) {
    header("Location: Sign Up1.php");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (!preg_match('/^\d.*[a-z]$/', $password) || strlen($password) < 6 || strlen($password) > 15) {
        $error = "Password must start with a digit, end with a lowercase letter, and be 6-15 characters long.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute([':username' => $username]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Username already exists.";
        } else {
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            header("Location: Sign Up3.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Step 2: E-Account</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>

    <main class="main-content">
        <h2>Step 2: E-Account Registration</h2>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label>Username (valid email):</label>
            <input type="email" name="username" required><br>

            <label>Password:</label>
            <input type="password" name="password" required><br>

            <label>Confirm Password:</label>
            <input type="password" name="confirm_password" required><br>

            <button type="submit">Next</button>
        </form>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
