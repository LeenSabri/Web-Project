<?php
require_once("dbconfig.inc.php");
include_once("flat.php");
require_once("layout.php");
$pdo = db_connect();

$successMsg = "";
$errorMsg = "";

// تعريف مصفوفة أخطاء
$errors = [
    'national_id' => '',
    'fullname' => '',
    'address' => '',
    'dob' => '',
    'email' => '',
    'mobile' => '',
    'telephone' => '',
    'username' => '',
    'password' => '',
    'confirm_password' => '',
    'bank_name' => '',
    'bank_branch' => '',
    'account_number' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];

    if (
        isset($_POST['national_id']) &&
        isset($_POST['fullname']) &&
        isset($_POST['address']) &&
        isset($_POST['dob']) &&
        isset($_POST['email']) &&
        isset($_POST['mobile']) &&
        isset($_POST['telephone']) &&
        isset($_POST['username']) &&
        isset($_POST['password']) &&
        isset($_POST['confirm_password'])
    ) {
        $national_id = $_POST['national_id'];
        $fullname = $_POST['fullname'];
        $address = $_POST['address'];
        $dob = $_POST['dob'];
        $email = $_POST['email'];
        $mobile = $_POST['mobile'];
        $telephone = $_POST['telephone'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // التحقق من الحقول الأساسية
        if (empty($national_id)) {
            $errors['national_id'] = "National ID is required.";
        }
        if (empty($fullname)) {
            $errors['fullname'] = "Full name is required.";
        }
        if (empty($address)) {
            $errors['address'] = "Address is required.";
        }
        if (empty($dob)) {
            $errors['dob'] = "Date of birth is required.";
        }
        if (empty($email)) {
            $errors['email'] = "Email is required.";
        }
        if (empty($mobile)) {
            $errors['mobile'] = "Mobile number is required.";
        }
        if (empty($telephone)) {
            $errors['telephone'] = "Telephone number is required.";
        }
        if (empty($username)) {
            $errors['username'] = "Username is required.";
        }
        if (empty($password)) {
            $errors['password'] = "Password is required.";
        }
        if (empty($confirm_password)) {
            $errors['confirm_password'] = "Confirm password is required.";
        }

        // التحقق من كلمة السر
        if (empty($errors['password']) && empty($errors['confirm_password'])) {
            if ($password !== $confirm_password) {
                $errors['confirm_password'] = "Passwords do not match.";
            } elseif (!preg_match('/^\d.*[a-z]$/', $password) || strlen($password) < 6 || strlen($password) > 15) {
                $errors['password'] = "Password must start with a digit, end with a lowercase letter, and be 6-15 characters long.";
            }
        }

        // التحقق من الرقم الوطني المكرر
        if (empty($errors['national_id'])) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE national_id = :national_id");
            $stmt->execute([':national_id' => $national_id]);
            if ($stmt->fetchColumn() > 0) {
                $errors['national_id'] = "This national ID is already registered.";
            }
        }

        // التحقق من اسم المستخدم المكرر (case-sensitive)
        if (empty($errors['username'])) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetchColumn() > 0) {
                $errors['username'] = "This username is already registered.";
            }
        }

        // التحقق من الحقول البنكية لو Owner
        if ($role === 'owner') {
            $bank_name = $_POST['bank_name'] ?? '';
            $bank_branch = $_POST['bank_branch'] ?? '';
            $account_number = $_POST['account_number'] ?? '';

            if (empty($bank_name)) {
                $errors['bank_name'] = "Bank name is required.";
            }
            if (empty($bank_branch)) {
                $errors['bank_branch'] = "Bank branch is required.";
            }
            if (empty($account_number)) {
                $errors['account_number'] = "Account number is required.";
            }
        }

        // إذا ما في أخطاء، سجل البيانات
        if (!in_array(true, array_map('strlen', $errors))) {
            try {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users 
                    (national_id, fullname, address, dob, email, mobile, telephone, username, password, role)
                    VALUES (:national_id, :fullname, :address, :dob, :email, :mobile, :telephone, :username, :password, :role)");
                $stmt->execute([
                    ':national_id' => $national_id,
                    ':fullname' => $fullname,
                    ':address' => $address,
                    ':dob' => $dob,
                    ':email' => $email,
                    ':mobile' => $mobile,
                    ':telephone' => $telephone,
                    ':username' => $username,
                    ':password' => $hashedPassword,
                    ':role' => $role
                ]);

                if ($role === 'owner') {
                    $userId = $pdo->lastInsertId();
                    $stmt = $pdo->prepare("INSERT INTO owners 
                        (user_id, bank_name, bank_branch, account_number) 
                        VALUES (:user_id, :bank_name, :bank_branch, :account_number)");
                    $stmt->execute([
                        ':user_id' => $userId,
                        ':bank_name' => $bank_name,
                        ':bank_branch' => $bank_branch,
                        ':account_number' => $account_number
                    ]);
                }

                $successMsg = "Registration successful! Welcome, " . htmlspecialchars($fullname) . ".";
            } catch (PDOException $e) {
                $errorMsg = "Database error: " . $e->getMessage();
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
        .error { color: red; font-size: 14px; }
        .success { color: green; font-size: 16px; }
    </style>
</head>
<body>
<main>
    <section>
        <fieldset>
            <legend>Sign Up</legend>

            <?php
            if (!empty($successMsg)) {
                echo "<p class='success'>$successMsg</p>";
            }
            if (!empty($errorMsg)) {
                echo "<p class='error'>$errorMsg</p>";
            }

            if (!isset($role)) {
                echo '<form action="" method="POST" class="formRole">';
                echo '<label>Select your role:</label><br>';

                echo '<div class="radio-item"><input type="radio" id="manager" name="role" value="manager" required>';
                echo '<label for="manager">Manager</label></div>';

                echo '<div class="radio-item"><input type="radio" id="owner" name="role" value="owner" required>';
                echo '<label for="owner">Owner</label></div>';

                echo '<div class="radio-item"><input type="radio" id="customer" name="role" value="customer" required>';
                echo '<label for="customer">Customer</label></div>';

                echo '<br><button type="submit">Continue</button>';
                echo '</form>';
            } else {
                echo "<h3>" . ucfirst($role) . " Registration</h3>";
                echo '<form action="" method="POST">';
                echo '<input type="hidden" name="role" value="' . htmlspecialchars($role) . '">';

                function field($label, $name, $type = 'text', $errors, $value = '') {
                    echo "<label for='$name'>$label:</label><br>";
                    echo "<input type='$type' id='$name' name='$name' value='" . htmlspecialchars($value) . "' required><br>";
                    if (!empty($errors[$name])) {
                        echo "<span class='error'>{$errors[$name]}</span><br>";
                    }
                }

                field('National ID Number', 'national_id', 'text', $errors, $_POST['national_id'] ?? '');
                field('Full Name', 'fullname', 'text', $errors, $_POST['fullname'] ?? '');
                field('Address', 'address', 'text', $errors, $_POST['address'] ?? '');
                field('Date of Birth', 'dob', 'date', $errors, $_POST['dob'] ?? '');
                field('Email Address', 'email', 'email', $errors, $_POST['email'] ?? '');
                field('Mobile Number', 'mobile', 'text', $errors, $_POST['mobile'] ?? '');
                field('Telephone Number', 'telephone', 'text', $errors, $_POST['telephone'] ?? '');

                if ($role === 'owner') {
                    field('Bank Name', 'bank_name', 'text', $errors, $_POST['bank_name'] ?? '');
                    field('Bank Branch', 'bank_branch', 'text', $errors, $_POST['bank_branch'] ?? '');
                    field('Account Number', 'account_number', 'text', $errors, $_POST['account_number'] ?? '');
                }

                field('Username', 'username', 'text', $errors, $_POST['username'] ?? '');
                field('Password', 'password', 'password', $errors);
                field('Confirm Password', 'confirm_password', 'password', $errors);

                echo '<button type="submit">Sign Up as ' . ucfirst($role) . '</button>';
                echo '</form>';

                echo '<form action="" method="POST">';
                echo '<button type="submit">Back to Role Selection</button>';
                echo '</form>';
            }
            ?>
        </fieldset>
    </section>
</main>
<!-- <?php showFooter(); ?> -->
</body>
</html>
