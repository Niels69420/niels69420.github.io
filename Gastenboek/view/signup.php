<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include bootstrap file with autoloader
require_once "../includes/bootstrap.php";

// Define variables and initialize with empty values
$username_err = $password_err = $confirm_password_err = "";
$username = $password = $confirm_password = "";
$userObj = new User();
$showForm = true;

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else {
        $username = trim($_POST["username"]);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["passwordcon"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["passwordcon"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        // Call register method
        list($success, $error) = $userObj->register($username, $password);
        
        if ($success) {
            // Redirect to login page
            header("location: login.php");
            exit;
        } else {
            $username_err = $error;
        }
    }
}

// Prepare navigation elements based on login status
$navItems = '';
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    $navItems = '<span>Welcome, ' . htmlspecialchars($_SESSION["username"]) . '</span>
                <li>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </li>';
} else {
    $navItems = '<li><a href="login.php">Log in</a></li>';
}

// Prepare form action URL
$formAction = htmlspecialchars($_SERVER["PHP_SELF"]);

// Prepare username field with value and error message
$usernameField = '<input type="text" placeholder="Username" name="username" class="input_box" value="' . $username . '" required>';
$usernameError = '<span class="error">' . $username_err . '</span>';

// Prepare password field with error message
$passwordField = '<input type="password" placeholder="Password" name="password" class="input_box" required>';
$passwordError = '<span class="error">' . $password_err . '</span>';

// Prepare confirm password field with error message
$confirmPasswordField = '<input type="password" placeholder="Confirm password" name="passwordcon" class="input_box" required>';
$confirmPasswordError = '<span class="error">' . $confirm_password_err . '</span>';

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
    <link rel="stylesheet" href="../css/signup.css">
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
        <div class="bluebox bluebox-signup">
            <h1>Sign up</h1>
        </div>
    </div>
    
    <div class="flexbox">
        <div class="greybox greybox-first">
            <form action="{$formAction}" method="post">
                {$usernameField}
                {$usernameError}
                
                {$passwordField}
                {$passwordError}
                
                {$confirmPasswordField}
                {$confirmPasswordError}
                
                <input type="submit" class="blue_small" value="Sign up">
            </form>
        </div>
    </div>
</body>
</html>
HTML;

// Output the complete HTML
echo $htmlContent;
?>