<?php
session_start();
require_once 'database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/login.php');
    exit();
}
// Input validation
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    $_SESSION['error'] = 'Username and password are required';
    header('Location: ../views/login.php');
    exit();
}

// Rate limiting check (simple implementation)
$max_attempts = 5;
$lockout_time = 900; // 15 minutes

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt'] = time();
}

if ($_SESSION['login_attempts'] >= $max_attempts) {
    if (time() - $_SESSION['last_attempt'] < $lockout_time) {
        $_SESSION['error'] = 'Too many failed attempts. Please try again later.';
        header('Location: ../views/login.php');
        exit();
    } else {
        $_SESSION['login_attempts'] = 0;
    }
}

try {
    $stmt = $conn->prepare("SELECT id, username, password FROM Users_E_learning WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // Reset login attempts
        unset($_SESSION['login_attempts']);
        unset($_SESSION['last_attempt']);
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        header('Location: ../views/home.php');
        exit();
    }

    // Increment failed login attempts
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt'] = time();
    
    $_SESSION['error'] = 'Invalid username or password';
    header('Location: ../views/login.php');
    exit();
    
} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    $_SESSION['error'] = 'A system error occurred. Please try again.';
    header('Location: ../views/login.php');
    exit();
}
?>