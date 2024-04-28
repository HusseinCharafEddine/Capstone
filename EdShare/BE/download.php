<?php
session_start();
if (!isset($_SESSION["userId"])) {
    echo "error";
    exit;
}

include 'downloadController.php';
$downloadController = new DownloadController();
$userId = $_SESSION["userId"]; // Retrieve userId from session
$documentId = $_POST['documentId'];

// Decrement user's token score
$success = $downloadController->addDocumentToDownloads($userId, $documentId);

if ($success) {
    echo "success";
} else {
    echo "error";
}
?>