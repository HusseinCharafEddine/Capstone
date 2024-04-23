<?php
include 'common/commonFunctions.php';

class FavoriteController
{

    private $db;

    // Constructor to initialize database connection
    public function __construct()
    {
        $this->db = DBConnect();
    }

    // Fetch uploads for a specific page
    public function fetchFavorites($userId, $offset, $documentsPerPage, $filter = null)
    {
        $query = "SELECT d.*
                  FROM favorite f
                  JOIN document d ON f.DocumentId = d.DocumentId
                  WHERE f.UserId = :userId";

        // Apply filter conditions if provided
        if ($filter) {
            if (!empty($filter['universityId'])) {
                $query .= " AND d.CourseId IN (SELECT CourseId FROM course WHERE UniversityId = :universityId)";
            }
            if (!empty($filter['courseId'])) {
                $query .= " AND d.CourseId = :courseId";
            }
            if (!empty($filter['rating'])) {
                $query .= " AND d.Rating = :rating";
            }
        }

        $query .= " LIMIT :offset, :documentsPerPage";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':documentsPerPage', $documentsPerPage, PDO::PARAM_INT);

        // Bind filter parameters
        if ($filter) {
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
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getFavoriteByUserIdAndDocumentId($userId, $documentId)
    {
        $query = "SELECT * FROM favorite WHERE UserId = :userId AND DocumentId = :documentId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();
        $favorite = $stmt->fetch(PDO::FETCH_ASSOC);

        return $favorite; // Returns the favorite record if found, or NULL if not favorited
    }

    public function toggleFavorite($userId, $documentId)
    {

        $existingFavorite = $this->getFavoriteByUserIdAndDocumentId($userId, $documentId);

        // If favorite exists, remove it; otherwise, add it
        if ($existingFavorite) {
            $deleteQuery = "DELETE FROM favorite WHERE UserId = :userId AND DocumentId = :documentId";
            $deleteStmt = $this->db->prepare($deleteQuery);
            $deleteStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $deleteStmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
            $deleteStmt->execute();

            return "Favorite removed successfully";
        } else {
            $insertQuery = "INSERT INTO favorite (UserId, DocumentId) VALUES (:userId, :documentId)";
            $insertStmt = $this->db->prepare($insertQuery);
            $insertStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $insertStmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
            $insertStmt->execute();

            return "Favorite added successfully";
        }
    }

}
?>