<?php

include 'common/commonFunctions.php';

class DownloadController
{

    private $db;

    // Constructor to initialize database connection
    public function __construct()
    {
        $this->db = DBConnect();
    }

    // Add a new download record
    public function addDownload($documentId, $userId, $date)
    {
        $query = "INSERT INTO downloaded (DocumentId, UserId, Date) VALUES (:documentId, :userId, :date)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Get all downloads
    public function getAllDownloads()
    {
        $query = "SELECT * FROM downloaded";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get downloads by document ID
    public function getDownloadsByDocumentId($documentId)
    {
        $query = "SELECT * FROM downloaded WHERE DocumentId = :documentId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':documentId', $documentId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get downloads by user ID
    public function getDownloadsByUserId($userId)
    {
        $query = "SELECT * FROM download WHERE UserId = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get downloads by date range
    public function getDownloadsByDateRange($startDate, $endDate)
    {
        $query = "SELECT * FROM downloaded WHERE Date BETWEEN :startDate AND :endDate";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function fetchDownloadsForPage($userId, $offset, $downloadsPerPage, $filter = null)
    {
        // Start building the query
        $query = "SELECT d.*, doc.*
                  FROM downloaded d
                  JOIN document doc ON d.DocumentId = doc.DocumentId";

        // Initialize a flag to track if any filter is applied
        $filterApplied = false;

        // Add filter conditions if provided
        if ($filter) {
            // Check if any filter parameters are present
            if (!empty($filter['universityId']) || !empty($filter['courseId']) || !empty($filter['rating'])) {
                $query .= " WHERE 1"; // Start WHERE clause
                $filterApplied = true;

                if (!empty($filter['universityId'])) {
                    // Join with the course table to get the university
                    $query .= " AND EXISTS (SELECT 1 FROM course c WHERE doc.CourseId = c.CourseId AND c.UniversityId = :universityId)";
                }
                if (!empty($filter['courseId'])) {
                    // Apply course filter
                    $query .= " AND doc.CourseId = :courseId";
                }
                if (!empty($filter['rating'])) {
                    // Apply rating filter
                    $query .= " AND doc.Rating = :rating";
                }
            }
        }

        // Add limit and offset to the query
        $query .= " LIMIT :offset, :downloadsPerPage";

        // Prepare the query
        $stmt = $this->db->prepare($query);

        // Bind common parameters
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':downloadsPerPage', $downloadsPerPage, PDO::PARAM_INT);

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
        // Calculate the start and end date for the last week (7 days ago from today)
        $startDate = date('Y-m-d', strtotime('-14 days'));
        $endDate = date('Y-m-d', strtotime('-7 days'));

        // SQL query to count downloads within the last week for user's documents
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
        // Calculate the start and end date for the current week (starting from Monday of this week)
        $startDate = date('Y-m-d', strtotime('-7 days'));
        // SQL query to count downloads within the current week for user's documents
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


    // Function to compute the percentage growth of downloads between last week and this week
    public function computeDownloadGrowthPercentage($userId)
    {
        $downloadsLastWeek = $this->getTotalDownloadsLastWeek($userId);
        $downloadsThisWeek = $this->getTotalDownloadsThisWeek($userId);
        if ($downloadsLastWeek > 0) {
            $growthPercentage = (($downloadsThisWeek - $downloadsLastWeek) / $downloadsLastWeek) * 100;
        } else if ($downloadsThisWeek > 0) {
            $growthPercentage = 100;
        } else {
            $growthPercentage = 0; // Default if no downloads last week (avoid division by zero)
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

        // Round the average rating to 1 decimal place
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

}

?>