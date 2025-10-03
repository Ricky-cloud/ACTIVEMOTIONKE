<?php


// Enable strict error reporting
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

// 1. Verify CSRF Token
if (empty($_POST['csrf_token']) ||
 empty($_SESSION['csrf_token']) || 
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    http_response_code(403);
    die("Inavlid CSRF token. Please reload the page and try again.");
}

// 2. Verify Request Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Only POST requests allowed.");
}

// 3. Sanitize and Validate Inputs
$firstname = trim(htmlspecialchars($_POST['firstname'] ?? ''));
$lastname = trim(htmlspecialchars($_POST['lastname'] ?? ''));
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm-password'] ?? '';

// 4. Validate Required Fields
$errors = [];
if (empty($firstname)) $errors[] = "First name is required";
if (strlen($firstname) > 255) $errors[] = "First name must be 255 characters or less";
if (empty($lastname)) $errors[] = "Last name is required";
if (strlen($lastname) > 255) $errors[] = "Last name must be 255 characters or less";
if (empty($email)) $errors[] = "Email is required";
if (strlen($email) > 255) $errors[] = "Email must be 255 characters or less";
if (empty($password)) $errors[] = "Password is required";
if (strlen($password) > 72) $errors[] = "Password must be 72 characters or less";

// 5. Validate Email Format and Domain
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
} else {
    $domain = explode('@', $email)[1] ?? '';
    if (!checkdnsrr($domain, 'MX')) {
        $errors[] = "Email domain doesn't exist";
    }
}

// 6. Password Complexity Validation
if (strlen($password) < 10) {
    $errors[] = "Password must be at least 7 characters";
}
if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = "Password needs at least one uppercase letter";
}
if (!preg_match('/[a-z]/', $password)) {
    $errors[] = "Password needs at least one lowercase letter";
}
if (!preg_match('/[0-9]/', $password)) {
    $errors[] = "Password needs at least one number";
}
if (!preg_match('/[\W]/', $password)) {
    $errors[] = "Password needs at least one special character";
}

// 7. Password Match Verification
if ($password !== $confirm_password) {
    $errors[] = "Passwords don't match";
}

// 8. Return Errors if Any
if (!empty($errors)) {
    http_response_code(400);
    die(json_encode(['errors' => $errors]));
}


// 10. Database Connection
require_once 'db_config.php'; // Separate file with credentials
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    die(json_encode(['error' => 'Database connection failed']));
}

try {
    // 11. Check for Existing Email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    if ($stmt->get_result()->num_rows > 0) {
        die(json_encode(['error' => 'Email already registered']));
    }
    $stmt->close();

    // 12. Hash Password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // 13. Generate Verification Token
    $verificationToken = bin2hex(random_bytes(32));
    $tokenExpiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // 14. Insert User with Prepared Statement
    $stmt = $conn->prepare("
        INSERT INTO users 
        (firstname, lastname, email, password, verification_token, token_expires_at) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssss", 
        $firstname, 
        $lastname, 
        $email, 
        $hashedPassword, 
        $verificationToken,
        $tokenExpiry
    );

    if (!$stmt->execute()) {
        throw new Exception("Registration failed: " . $stmt->error);
    }

    // 15. Send Verification Email (using PHPMailer)
    require 'vendor/autoload.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.yourdomain.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@yourdomain.com';
        $mail->Password = 'your-email-password';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        
        $mail->setFrom('noreply@yourdomain.com', 'Your Service Name');
        $mail->addAddress($email, $firstname);
        $mail->isHTML(true);
        
        $verificationLink = "https://yourdomain.com/verify.php?token=" . urlencode($verificationToken);
        
        $mail->Subject = 'Verify Your Account';
        $mail->Body = "
            <h1>Welcome, $firstname!</h1>
            <p>Please verify your email by clicking below:</p>
            <a href='$verificationLink'>Verify Email</a>
            <p>Link expires in 24 hours</p>
        ";
        
        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        // Continue registration even if email fails
    }

    // 16. Regenerate CSRF Token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    
    // 17. Success Response
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Registration complete! Please check your email to verify your account.',
        'data' => [
            'email' => $email,
            'verification_sent' => isset($mail) 
        ]
    ]);

} catch (Exception $e) {
    error_log("Registration Error: " . $e->getMessage());
    http_response_code(500);
    
    // For debugging (show full error)
    header('Content-Type: application/json');
    die(json_encode([
        'error' => 'Registration failed. DEBUG: ' . $e->getMessage(),
        'trace' => $e->getTrace() // Optional stack trace
    ]));
    
    // For production (generic error)
    // die(json_encode(['error' => 'Registration failed. Please try again.']));
}