<?php
session_start();
require_once 'database.php';
$list_userId = null;
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $listId = $_GET['id'];
    $stmt = $conn->prepare("SELECT userId FROM WordLists_E_learning WHERE id = ?");
    $stmt->execute([$listId]);
    $list_userId = $stmt->fetch(PDO::FETCH_COLUMN);
}
if ($_SESSION['user_id'] != $list_userId) {
    $_SESSION['error'] = 'You cannot delete this list as it does not belong to you';
    header('Location: ../views/home.php');
    exit();
}
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = 'No list ID provided';
    header('Location: ../views/home.php');
    exit();
}
$listId = $_GET['id'];
try {
    $stmt = $conn->prepare("DELETE FROM Words WHERE wordListId = ?");
    $stmt->execute([$listId]);
    
    $stmt = $conn->prepare("DELETE FROM WordLists_E_learning WHERE id = ? AND userId = ?");
    $stmt->execute([$listId, $_SESSION['user_id']]);
    $_SESSION['success'] = 'List deleted successfully';
    header('Location: ../views/home.php');
    exit();
} catch (PDOException $e) {
    $_SESSION['error'] = 'An error occurred while deleting the list. Please try again. ' . $e->getMessage();
    header('Location: ../views/home.php');
    exit();
}

?>