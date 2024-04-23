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
    public function getDocumentById($documentId)
    {
        $query = "SELECT * FROM document WHERE DocumentId = :documentId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
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
    // public function fetchAllDocumentsForPage($offset, $documentsPerPage)
    // {
    //     $query = "SELECT * FROM document LIMIT :offset, :documentsPerPage";
    //     $stmt = $this->db->prepare($query);
    //     $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    //     $stmt->bindParam(':documentsPerPage', $documentsPerPage, PDO::PARAM_INT);
    //     $stmt->execute();
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
    public function getCountOfDocuments()
    {
        $query = "SELECT COUNT(*) AS documentCount FROM document";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the query executed successfully
        if ($result !== false && isset($result['documentCount'])) {
            return $result['documentCount'];
        } else {
            return 0; // Default value if query fails or no documents found
        }
    }
    public function fetchAllDocumentsForPage($offset, $uploadsForPage, $filter = null)
    {
        // Start building the query
        $query = "SELECT *
              FROM document";

        // Initialize a flag to track if any filter is applied
        $filterApplied = false;

        // Add filter conditions if provided
        if ($filter) {
            $query .= " WHERE 1"; // Start WHERE clause

            // Check if any filter parameters are present
            if (!empty($filter['universityId'])) {
                // Join with the course table to get the university
                $query .= " AND EXISTS (SELECT 1 FROM course c WHERE document.CourseId = c.CourseId AND c.UniversityId = :universityId)";
                $filterApplied = true;
            }
            if (!empty($filter['courseId'])) {
                // Apply course filter
                $query .= " AND document.CourseId = :courseId";
                $filterApplied = true;
            }
            if (!empty($filter['rating'])) {
                // Apply rating filter
                $query .= " AND document.Rating = :rating";
                $filterApplied = true;
            }
        }

        // Add limit and offset to the query
        $query .= " LIMIT :offset, :uploadsForPage";

        // Prepare the query
        $stmt = $this->db->prepare($query);

        // Bind limit and offset parameters
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':uploadsForPage', $uploadsForPage, PDO::PARAM_INT);

        // Bind filter parameters if any filter is applied
        if ($filterApplied) {
            if (!empty($filter['universityId'])) {
                $stmt->bindParam(':universityId', $filter['universityId'], PDO::PARAM_INT);
            }
            if (!empty($filter['courseId'])) {
                $stmt->bindParam(':courseId', $filter['courseId'], PDO::PARAM_INT);
            }
            if (!empty($filter['rating'])) {
                $stmt->bindParam(':rating', $filter['rating'], PDO::PARAM_INT);
            }
        }

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>