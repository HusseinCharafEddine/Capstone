<?php
// Database connection
require_once("common/commonFunctions.php");
$db = DBConnect();

// Check if the course-name parameter is set
if (isset($_GET['term'])) {
    $category = $_GET['term'];

    // SQL query to retrieve matching course names
    $sql = "SELECT DISTINCT Category FROM Document WHERE Category LIKE :category";

    // Prepare the SQL statement
    $stmt = $db->prepare($sql);

    // Bind the parameter
    $term = '%' . $category . '%'; // Add wildcards to search anywhere in the name
    $stmt->bindParam(':category', $term, PDO::PARAM_STR);

    // Execute the query
    if ($stmt->execute()) {
        // Array to store matching course names
        $categories = array();

        // Fetch and store matching course names in the array
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $row['Category'];
        }

        // Return matching course names as JSON
        echo json_encode($categories);
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
