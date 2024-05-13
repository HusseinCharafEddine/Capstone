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

$maxFileSize = 1 * 1024 * 1024;

if ($_FILES["file"]["size"] > $maxFileSize) {
    header("location:../html/uploads.php?success=0");
    exit;
}

$allowedExtensions = ["pdf"];

$pathinfo = pathinfo($_FILES["file"]["name"]);
$fileExtension = strtolower($pathinfo["extension"]);

if (!in_array($fileExtension, $allowedExtensions)) {
    header("location:../html/uploads.php?success=3");
    exit;
}

$baseName = preg_replace("/[^\w-]/", "_", $pathinfo["filename"]);
$fileName = $baseName . "." . $fileExtension;

$userUploadDirectory = __DIR__ . "/../uploads/" . $username . "/";
$thumbnailDirectory = __DIR__ . "/../thumbnails/" . $username . "/";

if (!file_exists($userUploadDirectory)) {
    mkdir($userUploadDirectory, 0777, true);
}

$destination = $userUploadDirectory . $fileName;
if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
    exit("Can't move uploaded file");
}


$thumbnailPath = generateThumbnail($destination, $thumbnailDirectory);

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
} catch (PDOException $e) {
    header("location:../html/uploads.php?success=2");
    exit;
}

if ($DocumentId > 0) {
    header("location:../html/uploads.php?success=4");
    exit;
} else {
    header("location:../html/uploads.php?success=1");
    exit;
}

function generateThumbnail($filePath, $thumbnailDirectory)
{
    if (!file_exists($thumbnailDirectory)) {
        mkdir($thumbnailDirectory, 0777, true);
    }

    $pathinfo = pathinfo($filePath);
    $thumbnailPath = $pathinfo['filename'] . "_thumb.png";

    try {
        $im = new Imagick();

        $im->setResolution(600, 600);
        $im->readImage($filePath . '[0]');
        $im->sharpenImage(1, 0.5);
        $im->setImageFormat('png');

        $im->setImageBackgroundColor('white');
        $im->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);

        $im->transformImageColorspace(Imagick::COLORSPACE_SRGB);

        $im->setAntiAlias(true);

        $im->thumbnailImage(300, 300, true);

        $im->writeImage($thumbnailDirectory . $pathinfo['filename'] . "_thumb.png");

        $im->destroy();

        return $thumbnailPath;
    } catch (Exception $e) {
        echo 'Error generating thumbnail: ', $e->getMessage();
        return null;
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