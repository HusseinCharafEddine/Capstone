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
    public function fetchDocumentsForPage($UserId, $offset, $documentsPerPage, $searchTerm =null)
    {
        // Define the base SQL query
        $query = "SELECT * FROM document WHERE UserId = :UserId";
    
        // If a search term is provided, add the search condition to the query
        if (!empty($searchTerm)) {
            $query .= " AND Title LIKE :searchTerm";
        }
    
        // Add LIMIT clause for pagination
        $query .= " LIMIT :offset, :documentsPerPage";
    
        // Prepare the SQL statement
        $stmt = $this->db->prepare($query);
    
        // Bind parameters
        $stmt->bindParam(':UserId', $UserId, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':documentsPerPage', $documentsPerPage, PDO::PARAM_INT);
    
        // If a search term is provided, bind the search parameter
        if (!empty($searchTerm)) {
            $searchTerm = "%{$searchTerm}%"; // Add wildcard characters to search term
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
        }
    
        // Execute the query
        $stmt->execute();
    
        // Fetch the results
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
    public function fetchAllDocumentsForPage($offset, $uploadsForPage, $filter = null, $searchTerm = null)
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

        // Add search term condition if provided
        if ($searchTerm !== null && trim($searchTerm) !== '') {
            if ($filterApplied) {
                $query .= " AND"; // Add AND if filters are already applied
            } else {
                $query .= " WHERE"; // Start WHERE clause if no filters applied
            }
            $query .= " (Title LIKE :searchTerm OR Category LIKE :searchTerm)";
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

        // Bind search term parameter if provided
        if ($searchTerm !== null && trim($searchTerm) !== '') {
            $searchParam = '%' . $searchTerm . '%'; // Wrap the search term with wildcards for partial matching
            $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);
        }

        // Execute the query
        $stmt->execute();

        // Return the result
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function fetchAllDocumentsForUser($userId, $offset, $uploadsForPage, $filter = null)
    {
        // Start building the query
        $query = "SELECT d.*, COUNT(dl.DocumentId) AS downloadCount
                  FROM document d
                  LEFT JOIN downloaded dl ON d.DocumentId = dl.DocumentId
                  WHERE d.UserId = :userId";

        // Initialize a flag to track if any filter is applied
        $filterApplied = false;

        // Add filter conditions if provided
        if ($filter) {
            // Check if any filter parameters are present
            if (!empty($filter['universityId'])) {
                // Join with the course table to get the university
                $query .= " AND EXISTS (SELECT 1 FROM course c WHERE d.CourseId = c.CourseId AND c.UniversityId = :universityId)";
                $filterApplied = true;
            }
            if (!empty($filter['courseId'])) {
                // Apply course filter
                $query .= " AND d.CourseId = :courseId";
                $filterApplied = true;
            }
            if (!empty($filter['rating'])) {
                // Apply rating filter
                $query .= " AND d.Rating = :rating";
                $filterApplied = true;
            }
        }

        // Group by document columns to aggregate download counts
        $query .= " GROUP BY d.DocumentId";

        // Add order by download count in descending order
        $query .= " ORDER BY downloadCount DESC";

        // Add limit and offset to the query
        $query .= " LIMIT :offset, :uploadsForPage";

        // Prepare the query
        $stmt = $this->db->prepare($query);

        // Bind parameters
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
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


    public function getDocumentCountByUserId($userId)
    {
        $query = "SELECT COUNT(*) AS documentCount FROM document WHERE UserId = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the query executed successfully
        if ($result !== false && isset($result['documentCount'])) {
            return $result['documentCount'];
        } else {
            return 0; // Default value if query fails or no documents found
        }
    }


    // Function to get total number of documents by user ID
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

    // Function to calculate total Documents for the last week by user ID
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


    // Function to calculate total Documents for this week by user ID
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


    // Function to calculate download growth percentage between last week and this week
    public function computeDocumentGrowthPercentage($userId)
    {
        $DocumentsLastWeek = $this->getTotalDocumentsLastWeekByUserId($userId);
        $DocumentsThisWeek = $this->getTotalDocumentsThisWeekByUserId($userId);

        if ($DocumentsLastWeek == 0) {
            return ($DocumentsThisWeek > 0) ? 100 : 0; // Handle division by zero
        }

        $growthPercentage = (($DocumentsThisWeek - $DocumentsLastWeek) / $DocumentsLastWeek) * 100;
        return round($growthPercentage, 2); // Return growth percentage rounded to 2 decimal places
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

    // Function to retrieve total number of downloads per document
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
        // Prepare the search query to match document names or categories
        $query = "SELECT *
                  FROM document
                  WHERE Title LIKE :searchTerm
                     OR Category LIKE :searchTerm";

        // Prepare the query
        $stmt = $this->db->prepare($query);

        // Bind the search term parameter
        $searchParam = '%' . $searchTerm . '%'; // Wrap the search term with wildcards for partial matching
        $stmt->bindParam(':searchTerm', $searchParam, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Return the search results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchUserDocuments($UserId, $searchTerm)
{
    // Prepare the search query to match document names or categories for a specific user
    $query = "SELECT *
              FROM document
              WHERE UserId = :UserId
              AND (Title LIKE :searchTerm OR Category LIKE :searchTerm)";

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


}
?>