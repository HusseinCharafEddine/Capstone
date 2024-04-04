<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location:../index.php");
    exit; // Prevent further execution
}
require_once ("common/commonFunctions.php");
require ("userController.php");

$userController = new UserController();
$db = DBConnect();

$username = $_SESSION['username'];
$newUsername = $_POST['username'];

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$email = $_POST['email'];
$password = $_POST['password'];
$universityName = $_POST['universityName'];
$universityAcronym = $_POST['universityAcronym'];

$oldUser = $userController->getUserByUsername($username);
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
    'Password' => $password, // Remember to hash the password before storing it
    'UniversityId' => $universityId
);

// Update user details
$result = $userController->updateUser($userId, $data);

if ($result) {
    echo "User details updated successfully!";
} else {
    echo "Failed to update user details!";
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