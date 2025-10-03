<?php
// Enable strict error reporting (disable display_errors in production)
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Set to '0' in production
ini_set('log_errors', '1');

// Define database connection constants
define('DB_HOST', 'localhost'); // Database host
define('DB_USER', 'root'); // Replace with your database username
define('DB_PASS', ''); // Replace with your database password
define('DB_NAME', 'user_auth'); // Replace with your database name

// Define SMTP configuration for PHPMailer
define('SMTP_HOST', 'smtp.gmail.com'); // Replace with your SMTP host
define('SMTP_USER', 'ericben824@gmail.com'); // Replace with your SMTP username
define('SMTP_PASS', 'tiktaktoe123'); // Replace with your SMTP password
define('SMTP_PORT', 587); // Replace with your SMTP port (587 for STARTTLS, 465 for SMTPS)

// Establish database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check for connection errors
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    http_response_code(500);
    die(json_encode(['error' => 'Database connection failed']));
}

// Set character set to UTF-8 for proper encoding
$conn->set_charset('utf8mb4');
?>