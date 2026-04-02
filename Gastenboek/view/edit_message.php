<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include bootstrap file with autoloader
require_once "../includes/bootstrap.php";

// Check if user is logged in
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Define variables and initialize with empty values
$title = $message = "";
$title_err = $message_err = $image_err = "";
$image_path = null;
$original_image = null;
$messageObj = new Message();
$message_id = 0;

// Check if message ID is provided
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Get message ID
    $message_id = trim($_GET["id"]);
    
    // Get the message data
    $result = $messageObj->getUserMessage($message_id, $_SESSION["id"]);
    
    if($result->num_rows == 1){
        // Fetch message data
        $row = $result->fetch_assoc();
        $title = $row["title"];
        $message = $row["message"];
        $original_image = $row["image_path"];
    } else{
        // Message doesn't belong to user or doesn't exist
        header("location: messages.php");
        exit();
    }
} else{
    // No message ID provided
    header("location: messages.php");
    exit();
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate title
    if(empty(trim($_POST["title"]))){
        $title_err = "Please enter a title.";
    } elseif(strlen(trim($_POST["title"])) > 100){
        $title_err = "Title cannot exceed 100 characters.";
    } else{
        $title = trim($_POST["title"]);
    }
    
    // Validate message
    if(empty(trim($_POST["message"]))){
        $message_err = "Please enter your message.";
    } else{
        $message = trim($_POST["message"]);
    }
    
    // Check input errors before updating in database
    if(empty($title_err) && empty($message_err)){
        
        // Update the message
        list($success, $error) = $messageObj->updateMessage(
            $message_id, 
            $_SESSION["id"],
            $title, 
            $message, 
            isset($_FILES['image']) ? $_FILES['image'] : null,
            $original_image,
            isset($_POST['remove_image']) && $_POST['remove_image'] == '1'
        );
        
        if($success){
            // Redirect to messages page
            header("location: messages.php");
            exit();
        } else{
            $image_err = $error;
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

// Prepare current image display if it exists
$currentImageDisplay = '';
if(!empty($original_image)){
    $currentImageDisplay = '<div class="current-image">
        <label>Current Image:</label>
        <img src="' . htmlspecialchars($original_image) . '" alt="Current Image" style="max-width: 150px; max-height: 150px; margin-top: 5px;">
        <div class="remove-image-checkbox">
            <label>
                <input type="checkbox" name="remove_image" value="1"> Remove current image
            </label>
        </div>
    </div>';
}

// Prepare form action URL
$formAction = htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $message_id);

// HTML content with all PHP calculations already performed
$htmlContent = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Message</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/sendmessages.css">
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
            <h1>Edit Message</h1>
        </div>
    </div>

    <div class="flexbox">
        <div class="greybox greybox-first">
            <form action="{$formAction}" method="post" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Title" class="title-input" value="{$title}">
                <span class="error">{$title_err}</span>
                
                <textarea name="message" placeholder="Write your message here...">{$message}</textarea>
                <span class="error">{$message_err}</span>

                <div class="file-upload">
                    <label for="image">Change image (optional):</label>
                    <div class="file-input-container">
                        <input type="file" name="image" id="image" accept="image/*">
                        <div id="imagePreviewContainer">
                            <img id="imagePreview" src="#" alt="Image Preview">
                            <button type="button" id="removeImage" class="remove-image-btn">×</button>
                        </div>
                    </div>
                    
                    {$currentImageDisplay}
                    
                    <span class="error">{$image_err}</span>
                </div>

                <div class="form-buttons">
                    <a href="messages.php" class="cancel-btn">Cancel</a>
                    <input type="submit" class="blue_small" value="Save Changes">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
HTML;

// Output the complete HTML
echo $htmlContent;
?>