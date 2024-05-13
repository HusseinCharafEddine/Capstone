<?php

include 'common/commonFunctions.php';

class DownloadController
{

    private $db;

    public function __construct()
    {
        $this->db = DBConnect();
    }

    public function addDownload($documentId, $userId, $date)
    {
        $query = "INSERT INTO downloaded (DocumentId, UserId, Date) VALUES (:documentId, :userId, :date)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getAllDownloads()
    {
        $query = "SELECT * FROM downloaded";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDownloadsByDocumentId($documentId)
    {
        $query = "SELECT * FROM downloaded WHERE DocumentId = :documentId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDownloadsByUserId($userId)
    {
        $query = "SELECT * FROM download WHERE UserId = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDownloadsByDateRange($startDate, $endDate)
    {
        $query = "SELECT * FROM downloaded WHERE Date BETWEEN :startDate AND :endDate";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchDownloadsForPage($userId, $offset, $downloadsPerPage, $filter = null, $searchTerm = null)
    {
        $query = "SELECT d.*, doc.*
              FROM downloaded d
              JOIN document doc ON d.DocumentId = doc.DocumentId
              WHERE d.UserId = :userId";

        $filterApplied = false;

        if ($filter) {
            if (!empty($filter['universityId']) || !empty($filter['courseId']) || !empty($filter['rating'])) {
                $query .= " AND 1";
                $filterApplied = true;

                if (!empty($filter['universityId'])) {
                    $query .= " AND EXISTS (SELECT 1 FROM course c WHERE doc.CourseId = c.CourseId AND c.UniversityId = :universityId)";
                }
                if (!empty($filter['courseId'])) {
                    $query .= " AND doc.CourseId = :courseId";
                }
                if (!empty($filter['rating'])) {
                    $query .= " AND doc.Rating = :rating";
                }
            }
        }

        if ($searchTerm !== null && trim($searchTerm) !== '') {
            $query .= " AND (doc.Title LIKE :searchTerm OR doc.Category LIKE :searchTerm)";
        }

        $query .= " LIMIT :offset, :downloadsPerPage";

        $stmt = $this->db->prepare($query);

        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':downloadsPerPage', (int) $downloadsPerPage, PDO::PARAM_INT);

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

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getMostDownloadedToday()
    {
        $today = date('Y-m-d');

        $queryToday = "SELECT DocumentId, UserId , COUNT(*) AS downloads_count
                   FROM downloaded
                   WHERE Date = :today
                   GROUP BY DocumentId
                   ORDER BY downloads_count DESC
                   LIMIT 1";

        $stmt = $this->db->prepare($queryToday);
        $stmt->bindParam(':today', $today, PDO::PARAM_STR);
        $stmt->execute();
        $resultToday = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resultToday && !empty($resultToday['DocumentId'])) {
            return $resultToday;
        } else {
            $queryAllTime = "SELECT DocumentId, COUNT(*) AS downloads_count, UserId
                         FROM downloaded
                         GROUP BY DocumentId
                         ORDER BY downloads_count DESC
                         LIMIT 1";

            $stmtAllTime = $this->db->query($queryAllTime);
            $resultAllTime = $stmtAllTime->fetch(PDO::FETCH_ASSOC);

            return $resultAllTime;
        }
    }

    public function getMostDownloadedYesterday($excludeDocumentIds = [])
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $query = "SELECT DocumentId, COUNT(*) AS downloads_count
                  FROM downloaded
                  WHERE Date >= :yesterday AND Date < DATE_ADD(:yesterday, INTERVAL 1 DAY)";

        if (!empty($excludeDocumentIds) && count($excludeDocumentIds) > 1) {
            $excludeIdsString = implode(',', $excludeDocumentIds);
            $query .= " AND DocumentId NOT IN ($excludeIdsString)";
        }

        $query .= " GROUP BY DocumentId
                    ORDER BY downloads_count DESC
                    LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':yesterday', $yesterday, PDO::PARAM_STR);

        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result && !empty($excludeDocumentIds) && count($excludeDocumentIds) > 1) {
            $fallbackQuery = "SELECT DocumentId, COUNT(*) AS downloads_count
                              FROM downloaded
                              GROUP BY DocumentId
                              ORDER BY downloads_count DESC
                              LIMIT 1";

            $fallbackStmt = $this->db->prepare($fallbackQuery);
            $fallbackStmt->execute();

            $result = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$result) {
            $fallbackQuery = "SELECT DocumentId, COUNT(*) AS downloads_count
                              FROM downloaded
                              WHERE Date >= :yesterday AND Date < DATE_ADD(:yesterday, INTERVAL 1 DAY)
                              GROUP BY DocumentId
                              ORDER BY downloads_count DESC
                              LIMIT 1";

            $fallbackStmt = $this->db->prepare($fallbackQuery);
            $fallbackStmt->bindParam(':yesterday', $yesterday, PDO::PARAM_STR);
            $fallbackStmt->execute();

            $result = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
        }

        return $result;
    }
    public function getTotalDownloadsForUser($userId)
    {
        $query = "SELECT COUNT(*) AS totalDownloads 
                  FROM downloaded d
                  INNER JOIN document doc ON d.DocumentId = doc.DocumentId
                  WHERE doc.UserId = :userId";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['totalDownloads'];
    }
    public function getTotalDownloadsLastWeek($userId)
    {
        $startDate = date('Y-m-d', strtotime('-14 days'));
        $endDate = date('Y-m-d', strtotime('-7 days'));

        $query = "SELECT COUNT(*) AS totalDownloadsLastWeek
              FROM downloaded d
              JOIN document doc ON d.DocumentId = doc.DocumentId
              WHERE doc.UserId = :userId
              AND DATE(d.Date) BETWEEN STR_TO_DATE(:startDate, '%Y-%m-%d') AND STR_TO_DATE(:endDate, '%Y-%m-%d')";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['totalDownloadsLastWeek'] ?? 0;
    }

    public function getTotalDownloadsThisWeek($userId)
    {
        $startDate = date('Y-m-d', strtotime('-7 days'));
        $query = "SELECT COUNT(*) AS totalDownloadsThisWeek
              FROM downloaded d
              JOIN document doc ON d.DocumentId = doc.DocumentId
              WHERE doc.UserId = :userId
              AND DATE(d.Date) > STR_TO_DATE(:startDate, '%Y-%m-%d') ";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['totalDownloadsThisWeek'] ?? 0;

    }

    public function getNewestDownloadedDocument($excludeDocumentId = null)
    {
        $query = "SELECT DocumentId, MAX(Date) AS latest_added_date
          FROM document ";

        if ($excludeDocumentId !== null) {
            $query .= "WHERE DocumentId != :excludeDocumentId ";
        }

        $query .= "GROUP BY DocumentId
            ORDER BY latest_added_date DESC
            LIMIT 1";

        $stmt = $this->db->prepare($query);
        if ($excludeDocumentId !== null) {
            $stmt->bindParam(':excludeDocumentId', $excludeDocumentId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
    public function getTotalRatingByDocumentId($documentId)
    {
        $query = "SELECT COUNT(*) AS totalDownloads
                  FROM downloaded
                  WHERE DocumentId = :documentId               
                  AND rating IS NOT NULL";


        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result !== false && isset($result['totalDownloads'])) ? $result['totalDownloads'] : 0;
    }
    public function getDownloadByUserAndDocument($userId, $documentId)
    {
        $query = "SELECT * FROM downloaded WHERE UserId = :userId AND DocumentId = :documentId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    function addDocumentToDownloads($userId, $documentId)
    {
        $downloadController = new DownloadController();
        if (!$downloadController->getDownloadByUserAndDocument($userId, $documentId)) {
            $query = "INSERT INTO Downloaded (UserId, DocumentId) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $documentId]);
            return $stmt->rowCount() > 0;
        } else {
            return true;
        }
    }

    public function updateDocumentRating($userId, $documentId, $newRating)
    {
        $query = "UPDATE downloaded SET Rating = :newRating WHERE UserId = :userId AND DocumentId = :documentId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->bindParam(':newRating', $newRating, PDO::PARAM_INT);
        echo $userId, $documentId, $newRating;
        print_r($stmt);
        try {
            $result = $stmt->execute();
            return $result;
        } catch (PDOException $e) {
            echo "Database Error: " . $e->getMessage();
            return false;
        }
    }

    public function searchUserDownloads($UserId, $searchTerm)
    {
        $query = "SELECT d.*
        FROM document d
        INNER JOIN downloaded dl ON d.DocumentId = dl.DocumentId
        WHERE (d.Title LIKE :searchTerm OR d.Category LIKE :searchTerm)
          AND dl.UserId = :UserId
        ";

        $stmt = $this->db->prepare($query);

        $searchParam = '%' . $searchTerm . '%';
        $stmt->bindParam(':UserId', $UserId, PDO::PARAM_INT);
        $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);


        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function computeDownloadGrowthPercentage($userId)
    {
        $downloadsLastWeek = $this->getTotalDownloadsLastWeek($userId);
        $downloadsThisWeek = $this->getTotalDownloadsThisWeek($userId);
        if ($downloadsLastWeek > 0) {
            $growthPercentage = (($downloadsThisWeek - $downloadsLastWeek) / $downloadsLastWeek) * 100;
        } else if ($downloadsThisWeek > 0) {
            $growthPercentage = 100;
        } else {
            $growthPercentage = 0;
        }

        return $growthPercentage;
    }
    public function getAverageRatingByUserId($userId)
    {
        $query = "SELECT AVG(d.Rating) AS averageRating
              FROM downloaded d
              JOIN document doc ON d.DocumentId = doc.DocumentId
              WHERE doc.UserId = :userId
              AND d.Rating IS NOT NULL";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $averageRating = ($result !== false && isset($result['averageRating'])) ? $result['averageRating'] : 0;

        return round($averageRating, 2);
    }

    public function getTotalNonNullRatingsByUserId($userId)
    {
        $query = "SELECT COUNT(d.Rating) AS totalNonNullRatings
              FROM downloaded d
              JOIN document doc ON d.DocumentId = doc.DocumentId
              WHERE doc.UserId = :userId
              AND d.Rating IS NOT NULL";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result !== false && isset($result['totalNonNullRatings'])) ? $result['totalNonNullRatings'] : 0;
    }

    public function getCountOfRatingsEqualTo($userId, $ratingValue)
    {
        $query = "SELECT COUNT(*) AS ratingCount
                  FROM downloaded d
                  JOIN document doc ON d.DocumentId = doc.DocumentId
                  WHERE doc.UserId = :userId
                  AND d.Rating = :ratingValue";

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':ratingValue', $ratingValue, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $ratingCount = ($result !== false && isset($result['ratingCount'])) ? $result['ratingCount'] : 0;

        return $ratingCount;
    }


    // public function getDownloadByUserAndDocument($userId, $documentId)
    // {
    //     $query = "SELECT * FROM downloaded WHERE UserId = :userId AND DocumentId = :documentId";
    //     $stmt = $this->db->prepare($query);
    //     $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    //     $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //     return $result;
    // }
    // function addDocumentToDownloads($userId, $documentId)
    // {
    //     $downloadController = new DownloadController(); // Instantiate the controller if not already instantiated
    //     // Check if the user has not downloaded the document yet
    //     if (!$downloadController->getDownloadByUserAndDocument($userId, $documentId)) {
    //         $query = "INSERT INTO Downloaded (UserId, DocumentId) VALUES (?, ?)";
    //         $stmt = $this->db->prepare($query);
    //         $stmt->execute([$userId, $documentId]);
    //         return $stmt->rowCount() > 0; // Return true if insertion was successful
    //     } else {
    //         return true;
    //     }
    // }

    // public function updateDocumentRating($userId, $documentId, $newRating)
    // {
    //     $query = "UPDATE downloaded SET Rating = :newRating WHERE UserId = :userId AND DocumentId = :documentId";
    //     $stmt = $this->db->prepare($query);
    //     $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    //     $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
    //     $stmt->bindParam(':newRating', $newRating, PDO::PARAM_INT);
    //     echo $userId, $documentId, $newRating;
    //     print_r($stmt);
    //     try {
    //         $result = $stmt->execute();
    //         return $result;
    //     } catch (PDOException $e) {
    //         // Handle database error
    //         echo "Database Error: " . $e->getMessage();
    //         return false;
    //     }
    // }
    // // Function to add a document to the downloads table

    // public function searchUserDownloads($UserId, $searchTerm)
    // {
    //     // Prepare the search query to match document names or categories
    //     $query = "SELECT d.*
    //     FROM document d
    //     INNER JOIN downloaded dl ON d.DocumentId = dl.DocumentId
    //     WHERE (d.Title LIKE :searchTerm OR d.Category LIKE :searchTerm)
    //       AND dl.UserId = :UserId
    //     ";

    //     // Prepare the query
    //     $stmt = $this->db->prepare($query);

    //     // Bind the user ID and search term parameters
    //     $searchParam = '%' . $searchTerm . '%'; // Wrap the search term with wildcards for partial matching
    //     $stmt->bindParam(':UserId', $UserId, PDO::PARAM_INT);
    //     $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);


    //     // Execute the query
    //     $stmt->execute();

    //     // Return the search results
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }
}
?>