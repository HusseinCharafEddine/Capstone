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

$universityId = getOrCreateUniversityId($universityName, $universityAcronym);

$data = array(
    'Username' => $newUsername,
    'UserId' => $userId,
    'FirstName' => $firstName,
    'LastName' => $lastName,
    'Email' => $email,
    'Password' => $password,
    'UniversityId' => $universityId
);

try {
    $result = $userController->updateUser($userId, $data);
} catch (PDOException $e) {
    header("Location: ../html/pages-account-settings-account.php?success=2");
    exit;
}

if ($result > 0) {
    $oldUsername = $oldUser['Username'];
    $newUsername = $data['Username'];

    $oldUserUploadDirectory = __DIR__ . "/../uploads/" . $oldUsername . "/";
    $newUserUploadDirectory = __DIR__ . "/../uploads/" . $newUsername . "/";
    $oldUserThumbnailDirectory = __DIR__ . "/../thumbnails/" . $oldUsername . "/";
    $newUserThumbnailDirectory = __DIR__ . "/../thumbnails/" . $newUsername . "/";
    if (file_exists($oldUserUploadDirectory)) {
        if ($oldUsername !== $newUsername) {
            rename($oldUserUploadDirectory, $newUserUploadDirectory);
            rename($oldUserThumbnailDirectory, $newUserThumbnailDirectory);

        }
    }
    header("Location: ../html/pages-account-settings-account.php?success=1");
    exit;
} else {
    header("Location: ../html/pages-account-settings-account.php?success=0");
    exit;
}


function getOrCreateUniversityId($universityName, $universityAcronym)
{
    global $db;
    $universityQuery = "SELECT UniversityId FROM University WHERE UniversityName = ?";
    $stmt = $db->prepare($universityQuery);
    $stmt->execute([$universityName]);
    $universityId = $stmt->fetchColumn();

    if (!$universityId) {
        $insertUniversityQuery = "INSERT INTO University (UniversityName, UniversityAcronym) VALUES (?, ?)";
        $stmt = $db->prepare($insertUniversityQuery);
        $stmt->execute([$universityName, $universityAcronym]);
        $universityId = $db->lastInsertId();
    }
    return $universityId;
}
?>