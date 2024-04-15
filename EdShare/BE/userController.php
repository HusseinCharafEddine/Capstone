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
    public function getAllUsers()
    {
        $query = "SELECT * FROM user";
        $stmt = $this->db->prepare($query);
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
        $query = "UPDATE users SET Username = :Username, Password = :Password, Email = :Email, FirstName = :FirstName, LastName = :LastName, UniversityId = :UniversityId, Rating = :Rating, UploadCount = :UploadCount, DownloadCount = :DownloadCount, TokenScore = :TokenScore, ResetTokenHash = :ResetTokenHash WHERE UserId = :UserId";
        $stmt = $this->db->prepare($query);
        $data['UserId'] = $UserId;
        $stmt->execute($data);
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

?>