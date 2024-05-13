<?php
include 'common/commonFunctions.php';

class CoursesController
{

    private $db;

    public function __construct()
    {
        $this->db = DBConnect();
    }

    public function createCourse($CourseId, $CourseName, $CourseCode, $UniversityId)
    {
        $query = "INSERT INTO course (CourseId, CourseName, CourseCode, UniversityId) VALUES (:CourseId, :CourseName, :CourseCode, :UniversityId)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':CourseId', $CourseId);
        $stmt->bindParam(':CourseName', $CourseName);
        $stmt->bindParam(':CourseCode', $CourseCode);
        $stmt->bindParam(':UniversityId', $UniversityId);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function getCourse($CourseId)
    {
        $query = "SELECT * FROM course WHERE CourseId = :CourseId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':CourseId', $CourseId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllCourses()
    {
        $query = "SELECT * FROM course";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateCourse($CourseId, $CourseName, $CourseCode, $UniversityId)
    {
        $query = "UPDATE courses SET CourseName = :CourseName, CourseCode = :CourseCode, UniversityId = :UniversityId WHERE CourseId = :CourseId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':CourseId', $CourseId);
        $stmt->bindParam(':CourseName', $CourseName);
        $stmt->bindParam(':CourseCode', $CourseCode);
        $stmt->bindParam(':UniversityId', $UniversityId);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function deleteCourse($CourseId)
    {
        $query = "DELETE FROM courses WHERE CourseId = :CourseId";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':CourseId', $CourseId);
        $stmt->execute();
        return $stmt->rowCount();
    }
}

?>