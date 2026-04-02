<?php
require_once '../classes/database.php';
function getWordListsByUserId()
{
    global $conn;
    $userId = $_SESSION['user_id'] ?? null;
    $publicList = false;
    $sql = "SELECT w.id, w.userId, w.name, w.public, COUNT(Words.id) as word_count 
            FROM WordLists_E_learning w
            LEFT JOIN Words ON w.id = Words.wordListId
            WHERE w.userId = :userId AND w.public = :publicList
            GROUP BY w.id, w.userId, w.name, w.public";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['userId' => $userId, 'publicList' => $publicList]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}
function getPublicWordLists()
{
    global $conn;
    $publicList = true;
    $sql = "SELECT w.id, w.userId, w.name, w.public, COUNT(Words.id) as word_count 
            FROM WordLists_E_learning w
            LEFT JOIN Words ON w.id = Words.wordListId
            WHERE w.public = :publicList
            GROUP BY w.id, w.userId, w.name, w.public";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['publicList' => $publicList]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllCreators()
{
    global $conn;
    $sql = "SELECT id, username FROM Users_E_learning";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[$row['id']] = $row['username'];
    }
    return $users;
}

try {
    $userId = $_SESSION['user_id'];
    
    $privateLists = getWordListsByUserId();
    $publicLists = getPublicWordLists();
    $creators = getAllCreators();
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}
?>