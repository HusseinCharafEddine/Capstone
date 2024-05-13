<?php

include 'common/commonFunctions.php';

class UploadsController
{

    private $db;

    public function __construct()
    {
        $this->db = DBConnect();
    }


    public function getUploadCountByUserID($userId)
    {
        $query = "SELECT * FROM download WHERE UserId = :userId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}

?>