<?php
// Start the session (if not already started)
session_start();

// Include necessary files and initialize DocumentController
include 'documentController.php';
$documentController = new DocumentController();

// Retrieve filter values from AJAX request
$universityId = isset($_GET['universityId']) ? intval($_GET['universityId']) : 0;
$courseId = isset($_GET['courseId']) ? intval($_GET['courseId']) : 0;
$rating = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
$searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate offset and uploads per page
$uploadsPerPage = 20; // Adjust this value as needed
$offset = ($page - 1) * $uploadsPerPage;

// Prepare filter array based on provided parameters
$filter = array();
if ($universityId != 0) {
    $filter['universityId'] = $universityId;
}
if ($courseId != 0) {
    $filter['courseId'] = $courseId;
}
if ($rating != 0) {
    $filter['rating'] = $rating;
}

// Fetch filtered documents based on filter values and search term
$filteredDocuments = $documentController->fetchAllDocumentsForPage($offset, $uploadsPerPage, $filter, $searchTerm);

// Output the filtered documents as JSON response
header('Content-Type: application/json');
echo json_encode($filteredDocuments);
?>