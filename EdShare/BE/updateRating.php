<?php
session_start(); // Start the session

// Include the necessary PHP file that contains the `updateDocumentRating` function and database connection
include 'downloadController.php';

// Get the JSON data from the request body
$data = json_decode(file_get_contents('php://input'), true);

// Check if 'userId' is set in the session
if (isset($_SESSION['userId'])) {
    // Assuming you have a database connection established in your included file
    $documentId = $data['documentId'];
    $userId = $_SESSION['userId'];

    $downloadController = new DownloadController();

    // Call the updateDocumentRating function
    $newRating = $data['newRating'];
    $result = $downloadController->updateDocumentRating($userId, $documentId, $newRating);
    echo $result; // Return the result to the AJAX request
} else {
    // Handle case when 'userId' is not set in the session
    echo "Error: User not authenticated"; // or redirect to login page
}
?>