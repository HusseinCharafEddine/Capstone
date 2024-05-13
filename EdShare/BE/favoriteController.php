<?php
include 'common/commonFunctions.php';

class FavoriteController
{

    private $db;

    public function __construct()
    {
        $this->db = DBConnect();
    }

    public function fetchFavorites($userId, $offset, $documentsPerPage, $filter = null, $searchTerm = null)
    {
        $query = "SELECT d.*
                  FROM favorite f
                  JOIN document d ON f.DocumentId = d.DocumentId
                  WHERE f.UserId = :userId";

        $filterApplied = false;

        if ($filter) {
            if (!empty($filter['universityId'])) {
                $query .= " AND d.CourseId IN (SELECT CourseId FROM course WHERE UniversityId = :universityId)";
                $filterApplied = true;
            }
            if (!empty($filter['courseId'])) {
                $query .= " AND d.CourseId = :courseId";
                $filterApplied = true;
            }
            if (!empty($filter['rating'])) {
                $query .= " AND d.Rating = :rating";
                $filterApplied = true;
            }
        }

        if ($searchTerm !== null && trim($searchTerm) !== '') {
            $query .= " AND (d.Title LIKE :searchTerm OR d.Category LIKE :searchTerm)";
        }

        $query .= " LIMIT :offset, :documentsPerPage";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':documentsPerPage', $documentsPerPage, PDO::PARAM_INT);

        if (!empty($filter['universityId'])) {
            $stmt->bindParam(':universityId', $filter['universityId'], PDO::PARAM_INT);
        }
        if (!empty($filter['courseId'])) {
            $stmt->bindParam(':courseId', $filter['courseId'], PDO::PARAM_INT);
        }
        if (!empty($filter['rating'])) {
            $stmt->bindParam(':rating', $filter['rating'], PDO::PARAM_INT);
        }

        if ($searchTerm !== null && trim($searchTerm) !== '') {
            $searchParam = '%' . $searchTerm . '%';
            $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
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

        return $favorite;
    }

    public function toggleFavorite($userId, $documentId)
    {

        $existingFavorite = $this->getFavoriteByUserIdAndDocumentId($userId, $documentId);

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

    public function searchDocuments($UserId, $searchTerm)
    {
        $query = "SELECT d.*
        FROM document d
        INNER JOIN favorite f ON d.DocumentId = f.DocumentId
        WHERE (d.Title LIKE :searchTerm OR d.Category LIKE :searchTerm)
          AND f.UserId = :UserId";

        $stmt = $this->db->prepare($query);

        $searchParam = '%' . $searchTerm . '%';
        $stmt->bindParam(':UserId', $UserId, PDO::PARAM_INT);
        $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);


        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
?>