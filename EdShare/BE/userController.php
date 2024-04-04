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
        $query = "INSERT INTO user (UserId, Username, Password, Email, FirstName, LastName, UniversityId, Rating, UploadCount, DownloadCount, TokenScore, ResetTokenHash) VALUES (:UserId, :Username, :Password, :Email, :FirstName, :LastName, :UniversityId, :Rating, :UploadCount, :DownloadCount, :TokenScore, :ResetTokenHash)";
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
    public function getUserByUsername($Username)
    {
        $query = "SELECT * FROM user WHERE Username = :Username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':Username', $Username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
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