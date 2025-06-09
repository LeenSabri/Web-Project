<?php
session_start();
include("dbconfig.inc.php");
require_once("layout.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['marketing_info'] = [
        'title' => $_POST['title'],
        'description' => $_POST['description'],
        'url' => $_POST['url']
    ];
    header("Location: Offer_Flat3.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Offer Flat - Step 2</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>Step 2: Marketing Info (Optional)</h2>
        <form method="POST" action="">
            <label>Title:</label>
            <input type="text" name="title"><br>

            <label>Description:</label>
            <textarea name="description"></textarea><br>

            <label>URL:</label>
            <input type="url" name="url"><br>

            <button type="submit">Next</button>
        </form>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
