<?php

include 'common/commonFunctions.php';

class UniversityController
{

    private $db;

    public function __construct()
    {
        $this->db = DBConnect();
    }

    public function createUniversity($name, $acronym)
    {
        $query = "INSERT INTO university (UniversityName, UniversityAcronym) VALUES (:name, :acronym)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':acronym', $acronym, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function getAllUniversities()
    {
        $query = "SELECT * FROM university";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUniversityById($id)
    {
        $query = "SELECT * FROM university WHERE UniversityId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUniversity($id, $name, $acronym)
    {
        $query = "UPDATE university SET UniversityName = :name, UniversityAcronym = :acronym WHERE UniversityId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':acronym', $acronym, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function deleteUniversity($id)
    {
        $query = "DELETE FROM university WHERE UniversityId = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

?>