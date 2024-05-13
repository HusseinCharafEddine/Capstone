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

    public function createUser($data)
    {
        $query = "INSERT INTO user (UserId, Username, Password, Email, FirstName, LastName, UniversityId, Rating, UploadCount, DownloadCount, ContributionScore, ResetTokenHash) VALUES (:UserId, :Username, :Password, :Email, :FirstName, :LastName, :UniversityId, :Rating, :UploadCount, :DownloadCount, :ContributionScore, :ResetTokenHash)";
        $stmt = $this->db->prepare($query);
        $stmt->execute($data);
        return $stmt->rowCount();
    }

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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        $user = $this->getUser($userId);
        if (!$user) {
            return 0;
        }

        $ContributionScore = $user['ContributionScore'];
        $minRating = 0;
        $maxRating = PHP_INT_MAX;

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
            $minRating = 2101;
        } else {
            return 0;
        }

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

    public function updateUser($UserId, $data)
    {
        $setClause = '';

        $bindParams = array(':UserId' => $UserId);

        if (isset($data['Username']) && !empty($data['Username'])) {
            $setClause .= "Username = :Username, ";
            $bindParams[':Username'] = $data['Username'];
        }

        if (isset($data['FirstName']) && !empty($data['FirstName'])) {
            $setClause .= "FirstName = :FirstName, ";
            $bindParams[':FirstName'] = $data['FirstName'];
        }

        if (isset($data['LastName']) && !empty($data['LastName'])) {
            $setClause .= "LastName = :LastName, ";
            $bindParams[':LastName'] = $data['LastName'];
        }

        if (isset($data['Email']) && !empty($data['Email'])) {
            $setClause .= "Email = :Email, ";
            $bindParams[':Email'] = $data['Email'];
        }
        if (isset($data['UniversityId']) && !empty($data['UniversityId'])) {
            $setClause .= "UniversityId = :UniversityId, ";
            $bindParams[':UniversityId'] = $data['UniversityId'];
        }

        if (isset($data['Password']) && !empty($data['Password'])) {
            $hashedPassword = md5($data['Password']);

            $setClause .= "Password = :Password, ";
            $bindParams[':Password'] = $hashedPassword;
        }

        $setClause = rtrim($setClause, ', ');

        $query = "UPDATE user SET $setClause WHERE UserId = :UserId";
        $stmt = $this->db->prepare($query);

        foreach ($bindParams as $param => &$value) {
            $stmt->bindParam($param, $value);
        }

        $stmt->execute();

        return $stmt->rowCount();
    }



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
        $query = '';

        if ($standingsType === 'worldwide') {
            $query = "SELECT * FROM user ORDER BY ContributionScore DESC";
        } else {
            $userContributionScoreQuery = "SELECT ContributionScore FROM user WHERE UserId = :userId";
            $userContributionScoreStmt = $this->db->prepare($userContributionScoreQuery);
            $userContributionScoreStmt->bindParam(':userId', $userId);
            $userContributionScoreStmt->execute();
            $userContributionScore = $userContributionScoreStmt->fetch(PDO::FETCH_ASSOC)['ContributionScore'];

            $leagueMinRating = 0;
            $leagueMaxRating = 0;

            if ($userContributionScore >= 0 && $userContributionScore < 200) {
                $leagueMinRating = 0;
                $leagueMaxRating = 200;
            } elseif ($userContributionScore >= 200 && $userContributionScore < 400) {
                $leagueMinRating = 200;
                $leagueMaxRating = 400;
            }

            $query = "SELECT * FROM user WHERE ContributionScore BETWEEN :leagueMinRating AND :leagueMaxRating ORDER BY ContributionScore DESC";
        }

        $stmt = $this->db->prepare($query);

        if ($standingsType != 'worldwide') {
            $stmt->bindParam(':leagueMinRating', $leagueMinRating, PDO::PARAM_INT);
            $stmt->bindParam(':leagueMaxRating', $leagueMaxRating, PDO::PARAM_INT);
        }

        // print_r($stmt);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
        $offset = ($currentPage - 1) * $rowsPerPage;
        $paginatedUsers = array_slice($users, $offset, $rowsPerPage);

        return $paginatedUsers;
    }



}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['deleteUser'])) {
        deleteUser($_POST['userId']);
    } elseif (isset($_POST['updateUser'])) {
        updateUser($_POST['userId'], $_POST['newData']);
    }

}

?>