<?php
include 'documentController.php';
$documentController = new DocumentController();

$searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

$searchSuggestions = $documentController->searchDocuments($searchTerm);

echo '<style>
        
        .search-suggestion {
            padding: 8px 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width:100%;
        }

        .search-suggestion:hover {
            background-color: #f0f0f0;
        }
    </style>';

foreach ($searchSuggestions as $document) {
    echo '<div class="search-suggestion">' . $document['Title'] . '</div>';
}
?>