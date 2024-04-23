<?php
// Include necessary files and initialize DocumentController
include 'documentController.php';
$documentController = new DocumentController();

// Retrieve search term from AJAX request
$searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

// Fetch document titles based on the search term (for suggestions)
$searchSuggestions = $documentController->searchDocuments($searchTerm);

// Output the search suggestions as HTML response
foreach ($searchSuggestions as $document) {
    echo '<div class="search-suggestion" style ="cursor = pointer;">' . $document['Title'] . '</div>';
}
?>