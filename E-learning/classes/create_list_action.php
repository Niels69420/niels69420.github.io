<?php
session_start();
require_once '../classes/database.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/create_list.php');
    exit();
}
// Input validation
if (!isset($_POST['list_name']) || empty($_POST['list_name'])) {
    $_SESSION['error'] = 'List name is required';
    header('Location: ../views/create_list.php');
    exit();
}
if ($_POST['dutch-word'][0] == '' || $_POST['english-word'][0] == '' || !is_array($_POST['dutch-word']) || !is_array($_POST['english-word'])) {
    $_SESSION['error'] = 'You need at least 1 word to create a list';
    header('Location: ../views/create_list.php');
    exit();
}

$dutchWords = $_POST['dutch-word'] ?? [];
$englishWords = $_POST['english-word'] ?? [];
$listName = trim($_POST['list_name']);
$listType = $_POST['list-type'] ?? 'private';

if (count($dutchWords) !== count($englishWords)) {
    $_SESSION['error'] = 'The number of Dutch and English words must match';
    header('Location: ../views/create_list.php');
    exit();
}

try {
    $stmt = $conn->prepare("INSERT INTO WordLists_E_learning (userId, name, public) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $listName, $listType === 'public' ? 1 : 0]);

    $stmt = $conn->prepare("INSERT INTO Words (english, dutch, wordListId) VALUES (?, ?, ?)");
    $listId = $conn->prepare("SELECT id FROM WordLists_E_learning WHERE userId = ? AND name = ? ORDER BY id DESC LIMIT 1");
    $listId->execute([$_SESSION['user_id'], $listName]);
    $listId = $listId->fetch(PDO::FETCH_COLUMN);
    for ($i = 0; $i < count($dutchWords); $i++) {
        $dutch = trim($dutchWords[$i]);
        $english = trim($englishWords[$i]);

        if (!empty($dutch) && !empty($english)) {
            $stmt->execute([$english, $dutch, $listId]);
        }
    }
    $_SESSION['success'] = 'List created successfully';
    header('Location: ../views/home.php');
    exit();
}
catch (PDOException $e) {
    $_SESSION['error'] = 'An error occurred while creating the list. Please try again. ' . $e->getMessage();
    $stmt = $conn->prepare("DELETE FROM WordLists_e_learning WHERE id = ?");
    $stmt->execute([$listId]);
    header('Location: ../views/create_list.php');
    exit();
}
?>