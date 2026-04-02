<?php
// Initialize the session
session_start();

// Include bootstrap file with autoloader
require_once "includes/bootstrap.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: view/login.php");
    exit;
}

// Check if message ID is provided
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get message ID
    $message_id = trim($_GET["id"]);
    
    // Create message object
    $messageObj = new Message();
    
    // Attempt to delete the message
    if($messageObj->deleteMessage($message_id, $_SESSION["id"])){
        // Success - redirect to messages page in the view directory
        header("location: /Gastenboek/view/messages.php");
        exit();
    } else {
        // Failure or message not found - redirect to messages page in the view directory
        header("location: /Gastenboek/view/messages.php");
        exit();
    }
} else{
    // No message ID provided - redirect to messages page in the view directory
    header("location: /Gastenboek/view/messages.php");
    exit();
}
?>