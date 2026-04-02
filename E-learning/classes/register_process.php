<?php
session_start();
require_once 'database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/register.php');
    exit();
}
// Input validation
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($username) || empty($password) || empty($confirm_password)) {
    $_SESSION['error'] = 'Please enter all fields to register';
    header('Location: ../views/register.php');
    exit();
}

if (empty($username)) {
    $_SESSION['error'] = 'Username is required';
    header('Location: ../views/register.php');
    exit();
} elseif (strlen($username) < 3) {
    $_SESSION['error'] = 'Username must be at least 3 characters long';
    header('Location: ../views/register.php');
    exit();
} elseif (strlen($username) > 20) {
    $_SESSION['error'] = 'Username must be less than 20 characters';
    header('Location: ../views/register.php');
    exit();
} elseif (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
    $_SESSION['error'] = 'Username can only contain letters, numbers, underscores, and hyphens';
    header('Location: ../views/register.php');
    exit();
}

if (empty($password)) {
    $_SESSION['error'] = 'Password is required';
    header('Location: ../views/register.php');
    exit();
} elseif (strlen($password) < 8) {
    $_SESSION['error'] = 'Password must be at least 8 characters long';
    header('Location: ../views/register.php');
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = 'Passwords do not match';
    header('Location: ../views/register.php');
    exit();
}
if (empty($_SESSION)) {
    try {
        $stmt = $conn->prepare("SELECT id FROM Users_E_learning WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Username already exists';
            header('Location: ../views/register.php');
            exit();
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $_SESSION['error'] = 'A system error occurred' . $e->getMessage();
        header('Location: ../views/register.php');
        exit();
    }
}

try {
    $stmt = $conn->prepare("INSERT INTO Users_E_learning (username, password) VALUES (?, ?)");
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT)]);

    $userId = $conn->lastInsertId();
    $_SESSION['user_id'] = $userId;
    $_SESSION['username'] = $username;

    header('Location: ../views/home.php');
    exit();
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    $_SESSION['error'] = 'Registration failed, please try again. ' . $e->getMessage();
    header('Location: ../views/register.php');
    exit();
}
