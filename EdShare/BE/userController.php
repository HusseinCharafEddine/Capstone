<?php
include 'common/commonFunctions.php';

class UserController
{

    private $db;

    // Constructor to initialize database connection
    public function __construct()
    {
        $this->db = DBConnect();
    }

    // Create a new user
    public function createUser($data)
    {
        $query = "INSERT INTO user (UserId, Username, Password, Email, FirstName, LastName, UniversityId, Rating, UploadCount, DownloadCount, ContributionScore, ResetTokenHash) VALUES (:UserId, :Username, :Password, :Email, :FirstName, :LastName, :UniversityId, :Rating, :UploadCount, :DownloadCount, :ContributionScore, :ResetTokenHash)";
        $stmt = $this->db->prepare($query);
        $stmt->execute($data);
        return $stmt->rowCount();
    }

    // Read user details
    public function getUser($UserId)
    {
        $query = "SELECT * FROM user WHERE UserId = :UserId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':UserId', $UserId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getAllUsers()
    {
        $query = "SELECT * FROM user";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Use fetchAll() to get all rows
    }


    public function getUserByUsername($Username)
    {
        $query = "SELECT * FROM user WHERE Username = :Username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':Username', $Username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getCountOfUsersInLeague($userId)
    {
        // Retrieve the user's rating to determine the league range
        $user = $this->getUser($userId);
        if (!$user) {
            return 0; // User not found or error handling
        }

        // Determine league range based on user's rating
        $ContributionScore = $user['ContributionScore'];
        $minRating = 0;
        $maxRating = PHP_INT_MAX; // Use a large value for the maximum rating

        if ($ContributionScore >= 0 && $ContributionScore < 200) {
            $maxRating = 200;
        } elseif ($ContributionScore >= 200 && $ContributionScore < 400) {
            $minRating = 200;
            $maxRating = 400;
        } elseif ($ContributionScore >= 400 && $ContributionScore < 700) {
            $minRating = 400;
            $maxRating = 700;
        } elseif ($ContributionScore >= 700 && $ContributionScore < 1000) {
            $minRating = 700;
            $maxRating = 1000;
        } elseif ($ContributionScore >= 1000 && $ContributionScore < 1300) {
            $minRating = 1000;
            $maxRating = 1300;
        } elseif ($ContributionScore >= 1300 && $ContributionScore < 1700) {
            $minRating = 1300;
            $maxRating = 1700;
        } elseif ($ContributionScore >= 1700 && $ContributionScore < 2100) {
            $minRating = 1700;
            $maxRating = 2100;
        } elseif ($ContributionScore >= 2100) {
            $minRating = 2101; // Start from 2101 and no upper limit
        } else {
            // Unknown league or rating, handle accordingly
            return 0;
        }

        // Prepare SQL query to count users within the determined rating range
        $query = "SELECT COUNT(*) AS userCount FROM user WHERE ContributionScore >= :minRating AND ContributionScore < :maxRating";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':minRating', $minRating, PDO::PARAM_INT);
        $stmt->bindParam(':maxRating', $maxRating, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['userCount'];
    }


    public function getCountOfAllUsers()
    {
        $query = "SELECT COUNT(*) AS userCount FROM user";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['userCount'];
    }

    // Update user details
    public function updateUser($UserId, $data)
    {
        // Start building the SET clause
        $setClause = '';

        // Initialize an array to hold bind parameters
        $bindParams = array(':UserId' => $UserId); // UserId is always present and required

        // Check if First Name is provided and not empty
        if (isset($data['Username']) && !empty($data['Username'])) {
            $setClause .= "Username = :Username, ";
            $bindParams[':Username'] = $data['Username']; // Add FirstName parameter to bindParams array
        }

        if (isset($data['FirstName']) && !empty($data['FirstName'])) {
            $setClause .= "FirstName = :FirstName, ";
            $bindParams[':FirstName'] = $data['FirstName']; // Add FirstName parameter to bindParams array
        }

        // Check if Last Name is provided and not empty
        if (isset($data['LastName']) && !empty($data['LastName'])) {
            $setClause .= "LastName = :LastName, ";
            $bindParams[':LastName'] = $data['LastName']; // Add LastName parameter to bindParams array
        }

        // Check if Email is provided and not empty
        if (isset($data['Email']) && !empty($data['Email'])) {
            $setClause .= "Email = :Email, ";
            $bindParams[':Email'] = $data['Email']; // Add Email parameter to bindParams array
        }
        if (isset($data['UniversityId']) && !empty($data['UniversityId'])) {
            $setClause .= "UniversityId = :UniversityId, ";
            $bindParams[':UniversityId'] = $data['UniversityId']; // Add Email parameter to bindParams array
        }

        if (isset($data['Password']) && !empty($data['Password'])) {
            // Hash the password using MD5
            $hashedPassword = md5($data['Password']);

            $setClause .= "Password = :Password, ";
            $bindParams[':Password'] = $hashedPassword; // Add hashed password parameter to bindParams array
        }

        // Remove trailing comma and space from the SET clause
        $setClause = rtrim($setClause, ', ');

        // Prepare the SQL query
        $query = "UPDATE user SET $setClause WHERE UserId = :UserId";
        $stmt = $this->db->prepare($query);

        // Bind parameters
        foreach ($bindParams as $param => &$value) {
            $stmt->bindParam($param, $value);
        }

        // Execute the query
        $stmt->execute();

        // Return the number of affected rows
        return $stmt->rowCount();
    }



    // Delete user
    public function deleteUser($UserId)
    {
        $query = "DELETE FROM users WHERE UserId = :UserId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':UserId', $UserId);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getDownloadCount($Username)
    {
        $query = "SELECT DownloadCount FROM user WHERE Username= :Username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':Username', $Username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUploadCount($Username)
    {
        $query = "SELECT UploadCount FROM user WHERE Username= :Username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':Username', $Username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function fetchUsersForStandings($standingsType, $userId, $rowsPerPage)
    {
        // Initialize the query variable
        $query = '';

        // Determine the SQL query based on the selected standings type
        if ($standingsType === 'worldwide') {
            $query = "SELECT * FROM user ORDER BY ContributionScore DESC";
        } else {
            // Retrieve the user's rating to determine the league
            $userContributionScoreQuery = "SELECT ContributionScore FROM user WHERE UserId = :userId";
            $userContributionScoreStmt = $this->db->prepare($userContributionScoreQuery);
            $userContributionScoreStmt->bindParam(':userId', $userId);
            $userContributionScoreStmt->execute();
            $userContributionScore = $userContributionScoreStmt->fetch(PDO::FETCH_ASSOC)['ContributionScore'];

            // Determine the league range based on the user's rating
            $leagueMinRating = 0;
            $leagueMaxRating = 0;

            if ($userContributionScore >= 0 && $userContributionScore < 200) {
                $leagueMinRating = 0;
                $leagueMaxRating = 200;
            } elseif ($userContributionScore >= 200 && $userContributionScore < 400) {
                $leagueMinRating = 200;
                $leagueMaxRating = 400;
            }
            // Add more league ranges as needed...

            // Construct the query for league standings
            $query = "SELECT * FROM user WHERE ContributionScore BETWEEN :leagueMinRating AND :leagueMaxRating ORDER BY ContributionScore DESC";
        }

        // Prepare the SQL statement
        $stmt = $this->db->prepare($query);

        // Bind parameters for league standings query
        if ($standingsType != 'worldwide') {
            $stmt->bindParam(':leagueMinRating', $leagueMinRating, PDO::PARAM_INT);
            $stmt->bindParam(':leagueMaxRating', $leagueMaxRating, PDO::PARAM_INT);
        }

        // Execute the SQL query
        // print_r($stmt);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Pagination logic
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($currentPage - 1) * $rowsPerPage;
        $paginatedUsers = array_slice($users, $offset, $rowsPerPage);

        return $paginatedUsers;
    }


    // Other methods...

}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if it's a delete request
    if (isset($_POST['deleteUser'])) {
        // Handle delete user logic
        deleteUser($_POST['userId']); // Assuming deleteUser function exists in your code
    } elseif (isset($_POST['updateUser'])) {
        // Handle update user logic
        updateUser($_POST['userId'], $_POST['newData']); // Assuming updateUser function exists in your code
    }

}

?>