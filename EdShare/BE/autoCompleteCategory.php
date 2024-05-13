<?php
require_once ("common/commonFunctions.php");
$db = DBConnect();

if (isset($_GET['term'])) {
    $category = $_GET['term'];

    $sql = "SELECT DISTINCT Category FROM Document WHERE Category LIKE :category";

    $stmt = $db->prepare($sql);

    $term = '%' . $category . '%';
    $stmt->bindParam(':category', $term, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $categories = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[] = $row['Category'];
        }
        echo json_encode($categories);
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