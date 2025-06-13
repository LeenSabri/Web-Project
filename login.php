<?php
require_once("dbconfig.inc.php");
include_once("flat.php");
require_once("layout.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pdo = db_connect();

$usernameError = "";
$passwordError = "";
$generalError = "";
$username = "";
$password = "";

// معالجة الفورم عند الإرسال
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username)) {
        $usernameError = "Username is required.";
    }

    if (empty($password)) {
        $passwordError = "Password is required.";
    }

    if (empty($usernameError) && empty($passwordError)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE BINARY username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $usernameError = "Username not found.";
        } else {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];

                $_SESSION['fullname'] = $user['fullname'];

                header("Location: main.php");
                exit;
            } else {
                $passwordError = "Incorrect password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BZU Flat Rent - Login</title>
    <link rel="stylesheet" href="test.css">
    <style>
        .error {
            color: red;
            font-size: 14px;
            margin-top: 3px;
        }
    </style>
</head>
<body>

<?php showHeader(); ?>

<div class="container">
    <?php showSidebar(); ?>

    <main class="main-content">
    <section>
        <fieldset>
            <legend>Login</legend>
            <form action="" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required><br>
                <?php if (!empty($usernameError)) echo "<div class='error'>$usernameError</div>"; ?>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br>
                <?php if (!empty($passwordError)) echo "<div class='error'>$passwordError</div>"; ?>

                <button type="submit">Login</button>
            </form>
        </fieldset>
    </section>
</main>
</div>
<?php showFooter(); ?>
</body>
</html>
