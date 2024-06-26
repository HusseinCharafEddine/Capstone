<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("location:index.php");
}



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
$db = DBConnect();
$username = $_SESSION['username'];
$user = $userController->getUserByUsername($username);
$userId = $user["UserId"];
$_SESSION["userId"] = $userId;
$getUploadedDocumentsQuery = "SELECT * FROM Document WHERE UserId = (SELECT UserId FROM User WHERE Username = ?)";
$stmt = $db->prepare($getUploadedDocumentsQuery);
$stmt->execute([$username]);
$uploadedDocuments = $stmt->fetchAll(PDO::FETCH_ASSOC);
$user = $userController->getUserByUsername($username);

?>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
  data-assets-path="assets/" data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>EdShare</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="icon.svg" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="assets/vendor/fonts/boxicons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="assets/css/demo.css" />
  <link rel="stylesheet" href="assets/css/favs.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Page CSS -->

  <!-- Helpers -->
  <script src="assets/vendor/js/helpers.js"></script>
  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  <script src="assets/js/config.js"></script>

</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->

      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="icon.svg">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">EdShare</span>
          </a>

          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
          <li>
            <div id="token-container" style="display: flex; margin: 18px; margin-top:0px">
              <div id="uploads-token" style="margin-right: auto;">
                <span>
                  <img src="assets/img/icons/tokens/uploadstoken.png">
                  <?php
                  $contributionScore = $user['ContributionScore'];
                  $totalDownloaded = $user['TotalDownloaded'];
                  $tokenScore = $contributionScore - 2 * $totalDownloaded;
                  echo $tokenScore;
                  ?>
                </span>
              </div>
              <div class="vertical-divider" style="width: 20px;"></div>
              <div id="downloads-token" style="margin-left: auto;">
                <span>
                  <img src="assets/img/icons/tokens/downloadstoken.png">
                  <?php
                  echo $contributionScore;
                  ?>
                </span>
              </div>
            </div>
          </li>
          <!-- Dashboards -->

          <li class="menu-item active">
            <a href="landing.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Home">Home</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="html/favorites.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-heart"></i>
              <div data-i18n="Favorites">Favorites</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="html/downloads.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-download"></i>
              <div data-i18n="Downloads">Downloads</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="html/uploads.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-upload"></i>
              <div data-i18n="Uploads">Uploads</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="html/analytics.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-chart"></i>
              <div data-i18n="Analytics">Analytics</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="html/league-standings.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-crown"></i>
              <div data-i18n="LeagueStandings">League Standings</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="BE/logout.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-power-off"></i>
              <div data-i18n="LeagueStandings">Log Out</div>
            </a>
          </li>
        </ul>
        </ul>
      </aside>
      <!-- / Menu -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Navbar -->

        <nav
          class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
          id="layout-navbar">
          <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0   d-xl-none ">
            <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
              <i class="bx bx-menu bx-sm"></i>
            </a>
          </div>
          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse" style="width=100%;">

            <div class="navbar-nav align-items-center" style="width=100%;">
              <div class="nav-item d-flex align-items-center position-relative" style="width=100%;">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input id="searchInput" type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2"
                  placeholder="Search..." aria-label="Search..." onkeyup="fetchSearchSuggestions(this.value)">
                <div id="searchSuggestions" class="search-suggestions" style="position: absolute; top: 134%; left: 5%;background-color: #fff;
                width:100%; border-top: none; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
                max-height: 200px; overflow-y: auto; ">
                </div>
              </div>
            </div>
            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="dropdown-item" href="html/pages-account-settings-account.php">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-md avatar-online me-2"><span
                        class="avatar-initial rounded-circle bg-label-dark">
                        <?php echo ucfirst($user['FirstName'][0]) . ucfirst($user['LastName'][0]);
                        ?></span></div>
                  </div>
                </a>
              </li>
            </ul>
          </div>
          <!-- Search Small Screens -->
          <div class="navbar-search-wrapper search-input-wrapper d-none">
            <span class="twitter-typeahead" style="position: relative; display: inline-block;"><input type="text"
                class="form-control search-input container-xxl border-0 tt-input" placeholder="Search..."
                aria-label="Search..." autocomplete="off" spellcheck="false" dir="auto"
                style="position: relative; vertical-align: top;">
              <pre aria-hidden="true"
                style="position: absolute; visibility: hidden; white-space: pre; font-family: &quot;Public Sans&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Oxygen, Ubuntu, Cantarell, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 15px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;">sf</pre>
              <div class="tt-menu navbar-search-suggestion ps tt-empty"
                style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none;">
                <div class="tt-dataset tt-dataset-pages"></div>
                <div class="tt-dataset tt-dataset-files"></div>
                <div class="tt-dataset tt-dataset-members"></div>
                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                  <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 0px; right: 0px; height: 448px;">
                  <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                </div>
              </div>
            </span>
            <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
          </div>


        </nav>

        <!-- / Navbar -->

        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Content -->

          <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
              <div class="col-lg-4 col-md-12 col-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <span>Hottest Today</span>
                    <?php
                    $downloadController = new DownloadController();
                    $mostDownloadedToday = $downloadController->getMostDownloadedToday();
                    $downloadController = new DownloadController();
                    $newestDownloadedDocument = $downloadController->getNewestDownloadedDocument($mostDownloadedToday['DocumentId']);
                    $excludeDocumentIds = [$mostDownloadedToday['DocumentId'], $newestDownloadedDocument['DocumentId']];

                    $mostDownloadedYesterday = $downloadController->getMostDownloadedYesterday($excludeDocumentIds);
                    if ($mostDownloadedToday) {
                      $documentId = $mostDownloadedToday['DocumentId'];
                      $document = $documentController->getDocumentById($documentId);

                      if ($document) {
                        echo '<h5>' . $document['Title'] . '</h5>';
                      }
                    }
                    ?>
                    <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">

                      <div class="col-lg-12 col-md-6 mb-4">
                        <div class="card h-100">
                          <?php
                          $download = $downloadController->getMostDownloadedToday();
                          $document = $documentController->getDocumentById($download['DocumentId']);
                          $author = $userController->getUser((int) $document['UserId'])['Username'];
                          $isFavorited = $favoriteController->getFavoriteByUserIdAndDocumentId(
                            $userId,
                            $document['DocumentId']
                          );
                          $buttonText = ($isFavorited ? 'Remove from Favorites' : 'Add to Favorites');
                          $imgSrc = ($isFavorited ? 'assets/img/icons/unicons/heartfilled.png' : 'assets/img/icons/unicons/heart.png');
                          ?>
                          <div class="col">
                            <div class="card h-100">
                              <img class="card-img-top"
                                src="thumbnails/<?php echo $author ?>/<?php echo $document['ThumbnailPath']; ?>">
                              <div class="card-body">
                                <h5 class="card-title">
                                  <a href="app-academy-course-details.html" class="h5">
                                    <?php echo $document['Title']; ?>
                                  </a>
                                  <span style="float: right;">
                                    <a onclick="addToFavorites(<?php echo $document['DocumentId']; ?>)">
                                      <img id="heart-fav<?php echo $document['DocumentId']; ?>" class="heart-favs"
                                        src="<?php echo $imgSrc ?>">
                                    </a>
                                  </span>
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
                                  </span>
                                  <?php echo round($downloadController->getAverageRatingByDocumentId($document["DocumentId"]), 1) ?>
                                  <span class="text-warning"><i class="bx bxs-star me-1"></i></span><span
                                    class="text-muted">
                                    <?php echo "(" . $downloadController->getTotalRatingByDocumentId($document["DocumentId"]) . ")" ?>
                                  </span>
                                </div>
                                <div id="starRating_<?php echo $document['DocumentId']; ?>">
                                  <?php
                                  $testRating = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                                  if ($testRating) {
                                    $userRating = $testRating['Rating'];
                                  } else {
                                    $userRating = 0;
                                  }
                                  for ($i = 0; $i < 5; $i++) {
                                    if ($userRating !== null && $i < $userRating) {
                                      echo '<i class="bx bx-star bxs-star" style="color: #ffab00;" onclick="toggleStar(' . $i . ', ' . $document['DocumentId'] . ')"></i>';
                                    } else {
                                      echo '<i class="bx bx-star" onclick="toggleStar(' . $i . ', ' . $document['DocumentId'] . ')"></i>';
                                    }
                                  }
                                  ?>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                  <span class="text mb-3">Author: <?php echo $author; ?></span>
                                </div>
                                <div class="d-flex justify-content-center gap-3 mb-2 text-white">
                                  <?php
                                  $test = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                                  $downloadAllowed = ($tokenScore >= 2 || $test);
                                  if ($downloadAllowed): ?>
                                    <button class="btn btn-primary download-btn"
                                      data-document-id="<?php echo $document['DocumentId']; ?>"
                                      onclick="initiateDownload(<?php echo $userId; ?>, <?php echo $document['DocumentId']; ?>, 'uploads/<?php echo $author ?>/<?php echo $document['FilePath']; ?>', '<?php echo $document['Title']; ?>')">
                                      Download </button>
                                  <?php else: ?>
                                    <button class="btn btn-primary download-btn"> Insufficient Tokens! </button>
                                  <?php endif; ?>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="col-lg-4 col-md-12 col-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <span>Yesterday's Pick</span>
                    <?php

                    if ($mostDownloadedYesterday) {
                      $documentId = $mostDownloadedYesterday['DocumentId'];
                      $documentController = new DocumentController();
                      $document = $documentController->getDocumentById($documentId);

                      if ($document) {
                        echo '<h5>' . $document['Title'] . '</h5>';
                      }
                    }
                    ?>
                    <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">

                      <div class="col-lg-12 col-md-6 mb-4">
                        <div class="card h-100">
                          <?php
                          $document = $documentController->getDocumentById($mostDownloadedYesterday['DocumentId']);
                          $author = $userController->getUser((int) $document['UserId'])['Username'];
                          $isFavorited = $favoriteController->getFavoriteByUserIdAndDocumentId(
                            $userId,
                            $document['DocumentId']
                          );
                          $imgSrc = ($isFavorited ? 'assets/img/icons/unicons/heartfilled.png' : 'assets/img/icons/unicons/heart.png');
                          $buttonText = ($isFavorited ? 'Remove from Favorites' : 'Add to Favorites');
                          ?>
                          <div class="col">
                            <div class="card h-100">
                              <img class="card-img-top"
                                src="thumbnails/<?php echo $author ?>/<?php echo $document['ThumbnailPath']; ?>">
                              <div class="card-body">
                                <h5 class="card-title">
                                  <a href="app-academy-course-details.html" class="h5">
                                    <?php echo $document['Title']; ?>
                                  </a>
                                  <span style="float: right;">
                                    <a onclick="addToFavorites(<?php echo $document['DocumentId']; ?>)">
                                      <img id="heart-fav<?php echo $document['DocumentId']; ?>" class="heart-favs"
                                        src="<?php echo $imgSrc ?>">
                                    </a>
                                  </span>
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
                                  </span>
                                  <?php echo round($downloadController->getAverageRatingByDocumentId($document["DocumentId"]), 1) ?>
                                  <span class="text-warning"><i class="bx bxs-star me-1"></i></span><span
                                    class="text-muted">
                                    <?php echo "(" . $downloadController->getTotalRatingByDocumentId($document["DocumentId"]) . ")" ?>
                                  </span>
                                </div>
                                <div id="starRating_<?php echo $document['DocumentId']; ?>">
                                  <?php
                                  $testRating = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                                  if ($testRating) {
                                    $userRating = $testRating['Rating'];
                                  } else {
                                    $userRating = 0;
                                  }
                                  for ($i = 0; $i < 5; $i++) {
                                    if ($userRating !== null && $i < $userRating) {
                                      echo '<i class="bx bx-star bxs-star" style="color: #ffab00;" onclick="toggleStar(' . $i . ', ' . $document['DocumentId'] . ')"></i>';
                                    } else {
                                      echo '<i class="bx bx-star" onclick="toggleStar(' . $i . ', ' . $document['DocumentId'] . ')"></i>';
                                    }
                                  }
                                  ?>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                  <span class="text mb-3">Author: <?php echo $author; ?></span>
                                </div>
                                <div class="d-flex justify-content-center gap-3 mb-2 text-white">
                                  <!-- Download Button -->
                                  <?php
                                  $test = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                                  $downloadAllowed = ($tokenScore >= 2 || $test);
                                  if ($downloadAllowed): ?>
                                    <button class="btn btn-primary download-btn"
                                      data-document-id="<?php echo $document['DocumentId']; ?>"
                                      onclick="initiateDownload(<?php echo $userId; ?>, <?php echo $document['DocumentId']; ?>, 'uploads/<?php echo $author ?>/<?php echo $document['FilePath']; ?>', '<?php echo $document['Title']; ?>')">
                                      Download </button>
                                  <?php else: ?>
                                    <button class="btn btn-primary download-btn"> Insufficient Tokens! </button>
                                  <?php endif; ?>
                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
              <div class="col-lg-4 col-md-12 col-6 mb-4">
                <div class="card">
                  <div class="card-body">
                    <span>New Drops</span>
                    <?php


                    if ($newestDownloadedDocument) {
                      $documentId = $newestDownloadedDocument['DocumentId'];
                      $documentController = new DocumentController();
                      $document = $documentController->getDocumentById($documentId);

                      if ($document) {
                        echo '<h5>' . $document['Title'] . '</h5>';
                      }
                    }
                    ?>
                    <div class="row row-cols-1 row-cols-md-3 g-3 mb-3">

                      <div class="col-lg-12 col-md-6 mb-4">
                        <div class="card h-100">
                          <?php
                          $document = $documentController->getDocumentById($newestDownloadedDocument['DocumentId']);
                          $author = $userController->getUser((int) $document['UserId'])['Username'];
                          $isFavorited = $favoriteController->getFavoriteByUserIdAndDocumentId(
                            $userId,
                            $document['DocumentId']
                          );
                          $buttonText = ($isFavorited ? 'Remove from Favorites' : 'Add to Favorites');
                          $imgSrc = ($isFavorited ? 'assets/img/icons/unicons/heartfilled.png' : 'assets/img/icons/unicons/heart.png');

                          ?>
                          <div class="col">
                            <div class="card h-100">
                              <img class="card-img-top"
                                src="thumbnails/<?php echo $author ?>/<?php echo $document['ThumbnailPath']; ?>">
                              <div class="card-body">
                                <h5 class="card-title">
                                  <a href="app-academy-course-details.html" class="h5">
                                    <?php echo $document['Title']; ?>
                                  </a>
                                  <span style="float: right;">
                                    <a onclick="addToFavorites(<?php echo $document['DocumentId']; ?>)">
                                      <img id="heart-fav<?php echo $document['DocumentId']; ?>" class="heart-favs"
                                        src="<?php echo $imgSrc ?>">
                                    </a>
                                  </span>
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
                                  </span>
                                  <?php echo round($downloadController->getAverageRatingByDocumentId($document["DocumentId"]), 1) ?>
                                  <span class="text-warning"><i class="bx bxs-star me-1"></i></span><span
                                    class="text-muted">
                                    <?php echo "(" . $downloadController->getTotalRatingByDocumentId($document["DocumentId"]) . ")" ?>
                                  </span>
                                </div>
                                <div id="starRating_<?php echo $document['DocumentId']; ?>">
                                  <?php
                                  $testRating = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                                  if ($testRating) {
                                    $userRating = $testRating['Rating'];
                                  } else {
                                    $userRating = 0;
                                  }
                                  for ($i = 0; $i < 5; $i++) {
                                    if ($userRating !== null && $i < $userRating) {
                                      echo '<i class="bx bx-star bxs-star" style="color: #ffab00;" onclick="toggleStar(' . $i . ', ' . $document['DocumentId'] . ')"></i>';
                                    } else {
                                      echo '<i class="bx bx-star" onclick="toggleStar(' . $i . ', ' . $document['DocumentId'] . ')"></i>';
                                    }
                                  }
                                  ?>
                                </div>
                                <div class="d-flex align-items-center mb-3">
                                  <span class="text mb-3">Author: <?php echo $author; ?></span>
                                </div>
                                <div class="d-flex justify-content-center gap-3 mb-2 text-white">
                                  <!-- Download Button -->
                                  <?php
                                  $test = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                                  $downloadAllowed = ($tokenScore >= 2 || $test);
                                  if ($downloadAllowed): ?>
                                    <button class="btn btn-primary download-btn"
                                      data-document-id="<?php echo $document['DocumentId']; ?>"
                                      onclick="initiateDownload(<?php echo $userId; ?>, <?php echo $document['DocumentId']; ?>, 'uploads/<?php echo $author ?>/<?php echo $document['FilePath']; ?>', '<?php echo $document['Title']; ?>')">
                                      Download </button>
                                  <?php else: ?>
                                    <button class="btn btn-primary download-btn"> Insufficient Tokens! </button>
                                  <?php endif; ?>

                                </div>

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>
                </div>
              </div>

              <div class="card mb-4">
                <div class="card-header">
                  <h5 class="card-title">Filter</h5>
                  <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                    <div class="col-md-4 ">
                      <label for="defaultFormControlInput" class="form-label">University</label>
                      <select id="University" class="form-select text-capitalize">
                        <option value="0" diasbled></option>
                        <?php
                        $universities = $universityController->getAllUniversities();
                        foreach ($universities as $university) {
                          echo "<option value='" . $university['UniversityId'] . "'>" . $university['UniversityName'] . "</option>";
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-4 ">
                      <label for="defaultFormControlInput" class="form-label">Course</label>
                      <select id="Category" class="form-select text-capitalize">
                        <option value="0" diasbled></option>
                        <?php
                        $courses = $courseController->getAllCourses();
                        foreach ($courses as $course) {
                          echo "<option value='" . $course['CourseId'] . "'>" . strtoupper($course['CourseCode']) . "</option>";
                        }
                        ?>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label for="defaultFormControlInput" class="form-label">Rating</label>
                      <input id="Rating" class="form-control" aria-describedby="defaultFormControlHelp">

                    </div>
                    <div class="col-md-4 mt-3">
                      <button id="applyFilterBtn" class="btn btn-primary me-2">Apply Filter</button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <?php
                  $uploadsPerPage = 20;
                  $totalUploads = $documentController->getCountOfDocuments();
                  $totalPages = ceil($totalUploads / $uploadsPerPage);

                  $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

                  $offset = ($page - 1) * $uploadsPerPage;

                  $universityId = isset($_GET['universityId']) ? intval($_GET['universityId']) : 0;
                  $courseId = isset($_GET['courseId']) ? intval($_GET['courseId']) : 0;
                  $rating = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
                  $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

                  $filter = array();
                  if ($universityId != 0) {
                    $filter['universityId'] = $universityId;
                  }
                  if ($courseId != 0) {
                    $filter['courseId'] = $courseId;
                  }
                  if (!empty($rating)) {
                    $filter['rating'] = $rating;
                  }
                  $downloadsForPage = $documentController->fetchAllDocumentsForPage($offset, $uploadsPerPage, $filter, $searchTerm);

                  ?>

                  <h6 class="mb-5 mt-5">Documents</h6>

                  <div class="row row-cols-1 row-cols-md-4 g-3 mb-3">
                    <?php
                    $users = $userController->getAllUsers();
                    foreach ($downloadsForPage as $download): ?>
                      <?php
                      $document = $documentController->getDocumentById($download['DocumentId']);
                      $author = $userController->getUser((int) $document['UserId'])['Username'];
                      $isFavorited = $favoriteController->getFavoriteByUserIdAndDocumentId($userId, $document['DocumentId']);
                      $buttonText = ($isFavorited ? 'Remove from Favorites' : 'Add to Favorites');
                      $imgSrc = ($isFavorited ? 'assets/img/icons/unicons/heartfilled.png' : 'assets/img/icons/unicons/heart.png');
                      ?>

                      <div class="col">
                        <div class="card h-100">
                          <img class="card-img-top"
                            src="thumbnails/<?php echo $author ?>/<?php echo $document['ThumbnailPath']; ?>">
                          <div class="card-body">
                            <hr>
                            <h5 class="card-title">
                              <a href="app-academy-course-details.html" class="h5">
                                <?php echo $document['Title']; ?>
                              </a>
                              <span style="float: right;">
                                <a onclick="addToFavorites(<?php echo $document['DocumentId']; ?>)">
                                  <img id="heart-fav<?php echo $document['DocumentId']; ?>" class="heart-favs"
                                    src="<?php echo $imgSrc ?>">
                                </a>
                              </span>
                            </h5>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                              <span class="badge bg-label-primary">
                                <?php
                                $course = $courseController->getCourse($document['CourseId']);
                                if ($course) {
                                  $university = $universityController->getUniversityById($course['UniversityId']);
                                  echo $university ? $university['UniversityAcronym'] : 'University Not Found';
                                } else {
                                  echo 'Course Not Found';
                                }
                                ?>
                              </span>
                              <span class="badge bg-label-primary">
                                <?php echo $course ? $course['CourseCode'] : 'Course not found'; ?>
                              </span>
                              <?php echo round($downloadController->getAverageRatingByDocumentId($document["DocumentId"]), 1) ?>
                              <span class="text-warning"><i class="bx bxs-star me-1"></i></span><span class="text-muted">
                                <?php echo "(" . $downloadController->getTotalRatingByDocumentId($document["DocumentId"]) . ")" ?>
                              </span>
                            </div>
                            <div id="starRating_<?php echo $document['DocumentId']; ?>">
                              <?php
                              $canRate;
                              $testRating = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                              if ($testRating) {
                                $userRating = $testRating['Rating'];
                                $canRate = true;
                              } else {
                                $userRating = 0;
                                $canRate = false;
                              }


                              for ($i = 0; $i < 5; $i++) {
                                if ($canRate) {
                                  if ($userRating !== null && $i < $userRating) {
                                    echo '<i class="bx bx-star bxs-star" style="color: #ffab00;" onclick="toggleStar(' . $i . ', ' . $document['DocumentId'] . ')"></i>';
                                  } else {
                                    echo '<i class="bx bx-star" onclick="toggleStar(' . $i . ', ' . $document['DocumentId'] . ')"></i>';
                                  }
                                } else {
                                  if ($i < $userRating) {
                                    echo '<i class="bx bx-star bxs-star" style="color: #ffab00;"></i>';
                                  } else {
                                    echo '<i class="bx bx-star"></i>';
                                  }
                                }
                              }
                              ?>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                              <span class="text mb-3">Author:
                                <?php echo $author; ?>
                              </span>
                            </div>
                            <div class="d-flex justify-content-center gap-3 mb-2 text-white">
                              <!-- Download Button -->
                              <?php
                              $test = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                              $downloadAllowed = ($tokenScore >= 2 || $test);
                              if ($downloadAllowed): ?>
                                <button class="btn btn-primary download-btn"
                                  data-document-id="<?php echo $document['DocumentId']; ?>"
                                  onclick="initiateDownload(<?php echo $userId; ?>, <?php echo $document['DocumentId']; ?>, 'uploads/<?php echo $author ?>/<?php echo $document['FilePath']; ?>', '<?php echo $document['Title']; ?>')">
                                  Download </button>
                              <?php else: ?>
                                <button class="btn btn-primary download-btn"> Insufficient Tokens! </button>
                              <?php endif; ?>
                            </div>



                          </div>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>

                  <ul class="pagination justify-content-center mt-5">
                    <?php if ($page > 1): ?>
                      <li class="page-item prev">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>"><i
                            class="tf-icon bx bx-chevrons-left"></i></a>
                      </li>
                    <?php endif; ?>

                    <?php
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $startPage + 4);

                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                      <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>">
                          <?php echo $i; ?>
                        </a>
                      </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                      <li class="page-item next">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>"><i
                            class="tf-icon bx bx-chevrons-right"></i></a>
                      </li>
                    <?php endif; ?>
                  </ul>


                  <!-- / Footer -->

                  <div class="content-backdrop fade"></div>

                </div>
                <!-- Content wrapper -->
              </div>
              <!-- / Layout page -->

              <!-- Overlay -->
              <div class="layout-overlay layout-menu-toggle"></div>
            </div>
            <!-- / Layout wrapper -->



            <!-- Core JS -->
            <!-- build:js assets/vendor/js/core.js -->

            <script src="assets/vendor/libs/jquery/jquery.js"></script>
            <script src="assets/vendor/libs/popper/popper.js"></script>
            <script src="assets/vendor/js/bootstrap.js"></script>
            <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
            <script src="assets/vendor/js/menu.js"></script>

            <!-- endbuild -->

            <!-- Vendors JS -->
            <script src="assets/vendor/libs/apex-charts/apexcharts.js"></script>

            <!-- Main JS -->
            <script src="assets/js/main.js"></script>

            <!-- Page JS -->
            <script src="assets/js/dashboards-analytics.js"></script>



            <script>
              function submitFilter() {
                var universityId = document.getElementById("University").value;
                var courseId = document.getElementById("Category").value;
                var rating = document.getElementById("Rating").value;
                var searchTerm = document.getElementById("searchInput").value;

                var url = window.location.pathname +
                  "?universityId=" + universityId +
                  "&courseId=" + courseId +
                  "&rating=" + rating +
                  "&searchTerm=" + encodeURIComponent(searchTerm);

                window.location.href = url;
              }

              function fetchSearchSuggestions() {
                var searchTerm = document.getElementById("searchInput").value;
                if (searchTerm.trim() === '') {
                  return;
                }

                $.ajax({
                  url: 'BE/fetchSearchSuggestions.php',
                  method: 'GET',
                  data: { searchTerm: searchTerm },
                  success: function (response) {
                    var suggestionsDropdown = document.getElementById("searchSuggestions");
                    suggestionsDropdown.innerHTML = response;
                    suggestionsDropdown.style.display = 'block';
                  },
                  error: function (xhr, status, error) {
                    console.error(error);
                  }
                });
              }

              document.addEventListener("DOMContentLoaded", function () {
                document.querySelector(".bx-search").addEventListener("click", function () {
                  submitFilter();
                });

                document.getElementById("searchInput").addEventListener("keypress", function (event) {
                  if (event.key === "Enter") {
                    submitFilter(); n
                  }
                });

                document.getElementById("searchInput").addEventListener("input", function () {
                  fetchSearchSuggestions();
                });
                document.addEventListener("click", function (event) {
                  var clickedElement = event.target;
                  if (clickedElement.classList.contains("search-suggestion")) {
                    document.getElementById("searchInput").value = clickedElement.textContent.trim();
                    document.getElementById("searchSuggestions").style.display = "none";
                  }
                });

                var applyFilterBtn = document.getElementById('applyFilterBtn');

                applyFilterBtn.addEventListener('click', function () {
                  submitFilter();
                });

              });
            </script>

            <script>
              const userId = <?php echo isset($_SESSION['userId']) ? $_SESSION['userId'] : 'null'; ?>;
              function toggleStar(index, documentId) {
                const stars = document.querySelectorAll('#starRating_' + documentId + ' .bx-star');
                console.log(stars);
                const newRating = index + 1;

                for (let i = 0; i < stars.length; i++) {
                  const starGroupIndex = Math.floor(i / 5);
                  const starIndex = i % 5;

                  if (starIndex <= index) {
                    stars[i].classList.add('bxs-star');
                    stars[i].style.color = '#ffab00';
                  } else {
                    stars[i].classList.remove('bxs-star');
                    stars[i].style.color = '';
                  }
                }

                fetch('BE/updateRating.php', {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json'
                  },
                  body: JSON.stringify({
                    userId: userId,
                    documentId: documentId,
                    newRating: newRating
                  })
                })
                  .then(response => {
                    if (response.ok) {
                      console.log('Rating updated successfully');
                    } else {
                      console.error('Failed to update rating:', response.statusText);
                    }
                  })
                  .catch(error => {
                    console.error('Error updating rating:', error);
                  });
              }
            </script>

            <script>
              function addToFavorites(documentId) {
                $.ajax({
                  type: 'POST',
                  url: 'BE/toggleFavorite.php',
                  data: {
                    documentId: documentId
                  },
                  success: function (response) {
                    var heartIcons = document.querySelectorAll('[id^="heart-fav' + documentId + '"]');

                    heartIcons.forEach(function (heartIcon) {
                      if (response === 'Favorite added successfully') {
                        heartIcon.src = 'assets/img/icons/unicons/heartfilled.png';
                      } else if (response === 'Favorite removed successfully') {
                        heartIcon.src = 'assets/img/icons/unicons/heart.png';
                      }
                    });
                  },
                  error: function () {
                    console.error('Error toggling favorite.');
                  }
                });
              }
            </script>

            <script>
              function initiateDownload(userId, documentId, filePath, fileName) {
                $.ajax({
                  url: "BE/download.php",
                  type: "POST",
                  data: { documentId: documentId },
                  success: function (response) {
                    if (response === "success") {
                      var downloadLink = filePath;
                      var anchor = document.createElement('a');
                      anchor.href = downloadLink;
                      console.log(fileName);
                      anchor.download = fileName;
                      document.body.appendChild(anchor);
                      anchor.click();
                      document.body.removeChild(anchor);
                    } else {
                      alert("Failed to download the document. Please try again later.");
                    }
                  },
                  error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                    alert("An error occurred while processing your request. Please try again later.");
                  }
                });
              }
            </script>



            </script>
            <script>
              window.embeddedChatbotConfig = {
                chatbotId: "WPQBRApLkrR6WgsWXNKXS",
                domain: "www.chatbase.co"
              }
            </script>
            <script src="https://www.chatbase.co/embed.min.js" chatbotId="WPQBRApLkrR6WgsWXNKXS"
              domain="www.chatbase.co" defer>
              </script>



</body>

</html>