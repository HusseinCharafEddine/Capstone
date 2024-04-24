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
                              WHERE Date >= :yesterday AND Date < DATE_ADD(:yesterday, INTERVAL 1 DAY)
                              GROUP BY DocumentId
                              ORDER BY downloads_count DESC
                              LIMIT 1";

            // Prepare and execute the fallback query without exclusions
            $fallbackStmt = $this->db->prepare($fallbackQuery);
            $fallbackStmt->bindParam(':yesterday', $yesterday, PDO::PARAM_STR);
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

            // Fetch the fallback result
            $result = $fallbackStmt->fetch(PDO::FETCH_ASSOC);
        }

        // Debugging: Output the executed query for troubleshooting

        return $result;
    }




    // Get the newest downloaded document
    public function getNewestDownloadedDocument($excludeDocumentId = null)
    {
        $query = "SELECT DocumentId, MAX(Date) AS latest_download_date
                  FROM downloaded ";

        if ($excludeDocumentId !== null) {
            $query .= "WHERE DocumentId != :excludeDocumentId ";
        }

        $query .= "GROUP BY DocumentId
                   ORDER BY latest_download_date DESC
                   LIMIT 1";

        $stmt = $this->db->prepare($query);
        if ($excludeDocumentId !== null) {
            $stmt->bindParam(':excludeDocumentId', $excludeDocumentId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }




}

?>