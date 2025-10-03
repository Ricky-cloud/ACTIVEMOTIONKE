<?php
// Database connection settings
$host = "localhost"; // Usually "localhost"
$user = "root";      // Default MySQL username
$pass = "";          // Default MySQL password (empty for XAMPP/WAMP)
$dbname = "activekenya"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>