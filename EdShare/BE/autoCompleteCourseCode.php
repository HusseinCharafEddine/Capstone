<?php
require_once ("common/commonFunctions.php");
$db = DBConnect();

if (isset($_GET['term'])) {
    $courseCode = $_GET['term'];

    $sql = "SELECT DISTINCT CourseCode FROM Course WHERE CourseCode LIKE :courseCode";

    $stmt = $db->prepare($sql);

    $term = '%' . $courseCode . '%';
    $stmt->bindParam(':courseCode', $term, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $courseCodes = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $courseCodes[] = $row['CourseCode'];
        }

        echo json_encode($courseCodes);
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