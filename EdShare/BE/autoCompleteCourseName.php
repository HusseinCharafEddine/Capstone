<?php
require_once ("common/commonFunctions.php");
$db = DBConnect();

if (isset($_GET['term'])) {
    $courseName = $_GET['term'];

    $sql = "SELECT DISTINCT CourseName FROM Course WHERE CourseName LIKE :courseName";

    $stmt = $db->prepare($sql);

    $term = '%' . $courseName . '%';
    $stmt->bindParam(':courseName', $term, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $courseNames = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $courseNames[] = $row['CourseName'];
        }

        echo json_encode($courseNames);
    } else {
        $errorInfo = $stmt->errorInfo();
        echo "Error executing SQL query: " . $errorInfo[2];
    }

    $stmt = null;
    $db = null;
} else {
    echo "Course name parameter is not set.";
}
?>