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

    public function fetchDownloadsForPage($userId, $offset, $downloadsPerPage, $filter = null, $searchTerm = null)
    {
        // Start building the query
        $query = "SELECT d.*, doc.*
              FROM downloaded d
              JOIN document doc ON d.DocumentId = doc.DocumentId
              WHERE d.UserId = :userId"; // Filter by UserId

        // Initialize a flag to track if any filter is applied
        $filterApplied = false;

        // Add filter conditions if provided
        if ($filter) {
            // Check if any filter parameters are present
            if (!empty($filter['universityId']) || !empty($filter['courseId']) || !empty($filter['rating'])) {
                $query .= " AND 1"; // Continue with AND clause
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

        // Add search term condition if provided
        if ($searchTerm !== null && trim($searchTerm) !== '') {
            $query .= " AND (doc.Title LIKE :searchTerm OR doc.Category LIKE :searchTerm)";
        }

        // Add limit and offset to the query
        $query .= " LIMIT :offset, :downloadsPerPage";

        // Prepare the query
        $stmt = $this->db->prepare($query);

        // Bind common parameters
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(':downloadsPerPage', (int) $downloadsPerPage, PDO::PARAM_INT);

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

        // Bind search term parameter if provided
        if ($searchTerm !== null && trim($searchTerm) !== '') {
            $searchParam = '%' . $searchTerm . '%'; // Wrap the search term with wildcards for partial matching
            $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
        }

        // Execute the query
        $stmt->execute();

        // Return the result
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    // Get the most downloaded document today or on the latest date
    public function getMostDownloadedToday()
    {
        $today = date('Y-m-d');

        // Query to get most downloaded document today
        $queryToday = "SELECT DocumentId, UserId , COUNT(*) AS downloads_count
                   FROM downloaded
                   WHERE Date = :today
                   GROUP BY DocumentId
                   ORDER BY downloads_count DESC
                   LIMIT 1";

        // Prepare and execute query for today's downloads
        $stmt = $this->db->prepare($queryToday);
        $stmt->bindParam(':today', $today, PDO::PARAM_STR);
        $stmt->execute();
        $resultToday = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if there are downloads today; otherwise, get most downloaded document of all time
        if ($resultToday && !empty($resultToday['DocumentId'])) {
            return $resultToday; // Return most downloaded document of today
        } else {
            // Query to get most downloaded document of all time
            $queryAllTime = "SELECT DocumentId, COUNT(*) AS downloads_count, UserId
                         FROM downloaded
                         GROUP BY DocumentId
                         ORDER BY downloads_count DESC
                         LIMIT 1";

            // Execute query for all-time downloads
            $stmtAllTime = $this->db->query($queryAllTime);
            $resultAllTime = $stmtAllTime->fetch(PDO::FETCH_ASSOC);

            return $resultAllTime; // Return most downloaded document of all time
        }
    }

    // Get the most downloaded document yesterday or on the previous date with downloads
    public function getMostDownloadedYesterday($excludeDocumentIds = [])
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Build the base query
        $query = "SELECT DocumentId, COUNT(*) AS downloads_count
                  FROM downloaded
                  WHERE Date >= :yesterday AND Date < DATE_ADD(:yesterday, INTERVAL 1 DAY)";

        // Check if there are documents to exclude
        if (!empty($excludeDocumentIds) && count($excludeDocumentIds) > 1) {
            $excludeIdsString = implode(',', $excludeDocumentIds);
            $query .= " AND DocumentId NOT IN ($excludeIdsString)";
        }

        // Complete the query
        $query .= " GROUP BY DocumentId
                    ORDER BY downloads_count DESC
                    LIMIT 1";

        // Prepare the SQL statement
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':yesterday', $yesterday, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If no result found for yesterday and exclusions are present, try without exclusions
        if (!$result && !empty($excludeDocumentIds) && count($excludeDocumentIds) > 1) {
            $fallbackQuery = "SELECT DocumentId, COUNT(*) AS downloads_count
                              FROM downloaded
                              GROUP BY DocumentId
                              ORDER BY downloads_count DESC
                              LIMIT 1";

            // Prepare and execute the fallback query without exclusions
            $fallbackStmt = $this->db->prepare($fallbackQuery);
            $fallbackStmt->execute();

            // Fetch the fallback result
            $result = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
        }

        // If still no result found, try to get the most downloaded document without exclusions
        if (!$result) {
            $fallbackQuery = "SELECT DocumentId, COUNT(*) AS downloads_count
                              FROM downloaded
                              WHERE Date >= :yesterday AND Date < DATE_ADD(:yesterday, INTERVAL 1 DAY)
                              GROUP BY DocumentId
                              ORDER BY downloads_count DESC
                              LIMIT 1";

            // Prepare and execute the fallback query without any exclusions
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
        $downloadController = new DownloadController(); // Instantiate the controller if not already instantiated
        // Check if the user has not downloaded the document yet
        if (!$downloadController->getDownloadByUserAndDocument($userId, $documentId)) {
            $query = "INSERT INTO Downloaded (UserId, DocumentId) VALUES (?, ?)";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$userId, $documentId]);
            return $stmt->rowCount() > 0; // Return true if insertion was successful
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
            // Handle database error
            echo "Database Error: " . $e->getMessage();
            return false;
        }
    }
    // Function to add a document to the downloads table

    public function searchUserDownloads($UserId, $searchTerm)
    {
        // Prepare the search query to match document names or categories
        $query = "SELECT d.*
        FROM document d
        INNER JOIN downloaded dl ON d.DocumentId = dl.DocumentId
        WHERE (d.Title LIKE :searchTerm OR d.Category LIKE :searchTerm)
          AND dl.UserId = :UserId
        ";

        // Prepare the query
        $stmt = $this->db->prepare($query);

        // Bind the user ID and search term parameters
        $searchParam = '%' . $searchTerm . '%'; // Wrap the search term with wildcards for partial matching
        $stmt->bindParam(':UserId', $UserId, PDO::PARAM_INT);
        $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);


        // Execute the query
        $stmt->execute();

        // Return the search results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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