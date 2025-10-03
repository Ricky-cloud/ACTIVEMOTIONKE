<?php
session_start();

require_once 'db_config.php';

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Security token mismatch. Please try again.";
    header("Location: login.php");
    exit();
}

// Sanitize inputs
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember-me']);

// Validate inputs
if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Please fill in all fields";
    header("Location: login.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check user credentials
        $stmt = $pdo->prepare("SELECT id, firstname, lastname, email, password, is_verified FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['firstname'] . '' . $user['lastname'];
        $_SESSION['user_email'] = $user['email'];

        // Set remember me cookie if requested
        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (30 * 24 * 60 * 60); // 30 days
            
            // Store token in database
            $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
            $stmt->execute([hash('sha256', $token), date('Y-m-d H:i:s', $expiry), $user['id']]);
            
            // Set secure cookie
            setcookie('remember_token', $token, [
                'expires' => $expiry,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
        }

        $_SESSION['success'] = "Login successful! Redirecting...";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: login.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error. Please try again later." . $e->getMessage;
    header("Location: login.php");
    exit();
}