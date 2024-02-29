<?php
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
$allowedExtensions = ["pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "py", "c", "zip"];

$pathinfo = pathinfo($_FILES["file"]["name"]);
$fileExtension = strtolower($pathinfo["extension"]);

// Check if the file extension is allowed
if (!in_array($fileExtension, $allowedExtensions)) {
    exit("Invalid file type");
}

// Sanitize file name to prevent directory traversal and ensure unique file names
$baseName = preg_replace("/[^\w-]/", "_", $pathinfo["filename"]);
$fileName = $baseName . "." . $fileExtension;

// Check if file already exists, append number if necessary

$uploadDirectory = "C:/wamp64/www/Capstone/EdShare/uploads/";
// $uploadDirectory = __DIR__ . "/uploads/";
$destination = $uploadDirectory . $fileName;

$i = 1;
while (file_exists($destination)) {
    $fileName = $baseName . "($i)." . $fileExtension;
    $destination = $uploadDirectory . $fileName;
    $i++;
}

// Move the uploaded file to the destination
if (!move_uploaded_file($_FILES["file"]["tmp_name"], $destination)) {
    exit("Can't move uploaded file");
}

echo "File uploaded successfully.";
?>
