<?php
include 'common/commonFunctions.php';

class DocumentController
{

    private $db;

    public function __construct()
    {
        $this->db = DBConnect();
    }

    public function getDocumentById($documentId)
    {
        $query = "SELECT * FROM document WHERE DocumentId = :documentId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function fetchDocumentsForPage($UserId, $offset, $documentsPerPage, $searchTerm = null)
    {
        $query = "SELECT * FROM document WHERE UserId = :UserId";

        if (!empty($searchTerm)) {
            $query .= " AND Title LIKE :searchTerm";
        }

        $query .= " LIMIT :offset, :documentsPerPage";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':UserId', $UserId, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':documentsPerPage', $documentsPerPage, PDO::PARAM_INT);

        if (!empty($searchTerm)) {
            $searchTerm = "%{$searchTerm}%";
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
        }

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

        if ($result !== false && isset($result['documentCount'])) {
            return $result['documentCount'];
        } else {
            return 0;
        }
    }
    public function fetchAllDocumentsForPage($offset, $uploadsForPage, $filter = null, $searchTerm = null)
    {
        $query = "SELECT *
              FROM document";

        $filterApplied = false;

        if ($filter) {
            $query .= " WHERE 1";

            if (!empty($filter['universityId'])) {
                $query .= " AND EXISTS (SELECT 1 FROM course c WHERE document.CourseId = c.CourseId AND c.UniversityId = :universityId)";
                $filterApplied = true;
            }
            if (!empty($filter['courseId'])) {
                $query .= " AND document.CourseId = :courseId";
                $filterApplied = true;
            }
            if (!empty($filter['rating'])) {
                $query .= " AND document.Rating = :rating";
                $filterApplied = true;
            }
        }

        if ($searchTerm !== null && trim($searchTerm) !== '') {
            if ($filterApplied) {
                $query .= " AND";
            } else {
                $query .= " WHERE";
            }
            $query .= " (Title LIKE :searchTerm OR Category LIKE :searchTerm)";
        }

        $query .= " LIMIT :offset, :uploadsForPage";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':uploadsForPage', $uploadsForPage, PDO::PARAM_INT);

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

        if ($searchTerm !== null && trim($searchTerm) !== '') {
            $searchParam = '%' . $searchTerm . '%';
            $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function fetchAllDocumentsForUser($userId, $offset, $uploadsForPage, $filter = null)
    {
        $query = "SELECT d.*, COUNT(dl.DocumentId) AS downloadCount
                  FROM document d
                  LEFT JOIN downloaded dl ON d.DocumentId = dl.DocumentId
                  WHERE d.UserId = :userId";

        $filterApplied = false;

        if ($filter) {
            if (!empty($filter['universityId'])) {
                $query .= " AND EXISTS (SELECT 1 FROM course c WHERE d.CourseId = c.CourseId AND c.UniversityId = :universityId)";
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

        $query .= " GROUP BY d.DocumentId";

        $query .= " ORDER BY downloadCount DESC";

        $query .= " LIMIT :offset, :uploadsForPage";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':uploadsForPage', $uploadsForPage, PDO::PARAM_INT);

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

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getDocumentCountByUserId($userId)
    {
        $query = "SELECT COUNT(*) AS documentCount FROM document WHERE UserId = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result !== false && isset($result['documentCount'])) {
            return $result['documentCount'];
        } else {
            return 0;
        }
    }


    public function getTotalDocumentsByUserId($userId)
    {
        $query = "SELECT COUNT(*) AS totalDocuments
                  FROM document
                  WHERE UserId = :userId";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result !== false && isset($result['totalDocuments'])) ? $result['totalDocuments'] : 0;
    }

    public function getTotalDocumentsThisWeekByUserId($userId)
    {
        $thisWeekStartDate = date('Y-m-d', strtotime('this week'));

        $query = "SELECT COUNT(*) AS totalDocumentsThisWeek
              FROM document
              WHERE UserId = :userId
              AND DATE_FORMAT(Date, '%Y-%m-%d') > :startDate ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $thisWeekStartDate, PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result !== false && isset($result['totalDocumentsThisWeek'])) ? $result['totalDocumentsThisWeek'] : 0;
    }


    public function getTotalDocumentsLastWeekByUserId($userId)
    {
        $lastWeekStartDate = date('Y-m-d', strtotime('-14 days'));
        $lastWeekEndDate = date('Y-m-d', strtotime('-7 days'));
        $query = "SELECT COUNT(*) AS totalDocumentsLastWeek
                  FROM document d
                  WHERE d.UserId = :userId
                  AND DATE_FORMAT(d.Date, '%Y-%m-%d') BETWEEN :startDate AND :endDate";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $lastWeekStartDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $lastWeekEndDate, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result !== false && isset($result['totalDocumentsLastWeek'])) ? $result['totalDocumentsLastWeek'] : 0;
    }


    public function computeDocumentGrowthPercentage($userId)
    {
        $DocumentsLastWeek = $this->getTotalDocumentsLastWeekByUserId($userId);
        $DocumentsThisWeek = $this->getTotalDocumentsThisWeekByUserId($userId);

        if ($DocumentsLastWeek == 0) {
            return ($DocumentsThisWeek > 0) ? 100 : 0;
        }

        $growthPercentage = (($DocumentsThisWeek - $DocumentsLastWeek) / $DocumentsLastWeek) * 100;
        return round($growthPercentage, 2);
    }
    public function getAverageRatingByDocumentId($documentId)
    {
        $query = "SELECT AVG(rating) AS averageRating
                  FROM downloaded
                  WHERE DocumentId = :documentId
                  AND rating IS NOT NULL";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result !== false && isset($result['averageRating'])) ? $result['averageRating'] : 0;
    }

    public function getTotalDownloadsByDocumentId($documentId)
    {
        $query = "SELECT COUNT(*) AS totalDownloads
                  FROM downloaded
                  WHERE DocumentId = :documentId";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result !== false && isset($result['totalDownloads'])) ? $result['totalDownloads'] : 0;
    }
    public function getTotalDownloadsByUserIdAndType($userId, $type)
    {
        $query = "SELECT COUNT(d.DownloadedId) AS totalDownloads
              FROM downloaded d
              JOIN document doc ON d.DocumentId = doc.DocumentId
              WHERE doc.UserId = :userId
              AND doc.Type = :type";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result !== false && isset($result['totalDownloads'])) ? $result['totalDownloads'] : 0;
    }

    public function searchDocuments($searchTerm)
    {
        $query = "SELECT *
                  FROM document
                  WHERE Title LIKE :searchTerm
                     OR Category LIKE :searchTerm";

        $stmt = $this->db->prepare($query);

        $searchParam = '%' . $searchTerm . '%';
        $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchUserDocuments($UserId, $searchTerm)
    {
        $query = "SELECT *
              FROM document
              WHERE UserId = :UserId
              AND (Title LIKE :searchTerm OR Category LIKE :searchTerm)";

        $stmt = $this->db->prepare($query);

        $searchParam = '%' . $searchTerm . '%';
        $stmt->bindParam(':UserId', $UserId, PDO::PARAM_INT);
        $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
?>