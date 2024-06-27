<?php
// Database credentials
$host = "localhost"; // Change this to your host if it's different
$dbname = "yars"; // Change this to your database name
$username = "root"; // Change this to your database username
$password = ""; // Change this to your database password

try {
    // Establish a connection to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set PDO to throw exceptions on error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // If connection fails, display error message
    die("Error: " . $e->getMessage());
}
?>
