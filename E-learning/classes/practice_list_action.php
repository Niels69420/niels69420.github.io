<?php 
require_once '../classes/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $stmt = $conn->prepare("SELECT dutch, english FROM Words WHERE wordListId = ?");
    $stmt ->execute([$list_id]);
    $words = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$words) {
        $_SESSION['error'] = 'List is empty, cannot practice an empty list.';
        header('Location: home.php');
        exit();
    }

} catch (PDOException $e) {
    $_SESSION['error'] = 'An error occurred while fetching word lists. Please try again. ' . $e->getMessage();
    header('Location: home.php');
    exit();
}
?>