<?php
include 'common/commonFunctions.php';

class DocumentController
{

    private $db;

    // Constructor to initialize database connection
    public function __construct()
    {
        $this->db = DBConnect();
    }

    // Fetch uploads for a specific page
    public function fetchDocumentsForPage($UserId, $offset, $documentsPerPage)
    {
        $query = "SELECT * FROM document WHERE UserId = :UserId LIMIT :offset, :documentsPerPage";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':UserId', $UserId, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':documentsPerPage', $documentsPerPage, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDocumentById($documentId)
    {
        $query = "SELECT * FROM document WHERE DocumentId = :documentId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>