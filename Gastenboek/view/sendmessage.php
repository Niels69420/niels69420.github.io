<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include bootstrap file with autoloader
require_once "../includes/bootstrap.php";

$title_err = $message_err = $image_err = $cooldown_err = $session_err = "";
$title = $message = "";
$login_required = false;
$cooldown_active = false;
$time_remaining = 0;
$session_posted = false;

// Initialize classes
$messageObj = new Message();
$userObj = new User();

// Check if user is logged in and initialize session message flag if needed
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    // Initialize message_posted flag if it doesn't exist
    if(!isset($_SESSION["message_posted"])) {
        $_SESSION["message_posted"] = false;
    } else {
        $session_posted = $_SESSION["message_posted"];
    }
    
    // Check cooldown
    list($cooldown_active, $time_remaining) = $userObj->checkCooldown($_SESSION["id"]);
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
        $login_required = true;
    } elseif($session_posted) {
        // User has already posted a message in this session
        $session_err = "You can only post one message per session. Please log out and log back in to post another message.";
    } elseif($cooldown_active) {
        $cooldown_err = "You need to wait " . gmdate("H:i:s", $time_remaining) . " before posting again.";
    } else {
        // User is logged in, not in cooldown, and hasn't posted in this session
        
        // Validate title
        if(empty(trim($_POST["title"]))) {
            $title_err = "Please enter a title.";
        } elseif(strlen(trim($_POST["title"])) > 100) {
            $title_err = "Title cannot exceed 100 characters.";
        } else {
            $title = trim($_POST["title"]);
        }
        
        // Validate message
        if (empty(trim($_POST["message"]))) {
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
                // Set the session flag to indicate user has posted a message
                $_SESSION["message_posted"] = true;
                
                // Redirect to messages page
                header("location: messages.php");
                exit;
            } else {
                $image_err = $error;
            }
        }
    }
}

// Prepare navigation elements based on login status
$navItems = '';
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $navItems = '<span>Welcome, ' . htmlspecialchars($_SESSION["username"]) . '</span>
                <li>
                    <a href="../logout.php" class="logout-btn">Logout</a>
                </li>';
} else {
    $navItems = '<li><a href="login.php">Log in</a></li>';
}

// Prepare form content based on various conditions
$formContent = '';
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    $formContent = '<div class="login-alert">
                    You must be <a href="login.php">logged in</a> to send messages!
                </div>';
} elseif($session_posted) {
    $formContent = '<div class="session-limit-alert">
                    <p>You have already posted a message in this session.</p>
                    <p>To post another message, please <a href="../logout.php">log out</a> and log back in.</p>
                </div>';
} elseif($cooldown_active) {
    $formContent = '<div class="cooldown-alert">
                    <p>You need to wait before posting again.</p>
                    <p id="countdown">' . gmdate("H:i:s", $time_remaining) . '</p>
                    <script>
                        // Set up a countdown timer
                        let timeRemaining = ' . $time_remaining . ';
                        const countdownElement = document.getElementById("countdown");
                        
                        const countdownTimer = setInterval(function() {
                            timeRemaining--;
                            
                            if (timeRemaining <= 0) {
                                clearInterval(countdownTimer);
                                location.reload(); // Reload the page when countdown reaches zero
                            } else {
                                // Format time as HH:MM:SS
                                let hours = Math.floor(timeRemaining / 3600);
                                let minutes = Math.floor((timeRemaining % 3600) / 60);
                                let seconds = timeRemaining % 60;
                                
                                countdownElement.textContent = 
                                    (hours < 10 ? "0" + hours : hours) + ":" +
                                    (minutes < 10 ? "0" + minutes : minutes) + ":" +
                                    (seconds < 10 ? "0" + seconds : seconds);
                            }
                        }, 1000);
                    </script>
                </div>';
} else {
    $cooldownErrDisplay = !empty($cooldown_err) ? '<span class="error">' . $cooldown_err . '</span>' : '';
    $sessionErrDisplay = !empty($session_err) ? '<span class="error">' . $session_err . '</span>' : '';
    $imageErrDisplay = isset($image_err) ? '<span class="error">' . $image_err . '</span>' : '';
    
    $formContent = '<form action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post" enctype="multipart/form-data">
                    <input type="text" name="title" placeholder="Enter message title" class="title-input" value="' . $title . '">
                    <span class="error">' . $title_err . '</span>
                    
                    <textarea name="message" placeholder="Write your message here...">' . $message . '</textarea>
                    <span class="error">' . $message_err . '</span>
                    
                    ' . $cooldownErrDisplay . '
                    ' . $sessionErrDisplay . '

                    <div class="file-upload">
                        <label for="image">Add an image (optional):</label>
                        <div class="file-input-container">
                            <input type="file" name="image" id="image" accept="image/*">
                            <div id="imagePreviewContainer">
                                <img id="imagePreview" src="#" alt="Image Preview">
                                <button type="button" id="removeImage" class="remove-image-btn">×</button>
                            </div>
                        </div>
                        ' . $imageErrDisplay . '
                    </div>

                    <input type="submit" class="blue_small" value="Post Message">
                </form>';
}

// Script for login alert
$loginAlertScript = $login_required ? '<script>window.onload = function() { alert("You must be logged in to send messages!"); }</script>' : '';

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
    <link rel="stylesheet" href="../css/sendmessages.css">
    <script src="../javascript/javascript.js"></script>
    {$loginAlertScript}
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
            <h1>Send a Message</h1>
        </div>
    </div>

    <div class="flexbox">
        <div class="greybox greybox-first">
            {$formContent}
        </div>
    </div>
</body>
</html>
HTML;

// Output the complete HTML
echo $htmlContent;
?>