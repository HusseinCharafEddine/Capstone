<?php
// Database connection
require_once("common/commonFunctions.php");
$db = DBConnect();

// Check if the course-name parameter is set
if (isset($_GET['term'])) {
    $courseName = $_GET['term'];

    // SQL query to retrieve matching course names
    $sql = "SELECT DISTINCT CourseName FROM Course WHERE CourseName LIKE :courseName";

    // Prepare the SQL statement
    $stmt = $db->prepare($sql);

    // Bind the parameter
    $term = '%' . $courseName . '%'; // Add wildcards to search anywhere in the name
    $stmt->bindParam(':courseName', $term, PDO::PARAM_STR);

    // Execute the query
    if ($stmt->execute()) {
        // Array to store matching course names
        $courseNames = array();

        // Fetch and store matching course names in the array
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $courseNames[] = $row['CourseName'];
        }

        // Return matching course names as JSON
        echo json_encode($courseNames);
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
