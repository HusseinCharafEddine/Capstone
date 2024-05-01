<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location:../index.html");
}
require_once ("common/commonFunctions.php");
require ("userController.php");

$userController = new UserController();
$db = DBConnect();

$username = $_SESSION['username'];
$userId = $_SESSION['userId'];
$newUsername = $_POST['username'];

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];
$universityName = $_POST['universityName'];
$universityAcronym = $_POST['universityAcronym'];

$oldUser = $userController->getUser($userId);
$userId = $oldUser['UserId'];

// Check if the university exists
$universityId = getOrCreateUniversityId($universityName, $universityAcronym);

// Construct the data array
$data = array(
    'Username' => $newUsername,
    'UserId' => $userId,
    'FirstName' => $firstName,
    'LastName' => $lastName,
    'Email' => $email,
    'Password' => $password,
    'UniversityId' => $universityId
);

// Update user details
try {
    $result = $userController->updateUser($userId, $data);
    // Additional code if the operation is successful
} catch (PDOException $e) {
    // Handle the exception here
    header("Location: ../html/pages-account-settings-account.php?success=2");
    exit; // Ensure no further output is sent
}

if ($result > 0) {
    // User details updated successfully
    header("Location: ../html/pages-account-settings-account.php?success=1");
    exit; // Ensure no further output is sent
} else {
    // Failed to update user details
    header("Location: ../html/pages-account-settings-account.php?success=0");
    exit; // Ensure no further output is sent
}


// Function to get or create university ID
function getOrCreateUniversityId($universityName, $universityAcronym)
{
    global $db;
    $universityQuery = "SELECT UniversityId FROM University WHERE UniversityName = ?";
    $stmt = $db->prepare($universityQuery);
    $stmt->execute([$universityName]);
    $universityId = $stmt->fetchColumn();

    // If the university doesn't exist, create it and retrieve its ID
    if (!$universityId) {
        $insertUniversityQuery = "INSERT INTO University (UniversityName, UniversityAcronym) VALUES (?, ?)";
        $stmt = $db->prepare($insertUniversityQuery);
        $stmt->execute([$universityName, $universityAcronym]);
        $universityId = $db->lastInsertId();
    }
    return $universityId;
}
?>