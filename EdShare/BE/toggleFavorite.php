<?php
session_start(); // Start the session

// Include your necessary PHP file that contains the `toggleFavorite` function and database connection
include 'favoriteController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have a database connection established in your included file
    $documentId = $_POST['documentId'];

    // Check if 'userId' is set in the session
    if (isset($_SESSION['userId'])) {
        $userId = $_SESSION['userId'];

        $favoriteController = new FavoriteController();

        // Call the toggleFavorite function
        $result = $favoriteController->toggleFavorite($userId, $documentId);

        echo $result; // Return the result to the AJAX request
    } else {
        // Handle case when 'userId' is not set in the session
        echo "Error: User not authenticated"; // or redirect to login page
    }
}
?>