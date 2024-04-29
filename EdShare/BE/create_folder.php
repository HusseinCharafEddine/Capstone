<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location:../index.php");
}

require_once ("common/commonFunctions.php");
$db = DBConnect();
$username = $_SESSION['username'];
// Check if folder name is provided
if (isset($_POST['folderName'])) {
    // Sanitize and get the folder name
    $folderName = filter_var($_POST['folderName'], FILTER_SANITIZE_STRING);

    // Your logic to create the folder
    // For example:
    $folderPath = __DIR__ . "/../uploads/" . $username . "/" . $folderName;
    mkdir($folderPath);

    // Assuming folder creation is successful, you can send a success response
    echo "Folder '$folderName' created successfully.";
} else {
    // If folder name is not provided, send a failure response
    http_response_code(400);
    echo "Folder name not provided.";
}
?>
