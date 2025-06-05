<?php
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
            <a href="main.php?page=login">Login</a>
            <a href="main.php?page=Sign Up">Sign Up</a>
            
        </div>
        
    </header>
    ';
   
                
}

function showSidebar() {
    echo '
    <nav class="sidebar">
        <ul>
            <li><a href="main.php?">Home</a></li>
            <li><a href="main.php?page=flats">Flats</a></li>
            <li><a href="main.php?page=search">Search</a></li>
            <li><a href="main.php?page=register">Register</a></li>
            <li><a href="main.php?page=login">Login</a></li>
            <li><a href="main.php?page=about">About Us</a></li>
        </ul>
    </nav>
    ';
}


function showFooter() {
    echo '
    <footer class="footer">
        <hr>
        <p>&copy; ' . date("Y") . ' BZU Flat Rent. All rights reserved.</p>
        <p>Address: <a href="https://maps.google.com/?q=Birzeit+University" target="_blank">Birzeit University, Birzeit, Palestine</a> |Customer Support: <a href="tel:+97020042025">+970 2 004 2025</a>:  | <a href="mailto:support@birzeitfashion.com">support@birzeitfashion.com</a></p>
        <p><a href="contact us.php">Contact Us</a></p>
    </footer>
    ';
}


?>
