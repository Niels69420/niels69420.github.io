<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$list_id = $_GET['id'] ?? null;

if ($list_id === null) {
    header('Location: home.php');
    exit();
}

if ($list_id !== null) {
    require_once '../classes/database.php';
    $stmt = $conn->prepare("SELECT * FROM WordLists_E_learning WHERE id = ?");
    $stmt->execute([$list_id]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$list) {
        header('Location: home.php');
        exit();
    }
}

$_SESSION['error'] ?? null;
$_SESSION['success'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link href="../../tailwind/tailwind.css" rel="stylesheet">
    <title>Practice list</title>
</head>

<body class="font-poppins bg-blue-500">
    <?php include 'layout.php'; ?>
    <?php include '../classes/database.php'; ?>
    <div class="w-full min-h-[calc(100dvh-4rem)] flex flex-row justify-center items-center">
        <div class="flex flex-col justify-center items-center w-1/3 h-64 bg-white rounded-lg shadow">
            <div class="">Select a language that words will appear in.</div>
            <div class="flex flex-row mt-6">
                <a href="practice_list.php?language=dutch&list_id=<?php echo $_GET['id']; ?>&new=1" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600">Dutch</a>
                <a href="practice_list.php?language=english&list_id=<?php echo $_GET['id']; ?>&new=1" class="ml-4 px-6 py-3 bg-pink-500 text-white rounded-lg hover:bg-pink-600">English</a>
            </div>
        </div>
    </div>
</body>

</html>