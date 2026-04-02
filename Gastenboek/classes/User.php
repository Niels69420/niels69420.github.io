<?php
require_once "database.php";

class User {
    private $db;
    private $cooldownSeconds;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->cooldownSeconds = 7200; // 2 hours in seconds
    }
    
    public function login($username, $password) {
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Start session if not started
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                
                // Store user data in session
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["message_posted"] = false;
                
                $stmt->close();
                return true;
            }
        }
        
        $stmt->close();
        return false;
    }
    
    public function register($username, $password) {
        // Check if username exists
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->close();
            return [false, "Username already exists"];
        }
        
        $stmt->close();
        
        // Create new user
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("ss", $username, $hashed_password);
        
        if ($stmt->execute()) {
            $stmt->close();
            return [true, ""];
        } else {
            $stmt->close();
            return [false, "Registration failed"];
        }
    }
    
    public function checkCooldown($userId) {
        $sql = "SELECT last_message_time FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $last_message_time = $row['last_message_time'] ?? null;
        $stmt->close();
        
        if ($last_message_time === null) {
            return [false, 0];
        }
        
        $current_time = time();
        $last_post_time = strtotime($last_message_time);
        $time_passed = $current_time - $last_post_time;
        
        if ($time_passed < $this->cooldownSeconds) {
            return [true, $this->cooldownSeconds - $time_passed];
        }
        
        return [false, 0];
    }
    
    public function updateLastMessageTime($userId) {
        $sql = "UPDATE users SET last_message_time = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $userId);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    public function logout() {
        // Start session if not started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Unset all session variables
        $_SESSION = [];
        
        // Destroy the session
        session_destroy();
        
        return true;
    }
}