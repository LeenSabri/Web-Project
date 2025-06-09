<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("dbconfig.inc.php"); 
include_once("flat.php");
require_once("layout.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BZU Flat Rent</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>

<?php showHeader(); ?>

<div class="container">
    <?php showSidebar(); ?>

    <main class="main-content">
        <?php
            if (isset($_GET['page'])) {
                include($_GET['page'] . ".php");
            } else {
                echo '
                <section class="hero">
                    <h2>Your Dream Flat in Birzeit Awaits!</h2>
                    <p>Discover a wide selection of flats for rent in Birzeit. From cozy studios to spacious family homes, find your perfect match with ease and confidence.</p>
                    <a href="main.php?page=search" class="btn-primary">Find Your Perfect Flat</a>
                </section>

                <section class="recent-flats">
                    <h3>Recently Added Flats</h3>
                    <div class="flats-grid">
                        <div class="flat-card">
                            <img src="image/flat1.jpg" alt="Flat 1">
                            <h4>Modern Oasis</h4>
                            <p>Birzeit City Center</p>
                            <p class="price">$850/month</p>
                            <a href="details.php?flat_id=45" class="btn">View Details</a>
                        </div>
                        <div class="flat-card">
                            <img src="image/flat2.jpg" alt="Flat 2">
                            <h4>Cozy Studio</h4>
                            <p>Near Birzeit University</p>
                            <p class="price">$600/month</p>
                            <a href="details.php?flat_id=42" class="btn">View Details</a>
                        </div>
                        <div class="flat-card">
                            <img src="image/flat3.jpg" alt="Flat 3">
                            <h4>Family Apartment</h4>
                            <p>Al-Masyoun Quarter</p>
                            <p class="price">$1200/month</p>
                            <a href="details.php?flat_id=43" class="btn">View Details</a>
                        </div>
                        <div class="flat-card">
                            <img src="image/flat4.jpg" alt="Flat 4">
                            <h4>Family Apartment</h4>
                            <p>Al-Masyoun Quarter</p>
                            <p class="price">$1500/month</p>
                            <a href="details.php?flat_id=44" class="btn">View Details</a>
                        </div>
                    </div>
                </section>
                ';
            }
        ?>
    </main>
</div>

<?php showFooter(); ?>
</body>
</html>
