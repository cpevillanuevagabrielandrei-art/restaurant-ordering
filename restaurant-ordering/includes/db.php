<?php
$host = "localhost";
$db   = "restaurant_db";  // change to your DB name
$user = "root";           // your DB username
$pass = "";               // your DB password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}
?>
