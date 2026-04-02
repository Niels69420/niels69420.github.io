<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include bootstrap file with autoloader
require_once "../includes/bootstrap.php";

$title_err = $message_err = $image_err = $cooldown_err = "";
$title = $message = "";
$login_required = false;
$cooldown_active = false;
$time_remaining = 0;

// Initialize classes
$messageObj = new Message();
$userObj = new User();

// Check if user is logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Check cooldown
    list($cooldown_active, $time_remaining) = $userObj->checkCooldown($_SESSION["id"]);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        $login_required = true;
    } elseif($cooldown_active) {
        $cooldown_err = "You need to wait " . gmdate("H:i:s", $time_remaining) . " before posting again.";
    } else {
        // User is logged in and not in cooldown, process the message
        
        // Validate title
        if(empty(trim($_POST["title"]))) {
            $title_err = "Please enter a title.";
        } elseif(strlen(trim($_POST["title"])) > 100) {
            $title_err = "Title cannot exceed 100 characters.";
        } else {
            $title = trim($_POST["title"]);
        }
        
        // Validate message
        if(empty(trim($_POST["message"]))) {
            $message_err = "Please enter your message.";
        } else {
            $message = trim($_POST["message"]);
        }

        // Check input errors before inserting in database
        if (empty($title_err) && empty($message_err)) {
            // Create the message
            list($success, $error, $path) = $messageObj->createMessage(
                $_SESSION["id"],
                $title,
                $message,
                isset($_FILES['image']) ? $_FILES['image'] : null
            );
            
            if ($success) {
                // Redirect to messages page
                header("location: messages.php");
                exit;
            } else {
                $image_err = $error;
            }
        }
    }
}

// Fetch all messages with usernames
$result = $messageObj->getAllMessages();

// Prepare navigation elements based on login status
$navItems = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true
    ? '<span>Welcome, ' . htmlspecialchars($_SESSION["username"]) . '</span>
       <li><a href="../logout.php" class="logout-btn">Logout</a></li>'
    : '<li><a href="login.php">Log in</a></li>';

// Process message content
$messagesContent = '';
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $is_owner = isset($_SESSION["id"]) && $_SESSION["id"] == $row["user_id"];
        
        $messagesContent .= '<div class="message">
                <div class="message-header">
                    <span>' . htmlspecialchars($row["username"]) . '</span>
                    <span>' . htmlspecialchars($row["created_at"]) . '</span>
                </div>';
                
        if($is_owner) {
            $messagesContent .= '<div class="message-actions">
                    <a href="edit_message.php?id=' . $row["id"] . '" class="edit-btn">Edit</a>
                    <a href="javascript:void(0);" onclick="confirmDelete(' . $row["id"] . ')" class="delete-btn">Delete</a>
                  </div>';
        }
        
        $messagesContent .= '<div class="message-title">' . htmlspecialchars($row["title"]) . '</div>';
        
        $messagesContent .= '<div class="message-content">
                    ' . nl2br(htmlspecialchars($row["message"])) . '
                </div>';
        
        // Display image if exists
        if(!empty($row["image_path"])) {
            $messagesContent .= '<div class="message-image">
                    <img src="' . htmlspecialchars($row["image_path"]) . '" alt="Message Image">
                  </div>';
        }
        
        $messagesContent .= '</div>';
    }
} else {
    $messagesContent = '<p class="welcome-message">No messages yet. Be the first to post!</p>';
}

// HTML content with all PHP calculations already performed
$htmlContent = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastenboek</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/messages.css">
    <script src="../javascript/javascript.js"></script>
</head>
<body>
    <nav>
        <button class="hamburger" aria-label="Toggle navigation menu">≡</button>
        <img src="../images/image.png" class="book">
        <ul>
        {$navItems}
            <li><a href="messages.php">Messages</a></li>
            <li><a href="sendmessage.php">Send a message</a></li>
        </ul>
    </nav>

    <div class="flexbox">
        <div class="bluebox">
            <h1>Messages</h1>
        </div>
    </div>

    <div class="message-container">
        {$messagesContent}
    </div>
    
    <!-- Custom modal for delete confirmation -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-container">
            <h3>Delete Confirmation</h3>
            <p>Are you sure you want to delete this message?</p>
            <div class="modal-buttons">
                <button class="modal-cancel-btn" id="cancelDelete">Cancel</button>
                <button class="modal-delete-btn" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
    <script>
        // Wait until DOM is fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Custom delete confirmation modal
            let messageIdToDelete = null;
            const modal = document.getElementById('deleteModal');
            const cancelBtn = document.getElementById('cancelDelete');
            const confirmBtn = document.getElementById('confirmDelete');
            
            // Make the confirmDelete function available globally
            window.confirmDelete = function(id) {
                // Store the message ID to delete
                messageIdToDelete = id;
                
                // Display the modal
                modal.style.display = 'flex';
            };
            
            // When the user clicks cancel, close the modal
            cancelBtn.addEventListener('click', function() {
                modal.style.display = 'none';
                messageIdToDelete = null;
            });
            
            // When the user clicks delete, confirm deletion
            confirmBtn.addEventListener('click', function() {
                if (messageIdToDelete) {
                    window.location.href = "../delete_message.php?id=" + messageIdToDelete;
                }
                modal.style.display = 'none';
            });
            
            // Close modal if user clicks outside of it
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                    messageIdToDelete = null;
                }
            });
        });
    </script>
</body>
</html>
HTML;

// Output the complete HTML
echo $htmlContent;
?>