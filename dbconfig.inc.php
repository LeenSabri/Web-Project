<?php
define('DBHOST', 'localhost');
define('DBNAME', 'project');
define('DBUSER', 'root'); 
define('DBPASS', '');

function db_connect($dbhost = DBHOST, $dbname = DBNAME, $username = DBUSER, $password = DBPASS){
    try {
$pdo = new PDO("mysql:host=$dbhost;port=3307;dbname=$dbname;charset=utf8", $username, $password);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}
?>
