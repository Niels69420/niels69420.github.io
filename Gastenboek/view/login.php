<?php
// Include bootstrap file with autoloader
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Correct path from view/login.php to includes/bootstrap.php
require_once "../includes/bootstrap.php";

// Check if the user is already logged in, if yes then redirect to messages page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: messages.php");
    exit;
}
 
$userObj = new User();
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $login_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        if($userObj->login($username, $password)) {
            // Login successful, redirect to messages page
            header("location: messages.php");
            exit;
        } else {
            // Login failed
            $login_err = "Invalid username or password.";
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

// Prepare login error message if exists
$loginErrorDisplay = '';
if(!empty($login_err)){
    $loginErrorDisplay = '<div class="error">' . $login_err . '</div>';
}

// Prepare username field with value and error message
$usernameField = '<input type="text" placeholder="Username" name="username" class="input_box" value="' . $username . '">';
$usernameError = '<span class="error">' . $username_err . '</span>';

// Prepare password field with error message
$passwordField = '<input type="password" placeholder="Password" name="password" class="input_box">';
$passwordError = '<span class="error">' . $password_err . '</span>';

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
    <link rel="stylesheet" href="../css/login.css">
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
            <h1>Log in</h1>
        </div>
    </div>
    
    <div class="flexbox">
        <div class="greybox greybox-first">
            {$loginErrorDisplay}
            <form action="{$formAction}" method="post">
                {$usernameField}
                {$usernameError}
                
                {$passwordField}
                {$passwordError}
                
                <input type="submit" class="blue_small" value="Log in">
            </form>
        </div>
    </div>
    <div class="flexbox">
        <a href="signup.php">
            <div class="greybox greybox-second">
                <div class="infobox">
                    Don't have an account yet?
                    <br>
                    Sign up here!
                </div>
            </div>
        </a>
    </div>
</body>
</html>
HTML;

// Output the complete HTML
echo $htmlContent;
?>