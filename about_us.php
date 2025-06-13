<?php
include("layout.php"); // يحتوي على showHeader, showSidebar, showFooter
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us</title>
    <link rel="stylesheet" href="test.css">
</head>
<body>
<?php showHeader(); ?>
<div class="container">
    <?php showSidebar(); ?>
    <main class="main-content">
        <h2>About Us</h2>

        <section>
            <h3>The Agency</h3>
            <p>Our agency, <strong>BZU Flat Rent</strong>, was founded in 2015 with the aim of providing students and residents of Birzeit with high-quality flat rental services. Since its inception, the agency has won several excellence awards in property management and rental services. The management hierarchy consists of a Director, Operations Manager, and a Customer Service team.</p>
        </section>

        <section>
            <h3>The City - Birzeit</h3>
            <p>Birzeit is a small town in the West Bank, known for its historical and cultural significance. It has a population of approximately 6,000 people. Birzeit is home to Birzeit University, one of the leading academic institutions in Palestine. The town has a Mediterranean climate, with hot summers and mild winters. Famous places include the old town center and the university campus.</p>
            <p>Learn more about Birzeit on <a href="https://en.wikipedia.org/wiki/Birzeit" target="_blank">Wikipedia</a>.</p>
        </section>

        <section>
            <h3>Main Business Activities</h3>
            <ul>
                <li>Flat listing and advertising</li>
                <li>Rental agreements and management</li>
                <li>Property viewing scheduling</li>
                <li>Customer support and communication</li>
            </ul>
        </section>
    </main>
</div>
<?php showFooter(); ?>
</body>
</html>
