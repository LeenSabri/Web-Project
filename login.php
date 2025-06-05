<?php
require_once("dbconfig.inc.php");
include_once("flat.php");
require_once("layout.php");
// session_start();
$pdo = db_connect();

$usernameError = "";
$passwordError = "";
$generalError = "";
$username = "";
$password = "";

// معالجة الفورم عند الإرسال
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // قراءة القيم
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    // التحقق من الحقول الفارغة
    if (empty($username)) {
        $usernameError = "Username is required.";
    }

    if (empty($password)) {
        $passwordError = "Password is required.";
    }

    // إذا كان كلاهما موجودين
    if (empty($usernameError) && empty($passwordError)) {
        // تحقق إذا كان اسم المستخدم موجوداً (حساس للحروف)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE BINARY username = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $usernameError = "Username not found.";
        } else {
            // تحقق من كلمة المرور
            if (password_verify($password, $user['password'])) {
                // تسجيل الدخول (هنا يمكنك استخدام session إذا أردت)
                // session_start();
                // $_SESSION['user_id'] = $user['user_id'];
                header("Location: main.php"); // توجيه المستخدم للصفحة الرئيسية
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
    <title>BZU Flat Rent</title>
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
<main>
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
</body>
</html>
