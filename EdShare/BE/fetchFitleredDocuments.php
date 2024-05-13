<?php
session_start();

include 'documentController.php';
$documentController = new DocumentController();

$universityId = isset($_GET['universityId']) ? intval($_GET['universityId']) : 0;
$courseId = isset($_GET['courseId']) ? intval($_GET['courseId']) : 0;
$rating = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
$searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

$uploadsPerPage = 20;
$offset = ($page - 1) * $uploadsPerPage;

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

$filteredDocuments = $documentController->fetchAllDocumentsForPage($offset, $uploadsPerPage, $filter, $searchTerm);

header('Content-Type: application/json');
echo json_encode($filteredDocuments);
?>