<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function showHeader() {
    echo '
    <header class="header">
        <div class="logo">
            <img src="image/logo.png" alt="logo" width="120" height="70">
        </div>
        <div class="title">
            <h1>BZU Flat Rent</h1>
        </div>
        <div class="login-signUp">
    ';

    if (isset($_SESSION['role'])) {
        // المستخدم مسجل دخول
        echo '
         <a href="logout.php">Logout</a>
            <a href="profile.php">
                <img src="image/profile.png" alt="profile" width="45">
            </a>
           
        ';
    } else {
        // المستخدم ضيف
        echo '
            <a href="login.php">Login</a>
            <a href="Sign Up1.php">Sign Up</a>
        ';
    }

    echo '
        </div>
    </header>
    ';
}

function showSidebar() {
    echo '<nav class="sidebar"><ul>';

    // روابط مشتركة للجميع
    echo '<li><a href="main.php">Home</a></li>';
    echo '<li><a href="flats.php">Flats</a></li>';
    echo '<li><a href="search.php">Search</a></li>';
    echo '<li><a href="about_us.php">About Us</a></li>';

    if (isset($_SESSION['role'])) {
        // مستخدم مسجل دخول
        switch ($_SESSION['role']) {
            case 'owner':
                echo '<li><a href="Offer_Flat1.php">Offer Flat</a></li>';
                echo '<li><a href="view_messages.php">View Messages</a></li>';
                
                break;
            case 'manager':
                echo '<li><a href="manager_approve_flats.php">Approve Flats</a></li>';
                echo '<li><a href="view_messages.php">View Messages</a></li>';
                echo '<li><a href=" Flats_Inquire.php">Flats Inquire</a></li>';

                break;
            case 'customer':
                echo '<li><a href="viewRentedFlats.php">View Rented Flats</a></li>';
                echo '<li><a href="viewBasket.php">Basket</a></li>';
                echo '<li><a href="view_messages.php">View Messages</a></li>';
                break;
        }
        echo '<li><a href="logout.php">Logout</a></li>';
    } else {
        // المستخدم ضيف
        echo '<li><a href="register.php">Register</a></li>';
        echo '<li><a href="login.php">Login</a></li>';
    }

    echo '</ul></nav>';
}

function showFooter() {
    echo '
    <footer class="footer">
        <hr>
        <p>&copy; ' . date("Y") . ' BZU Flat Rent. All rights reserved.</p>
        <p>Address: <a href="https://maps.google.com/?q=Birzeit+University" target="_blank">Birzeit University, Birzeit, Palestine</a> |
        Customer Support: <a href="tel:+97020042025">+970 2 004 2025</a> |
        <a href="mailto:support@birzeitfashion.com">support@birzeitfashion.com</a></p>
        <p><a href="contact_us.php">Contact Us</a></p>
    </footer>
    ';
}
?>
