<?php
    session_start();
    if (!isset($_SESSION["username"])){
        header("location:../index.php");

    }

require_once("common/commonFunctions.php");
$db = DBConnect();
$username = $_SESSION['username'];

$RetreiveUniversityId= "SELECT UniversityId FROM User WHERE Username=?";
$stmt = $db->prepare($RetreiveUniversityId);
$stmt->execute([$username]);
$universityId= $stmt->fetchColumn();


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    exit('POST request method required');
}

if ($_FILES["file"]["error"] !== UPLOAD_ERR_OK) {
    switch ($_FILES["file"]["error"]) {
        case UPLOAD_ERR_PARTIAL:
            exit('File only partially uploaded');
            break;
        case UPLOAD_ERR_NO_FILE:
            exit('No file was uploaded');
            break;
        case UPLOAD_ERR_EXTENSION:
            exit('File upload stopped by a PHP extension');
            break;
        case UPLOAD_ERR_INI_SIZE:
            exit('File exceeds upload_max_filesize in php.ini');
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            exit('Temporary folder not found');
            break;
        case UPLOAD_ERR_CANT_WRITE:
            exit('Failed to write file');
            break;
        default:
            exit('Unknown upload error');
            break;
    }
}

// Setting max file size (50MB)
$maxFileSize = 50 * 1024 * 1024; // 50MB in bytes

if ($_FILES["file"]["size"] > $maxFileSize) {
    exit('File too large (max 50MB)');
}

// Allowed file types
$allowedExtensions = ["pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "py", "c", "zip", "java"];

$pathinfo = pathinfo($_FILES["file"]["name"]);
$fileExtension = strtolower($pathinfo["extension"]);

// Check if the file extension is allowed
if (!in_array($fileExtension, $allowedExtensions)) {
    exit("Invalid file type");
}

// Sanitize file name to prevent directory traversal and ensure unique file names
$baseName = preg_replace("/[^\w-]/", "_", $pathinfo["filename"]);
$fileName = $baseName . "." . $fileExtension;

$userUploadDirectory = __DIR__ . "/../uploads/" . $username . "/";

// Create user-specific directory if it doesn't exist
if (!file_exists($userUploadDirectory)) {
    mkdir($userUploadDirectory, 0777, true); // Create directory recursively with full permissions
}

// Set the destination path inside user-specific directory
$destination = $userUploadDirectory . $fileName;

$i = 1;
while (file_exists($destination)) {
    $fileName = $baseName . "($i)." . $fileExtension;
    $destination = $userUploadDirectory . $fileName;
    $i++;
}

// Move the uploaded file to the destination
if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
    exit("Can't move uploaded file");
}

$document= new stdClass();

$CourseName=VarExist($_POST["course-name"]);
$CourseCode=VarExist($_POST["course-code"]);

$RetreiveUserId= "SELECT UserId FROM User WHERE Username=?";
$stmt = $db->prepare($RetreiveUserId);
$stmt->execute([$username]);
$userId = $stmt->fetchColumn();


$RetreiveCourseId= "SELECT CourseId FROM Course WHERE CourseName=?";
$stmt = $db->prepare($RetreiveCourseId);
$stmt->execute([$CourseName]);
$CourseId =$stmt->fetchColumn();


if (!$CourseId) {
    // Insert the new course into the course table
    $insertCourseQuery = "INSERT INTO Course (CourseName, UniversityId, CourseCode) VALUES (?,?,?) ";
    $stmt = $db->prepare($insertCourseQuery);
    $stmt->execute([$CourseName,$universityId, $CourseCode]);
    $CourseId = $db->lastInsertId();
}
$Category=VarExist($_POST["category"]);
$Title=VarExist($_POST["title"]);

$document->UserId= $userId;
$document->CourseId= $CourseId;
$document->Category = $Category;
$document->Title = $Title;

$DocumentId = InsertDocumentToDBFromObject($document);

echo "File uploaded successfully.";


function InsertDocumentToDBFromObject($document){
    $db= DBConnect();

    $query = "INSERT INTO Document (UserId, CourseId, Category, Title) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$document->UserId, $document->CourseId, $document -> Category, $document -> Title]);
    if ($stmt->rowCount() > 0){
        return $db->lastInsertId();
    } else {
        return 0;
    }
}


?>
