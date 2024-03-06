<?php
// Database connection
require_once("common/commonFunctions.php");
$db = DBConnect();

// Check if the course-name parameter is set
if (isset($_GET['term'])) {
    $courseCode = $_GET['term'];

    // SQL query to retrieve matching course names
    $sql = "SELECT DISTINCT CourseCode FROM Course WHERE CourseCode LIKE :courseCode";

    // Prepare the SQL statement
    $stmt = $db->prepare($sql);

    // Bind the parameter
    $term = '%' . $courseCode . '%'; // Add wildcards to search anywhere in the name
    $stmt->bindParam(':courseCode', $term, PDO::PARAM_STR);

    // Execute the query
    if ($stmt->execute()) {
        // Array to store matching course names
        $courseCodes = array();

        // Fetch and store matching course names in the array
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $courseCodes[] = $row['CourseCode'];
        }

        // Return matching course names as JSON
        echo json_encode($courseCodes);
    } else {
        // Handle SQL error
        $errorInfo = $stmt->errorInfo();
        echo "Error executing SQL query: " . $errorInfo[2];
    }

    // Close statement and database connection
    $stmt = null;
    $db = null;
} else {
    // Handle if course-name parameter is not set
    echo "Course name parameter is not set.";
}
?>
