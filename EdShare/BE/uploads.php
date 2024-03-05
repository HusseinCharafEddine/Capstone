<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location:../index.php");
}

require_once("common/commonFunctions.php");
$db = DBConnect();
$username = $_SESSION['username'];

$RetreiveUniversityId = "SELECT UniversityId FROM User WHERE Username=?";
$stmt = $db->prepare($RetreiveUniversityId);
$stmt->execute([$username]);
$universityId = $stmt->fetchColumn();

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
$thumbnailDirectory = __DIR__ . "/../thumbnails/" . $username . "/";

// Create user-specific directory if it doesn't exist
if (!file_exists($userUploadDirectory)) {
    mkdir($userUploadDirectory, 0777, true); // Create directory recursively with full permissions
}

// Move the uploaded file to the destination
$destination = $userUploadDirectory . $fileName;
if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
    exit("Can't move uploaded file");
}

// Generate a thumbnail for the uploaded file
$thumbnailPath = generateThumbnail($destination, $thumbnailDirectory);

// Save the thumbnail path and original file path to the database
$document = new stdClass();
$document->UserId = getUserId($db, $username);
$document->CourseId = getCourseId($db, $_POST["course-name"], $universityId, $_POST["course-code"]);
$document->Category = VarExist($_POST["category"]);
$document->Title = VarExist($_POST["title"]);
$document->FilePath = $destination;
$document->ThumbnailPath = $thumbnailPath;

$DocumentId = InsertDocumentToDBFromObject($db, $document);

echo "File uploaded successfully.";

function generateThumbnail($filePath, $thumbnailDirectory)
{
    // Create thumbnail directory if it doesn't exist
    if (!file_exists($thumbnailDirectory)) {
        mkdir($thumbnailDirectory, 0777, true);
    }

    $pathinfo = pathinfo($filePath);
    $thumbnailPath = $thumbnailDirectory . $pathinfo['filename'] . "_thumb.jpg"; 

    // actually generateeee hallllll thingyyyyyyyyyyyyyyyyyyyyyyyyyy

    return $thumbnailPath;
}

function getUserId($db, $username)
{
    $RetreiveUserId = "SELECT UserId FROM User WHERE Username=?";
    $stmt = $db->prepare($RetreiveUserId);
    $stmt->execute([$username]);
    return $stmt->fetchColumn();
}

function getCourseId($db, $courseName, $universityId, $courseCode)
{
    $RetreiveCourseId = "SELECT CourseId FROM Course WHERE CourseName=?";
    $stmt = $db->prepare($RetreiveCourseId);
    $stmt->execute([$courseName]);
    $CourseId = $stmt->fetchColumn();

    if (!$CourseId) {
        // Insert the new course into the course table
        $insertCourseQuery = "INSERT INTO Course (CourseName, UniversityId, CourseCode) VALUES (?,?,?) ";
        $stmt = $db->prepare($insertCourseQuery);
        $stmt->execute([$courseName, $universityId, $courseCode]);
        $CourseId = $db->lastInsertId();
    }

    return $CourseId;
}

function InsertDocumentToDBFromObject($db, $document)
{
    $query = "INSERT INTO Document (UserId, CourseId, Category, Title, FilePath, ThumbnailPath) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$document->UserId, $document->CourseId, $document->Category, $document->Title, $document->FilePath, $document->ThumbnailPath]);
    if ($stmt->rowCount() > 0) {
        return $db->lastInsertId();
    } else {
        return 0;
    }
}
?>
