<?php
session_start();

include 'favoriteController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documentId = $_POST['documentId'];

    if (isset($_SESSION['userId'])) {
        $userId = $_SESSION['userId'];

        $favoriteController = new FavoriteController();

        $result = $favoriteController->toggleFavorite($userId, $documentId);

        echo $result;
    } else {
        echo "Error: User not authenticated";
    }
}
?>