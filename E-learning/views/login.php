<?php
session_start();

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$attempts = $_SESSION['login_attempts'] ?? 0;
$attemptsleft = max(0, 5 - $attempts);
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="../../tailwind/tailwind.css" rel="stylesheet">
</head>

<body class="flex flex-row font-poppins font-semibold">
    <div class="absolute left-0 w-1/2 inset-0 overflow-hidden pointer-events-none">
        <div class="floating-word left-[10%] text-pink-400" style="animation-delay: 0s; animation-duration: 14s;" >Innovation</div>
        <div class="floating-word left-[25%] text-pink-400" style="animation-delay: 4s; animation-duration: 13s;">Future</div>
        <div class="floating-word left-[40%] text-pink-400" style="animation-delay: 10s; animation-duration: 16s;">Design</div>
        <div class="floating-word left-[60%] text-pink-400" style="animation-delay: 6s; animation-duration: 13s;">Technology</div>
        <div class="floating-word left-[75%] text-pink-400" style="animation-delay: 7s; animation-duration: 18s;">Creativity</div>
        <div class="floating-word left-[50%] text-pink-400" style="animation-delay: 2s; animation-duration: 20s;">Learning</div>
    </div>
    <div class="flex justify-center items-center flex-col bg-blue-500 w-1/2 h-screen">
        <div class="z-10 bg-blue-500 w-auto max-w-sm h-auto p-3 shadow-lg text-white rounded-lg">
            <img src="../public/images/books.png" />
        </div>
        <div class="z-10 text-3xl text-white mt-8 font-bold">Learn lightning fast with <span class="text-pink-400">BEK</span></div>
        <div class="z-10 max-w-sm text-center text-lg text-white opacity-90 mt-6 font-light">The most efficient way to learn Dutch and English vocabulary</div>
    </div>
    <div class="flex flex-wrap z-10 flex-col justify-between content-center w-1/2 h-screen py-28">
        <div class="text-xl flex flex-col">
            <div class="flex flex-row items-center">
                <i class='bx bx-book-open bg-pink-400 p-2 rounded-lg mr-2 text-white'></i>
                <p class="font-bold">BEK</p>
            </div>
            <?php if (!empty($error)): ?>
            <div class="text-sm text-red-500 mt-2 font-medium">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            <?php if ($attempts > 0): ?>
            <div class="text-sm text-red-500 mt-2 font-medium">
                <?php echo "You have $attemptsleft attempts left."; ?>
            </div>
            <?php endif; ?>
            <div class="text-2xl text-black">
                Welcome back!
            </div>
            <div class="mt-2 text-sm font-normal text-gray-500">
                Please enter your details to login to your account
            </div>
            <div class="mt-8 border border-bottom border-gray-300"></div>
        </div>
        <div class="w-2/3 flex flex-col justify-center">
            <form action="../classes/login_process.php" method="POST">
            <div class="text-[15px] font-medium mb-2">Username</div>
            <div class="flex flex-row items-center justify-center mb-4 px-2 bg-gray-100 border border-gray-300 rounded-lg">
                <i class='bx bx-user text-gray-500 text-xl'></i>
                <input name="username" id="username" type="text" placeholder="John, Jane, etc" class="font-medium text-gray-500 w-full h-full p-2 focus:outline-none" />
            </div>
            <div class="text-[15px] font-medium mb-2">Password</div>
            <div class="flex flex-row items-center justify-center mb-4 px-2 bg-gray-100 border border-gray-300 rounded-lg mb-8">
                <i class='bx bx-lock text-gray-500 text-xl'></i>
                <input name="password" id="password" type="password" placeholder="Password" class="font-medium text-gray-500 w-full h-full p-2 focus:outline-none" />
            </div>
            <button class="hover:cursor-pointer hover:bg-pink-500 bg-pink-400 p-2 rounded-lg w-full text-white text-center" type="submit">Login</button>
        </div>
        </form>
        <div class="text-gray-500 text-sm font-normal">Don't have an account yet? <a href="../views/register.php" class="text-blue-400">Sign up here for free!</a></div>
    </div>
</body>
</html>
