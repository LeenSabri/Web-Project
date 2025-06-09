<?php
session_start();
include("dbconfig.inc.php");
include_once("flat.php");
require_once("layout.php");

$pdo = db_connect();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['role'] = $_POST['role'];
    $_SESSION['national_id'] = $_POST['national_id'];
    $_SESSION['fullname'] = $_POST['fullname'];
    $_SESSION['address'] = $_POST['address'];
    $_SESSION['dob'] = $_POST['dob'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['mobile'] = $_POST['mobile'];
    $_SESSION['telephone'] = $_POST['telephone'];

    // التحقق من الرقم الوطني
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE national_id = :national_id");
    $stmt->execute([':national_id' => $_SESSION['national_id']]);
    if ($stmt->fetchColumn() > 0) {
        $error = "This National ID is already registered.";
    } else {
        if ($_SESSION['role'] === 'owner') {
            $_SESSION['bank_name'] = $_POST['bank_name'];
            $_SESSION['bank_branch'] = $_POST['bank_branch'];
            $_SESSION['account_number'] = $_POST['account_number'];
        }
        header("Location: Sign Up2.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Step 1: Personal Info</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>

    <main class="main-content">
        <h2>Step 1: Personal Info</h2>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="">
            <label>Role:</label><br>
            <input type="radio" name="role" value="customer" required> Customer<br>
            <input type="radio" name="role" value="owner" required> Owner<br><br>

            <label>National ID:</label>
            <input type="text" name="national_id" required><br>

            <label>Full Name:</label>
            <input type="text" name="fullname" pattern="[A-Za-z\s]+" required><br>

            <label>Address:</label>
            <input type="text" name="address" required><br>

            <label>Date of Birth:</label>
            <input type="date" name="dob" required><br>

            <label>Email:</label>
            <input type="email" name="email" required><br>

            <label>Mobile Number:</label>
            <input type="text" name="mobile" required><br>

            <label>Telephone Number:</label>
            <input type="text" name="telephone" required><br>

            <div id="owner-fields" style="display:none;">
                <label>Bank Name:</label>
                <input type="text" name="bank_name"><br>

                <label>Bank Branch:</label>
                <input type="text" name="bank_branch"><br>

                <label>Account Number:</label>
                <input type="text" name="account_number"><br>
            </div>

            <button type="submit">Next</button>
        </form>
        <script>
            document.querySelectorAll('input[name="role"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    document.getElementById('owner-fields').style.display = 
                        this.value === 'owner' ? 'block' : 'none';
                });
            });
        </script>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
