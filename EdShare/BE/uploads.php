<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("location:../index.php");
}

require_once ("common/commonFunctions.php");
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
            header("location:../html/uploads.php?success=1");
            break;
        case UPLOAD_ERR_NO_FILE:
            header("location:../html/uploads.php?success=1");
            break;
        case UPLOAD_ERR_EXTENSION:
            header("location:../html/uploads.php?success=1");
            break;
        case UPLOAD_ERR_INI_SIZE:
            header("location:../html/uploads.php?success=0");
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            header("location:../html/uploads.php?success=1");
            break;
        case UPLOAD_ERR_CANT_WRITE:
            header("location:../html/uploads.php?success=1");
            break;
        default:
            header("location:../html/uploads.php?success=1");
            break;
    }
}

// Setting max file size (50MB)
$maxFileSize = 1 * 1024 * 1024; // 50MB in bytes

if ($_FILES["file"]["size"] > $maxFileSize) {
    header("location:../html/uploads.php?success=0");
    exit;
}

// Allowed file types
$allowedExtensions = ["pdf"];

$pathinfo = pathinfo($_FILES["file"]["name"]);
$fileExtension = strtolower($pathinfo["extension"]);

// Check if the file extension is allowed
if (!in_array($fileExtension, $allowedExtensions)) {
    header("location:../html/uploads.php?success=3");
    exit;
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
$document->Type = VarExist($_POST["type"]);
$document->FilePath = $destination;
$document->ThumbnailPath = $thumbnailPath;

try {
    $DocumentId = InsertDocumentToDBFromObject($db, $document);
    // Additional code if the operation is successful
} catch (PDOException $e) {
    // Handle the exception here
    header("location:../html/uploads.php?success=2");
    exit; // Ensure no further output is sent
}

if ($DocumentId > 0) {
    // User details updated successfully
    header("location:../html/uploads.php?success=4");
    exit; // Ensure no further output is sent
} else {
    // Failed to update user details
    header("location:../html/uploads.php?success=1");
    exit; // Ensure no further output is sent
}

function generateThumbnail($filePath, $thumbnailDirectory)
{
    // Create thumbnail directory if it doesn't exist
    if (!file_exists($thumbnailDirectory)) {
        mkdir($thumbnailDirectory, 0777, true);
    }

    $pathinfo = pathinfo($filePath);
    $thumbnailPath = $pathinfo['filename'] . "_thumb.png";

    try {
        $im = new Imagick();

        // Read the first page of the PDF and convert it to an image
        $im->setResolution(600, 600); // Set resolution to 600 dpi for higher quality
        $im->readImage($filePath . '[0]'); // [0] represents the first page
        // Enhance image sharpness
        $im->sharpenImage(1, 0.5); // Adjust sharpening parameters
        $im->setImageFormat('png'); // Set the image format to JPG

        // Set background color to white
        $im->setImageBackgroundColor('white');
        $im->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);

        // Optimize color space for better color representation
        $im->transformImageColorspace(Imagick::COLORSPACE_SRGB);

        $im->setAntiAlias(true); // Enable anti-aliasing

        // Resize the image to create a thumbnail (e.g., 300x300 pixels)
        $im->thumbnailImage(300, 300, true); // Adjust dimensions as needed

        // Write the thumbnail image to the destination
        $im->writeImage($thumbnailDirectory . $pathinfo['filename'] . "_thumb.png");

        // Destroy the Imagick object to free up memory
        $im->destroy();

        return $thumbnailPath;
    } catch (Exception $e) {
        // Handle any exceptions
        echo 'Error generating thumbnail: ', $e->getMessage();
        return null; // Return null to indicate failure
    }
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
    $fileName = basename($document->FilePath);

    $query = "INSERT INTO Document (UserId, CourseId, Category, Title, Type, FilePath, ThumbnailPath) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($query);
    $stmt->execute([$document->UserId, $document->CourseId, $document->Category, $document->Title, $document->Type, $fileName, $document->ThumbnailPath]);
    if ($stmt->rowCount() > 0) {
        return $db->lastInsertId();
    } else {
        return 0;
    }
}

?>