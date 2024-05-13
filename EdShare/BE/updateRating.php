<?php
session_start();

include 'downloadController.php';

$data = json_decode(file_get_contents('php://input'), true);

if (isset($_SESSION['userId'])) {
    $documentId = $data['documentId'];
    $userId = $_SESSION['userId'];

    $downloadController = new DownloadController();

    $newRating = $data['newRating'];
    $result = $downloadController->updateDocumentRating($userId, $documentId, $newRating);
    echo $result;
} else {
    echo "Error: User not authenticated";
}
?>