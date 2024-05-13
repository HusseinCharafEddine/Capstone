<?php
require ("BE/common/commonFunctions.php");
require ("BE/coursesController.php");
require ("BE/userController.php");
require ("BE/documentController.php");
require ("BE/universityController.php");
require ("BE/downloadController.php");
require ("BE/favoriteController.php");

$userController = new UserController();
$courseController = new CoursesController();
$documentController = new DocumentController();
$universityController = new UniversityController();
$downloadController = new DownloadController();
$favoriteController = new FavoriteController();
if (isset($_GET['documentId'])) {
    $documentId = $_GET['documentId'];
}

foreach ($downloadsForPage as $download):
    $document = $documentController->getDocumentById($download['DocumentId']);
    $author = $userController->getUser((int) $document['UserId'])['Username'];
    $isFavorited = $favoriteController->getFavoriteByUserIdAndDocumentId($userId, $document['DocumentId']);
    $buttonText = ($isFavorited ? 'Remove from Favorites' : 'Add to Favorites');
    ?>

    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card h-100">
            <img class="card-img-top" src="thumbnails/<?php echo $author ?>/<?php echo $document['ThumbnailPath']; ?>">
            <div class="card-body">
                <h5 class="card-title">
                    <a href="app-academy-course-details.html" class="h5"><?php echo $document['Title']; ?></a>
                </h5>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="badge bg-label-primary">
                        <?php
                        $course = $courseController->getCourse($document['CourseId']);
                        echo $course ? $course['CourseCode'] : 'Course Not Found';
                        ?>
                    </span>
                    <span class="badge bg-label-primary">
                        <?php
                        $university = $universityController->getUniversityById($course['UniversityId']);
                        echo $university ? $university['UniversityAcronym'] : 'University Not Found';
                        ?>
                    </span>
                    <span><?php echo $document['Rating']; ?> <i class="bx bxs-star me-1"></i>(1.23k)</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <span class="text mb-3">Author: <?php echo $author; ?></span>
                </div>
                <div class="d-flex justify-content-center gap-3 mb-2 text-white">
                    <a href="uploads/<?php echo $username ?>/<?php echo $document['FilePath']; ?>" download>
                        <div class="d-flex align-items-center bg-primary rounded p-1">
                            <button class="btn btn-primary me-2">Download</button>
                            <i class="bx bx-download lh-1 scaleX-n1-rtl"></i>
                        </div>
                    </a>
                </div>
                <div class="d-flex align-items-center bg-primary rounded p-1">
                    <button class="btn btn-primary toggle-favorite"
                        data-document-id="<?php echo $document['DocumentId']; ?>">
                        <?php echo $buttonText; ?> <i class="bx bx-star"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>