<?php
session_start();
require_once 'database.php';
$list_userId = null;
$listId = $_GET['id'];
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $stmt = $conn->prepare("SELECT userId FROM WordLists_E_learning WHERE id = ?");
    $stmt->execute([$listId]);
    $list_userId = $stmt->fetch(PDO::FETCH_COLUMN);
}
if ($_SESSION['user_id'] != $list_userId) {
    $_SESSION['error'] = 'You cannot edit this list as it does not belong to you';
    header('Location: ../views/home.php');
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'Something went wrong. Please try again.';
    header('Location: ../views/home.php');
    exit();
}

$dutchWords = $_POST['dutch-word'] ?? [];
$englishWords = $_POST['english-word'] ?? [];
$listName = trim($_POST['list_name']);
$listType = $_POST['list-type'] ?? 'private';
try {
    $stmt = $conn->prepare("SELECT name, public FROM WordLists_E_learning WHERE id = ? AND userId = ?");
    $stmt->execute([$listId, $_SESSION['user_id']]);
    $list = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $conn->prepare("SELECT dutch, english FROM Words WHERE wordListId = ?");
    $stmt->execute([$listId]);
    $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("UPDATE WordLists_E_learning SET name = ?, public = ? WHERE id = ? AND userId = ?");
    $stmt->execute([$listName, $listType === 'public' ? 1 : 0, $listId, $_SESSION['user_id']]);

    $stmt = $conn->prepare("DELETE FROM Words WHERE wordListId = ?");
    $stmt->execute([$listId]);
    $stmt = $conn->prepare("INSERT INTO Words (english, dutch, wordListId) VALUES (?, ?, ?)");
    for ($i = 0; $i < count($dutchWords); $i++) {
        $dutch = trim($dutchWords[$i]);
        $english = trim($englishWords[$i]);

        if (!empty($dutch) && !empty($english)) {
            $stmt->execute([$english, $dutch, $listId]);
        }
    }

    if (!$list) {
        $_SESSION['error'] = 'List not found, list is corrupted.';
        header('Location: ../views/home.php');
        exit();
    }
    $_SESSION['success'] = 'List updated successfully';
    header('Location: ../views/home.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = 'An error occurred while fetching the list details. Please try again. ' . $e->getMessage();
    header('Location: ../views/home.php');
    exit();
}



?>