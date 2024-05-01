<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("location:../index.php");
}
require ("../BE/common/commonFunctions.php");
require ("../BE/userController.php");
require ("../BE/downloadController.php");
require ("../BE/documentController.php");

$userController = new UserController();
$documentController = new DocumentController();
$downloadController = new DownloadController();

$db = DBConnect();
$username = $_SESSION['username'];
$user = $userController->getUserByUsername($username);
$userId = $user['UserId'];
?>
<html lang="en" class="light-style layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default"
  data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>EdShare</title>

  <meta name="description" content="" />

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../icon.svg" />

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Page CSS -->

  <!-- Helpers -->
  <script src="../assets/vendor/js/helpers.js"></script>
  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  <script src="../assets/js/config.js"></script>
</head>

<body>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->

      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="../index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
              <img src="../icon.svg">
            </span>
            <span class="app-brand-text demo menu-text fw-bold ms-2">EdShare</span>
          </a>

          <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
          <!-- Dashboards -->
          <li class="menu-item">
            <a href="../landing.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Email">Home</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="favorites.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-heart"></i>
              <div data-i18n="Favorites">Favorites</div>
            </a>
          </li>
          <li class="menu-item">
            <a href="downloads.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-download"></i>
              <div data-i18n="Downloads">Downloads</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="uploads.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-upload"></i>
              <div data-i18n="Uploads">Uploads</div>
            </a>
          </li>
          <li class="menu-item active">
            <a href="analytics.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-chart"></i>
              <div data-i18n="Analytics">Analytics</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="league-standings.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-crown"></i>
              <div data-i18n="LeagueStandings">League Standings</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="../BE/logout.php" class="menu-link">
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
          <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
            <!-- Search -->
            <div class="navbar-nav align-items-center">
              <div class="nav-item navbar-search-wrapper mb-0">
                <a class="nav-item nav-link search-toggler px-0" href="javascript:void(0);">
                </a>
              </div>
            </div>
            <!-- /Search -->
            <ul class="navbar-nav flex-row align-items-center ms-auto">
              <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
              </li>
              <!-- /Language -->
              <!-- Quick links  -->
              <li class="nav-item dropdown-shortcuts navbar-dropdown dropdown me-2 me-xl-0">

              </li>
              <!-- Quick links -->


              <!-- Style Switcher -->
              <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">

              </li>
              <!-- / Style Switcher-->


              <!-- User -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="dropdown-item" href="pages-account-settings-account.php">
                  <div class="avatar-wrapper">
                    <div class="avatar avatar-md avatar-online me-2"><span
                        class="avatar-initial rounded-circle bg-label-dark">
                        <?php echo ucfirst($user['FirstName'][0]) . ucfirst($user['LastName'][0]);
                        ?></span></div>
                  </div>
                </a>
              </li>
              <!--/ User -->


            </ul>
          </div>


          <!-- Search Small Screens -->
          <div class="navbar-search-wrapper search-input-wrapper d-none">
            <span class="twitter-typeahead" style="position: relative; display: inline-block;"><input type="text"
                class="form-control search-input container-xxl bdownload-0 tt-input" placeholder="Search..."
                aria-label="Search..." autocomplete="off" spellcheck="false" dir="auto"
                style="position: relative; vertical-align: top;">
              <pre aria-hidden="true"
                style="position: absolute; visibility: hidden; white-space: pre; font-family: &quot;Public Sans&quot;, -apple-system, BlinkMacSystemFont, &quot;Segoe UI&quot;, Oxygen, Ubuntu, Cantarell, &quot;Fira Sans&quot;, &quot;Droid Sans&quot;, &quot;Helvetica Neue&quot;, sans-serif; font-size: 15px; font-style: normal; font-variant: normal; font-weight: 400; word-spacing: 0px; letter-spacing: 0px; text-indent: 0px; text-rendering: auto; text-transform: none;"><div class="open_grepper_editor" title="Edit &amp; Save To Grepper"></div></pre>
              <div class="tt-menu navbar-search-suggestion ps"
                style="position: absolute; top: 100%; left: 0px; z-index: 100; display: none;">
                <div class="tt-dataset tt-dataset-pages"></div>
                <div class="tt-dataset tt-dataset-files"></div>
                <div class="tt-dataset tt-dataset-members"></div>
                <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                  <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 0px; right: 0px;">
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
              <div class="row">
                <div class="col-lg-4 col-md-12 col-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                          <i style="font-size: 40px;" class="menu-icon tf-icons bx bx-download"></i>
                        </div>
                      </div>
                      <span>Total Downloads</span>
                      <h3 class="card-title text-nowrap mb-1">
                        <?php echo $totalDownloadsForUser = $user['TotalDownloaded'];
                        $growthPercentage = $downloadController->computeDownloadGrowthPercentage($userId); ?>
                      </h3>
                      <small class="<?php echo getGrowthClass($growthPercentage); ?>"><i
                          class="<?php echo getGrowthClassForArrow($growthPercentage); ?>"> </i>
                        <?php echo formatGrowthPercentage($growthPercentage); ?>
                      </small>
                    </div>
                  </div>
                </div>

                <div class="col-lg-4 col-md-12 col-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                          <i style="font-size:40px;" class="menu-icon tf-icons bx bx-upload"></i>
                        </div>
                      </div>
                      <span>Total Uploads</span>
                      <h3 class="card-title text-nowrap mb-1">
                        <?php echo $totalUploadsForUser = $documentController->getDocumentCountByUserId($user['UserId']);
                        $growthPercentageUploads = $documentController->computeDocumentGrowthPercentage($userId); ?>
                      </h3>
                      <small class="<?php echo getGrowthClass($growthPercentageUploads); ?>"><i
                          class="<?php echo getGrowthClassForArrow($growthPercentageUploads); ?>"> </i>
                        <?php echo formatGrowthPercentage($growthPercentageUploads); ?>
                      </small>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Rating start-->
              <div class="col-lg-12 col-md-12 col-8 mb-4">
                <div class="card">
                  <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                      Average Rating
                    </div>
                    <div class="row">
                      <div class="col-md-12 course-details-content">
                        <div class="course-content">
                          <div class="row row--30">
                            <div class="col-lg-4">
                              <div class="rating-box">
                                <div class="rating-number">
                                  <?php echo $downloadController->getAverageRatingByUserId($userId) ?>
                                </div>
                                <div class="rating"> <i class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star"
                                    aria-hidden="true"></i> <i class="fa fa-star" aria-hidden="true"></i> <i
                                    class="fa fa-star" aria-hidden="true"></i> <i class="fa fa-star"
                                    aria-hidden="true"></i> </div>
                                <span>(<?php echo $ratingTotalCount = $downloadController->getTotalNonNullRatingsByUserId($userId) ?>
                                  Review)</span>
                              </div>
                            </div>
                            <div class="col-lg-8">
                              <div class="review-wrapper">
                                <div class="single-progress-bar">
                                  <div class="rating-text"> 5 <i class="fa fa-star" aria-hidden="true"></i> </div>
                                  <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php $rating5 = $downloadController->getCountOfRatingsEqualTo($userId, 5);
                                    if ($ratingTotalCount == 0) {
                                      echo 0;
                                    } else {
                                      echo $rating5 / $ratingTotalCount * 100;
                                    }
                                    ?>%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <span class="rating-value">
                                    <?php echo $rating5; ?></span>
                                </div>
                                <div class="single-progress-bar">
                                  <div class="rating-text"> 4 <i class="fa fa-star" aria-hidden="true"></i> </div>
                                  <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php $rating4 = $downloadController->getCountOfRatingsEqualTo($userId, 4);
                                    if ($ratingTotalCount == 0) {
                                      echo 0;
                                    } else {
                                      echo $rating4 / $ratingTotalCount * 100;
                                    }
                                    ?>%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <span class="rating-value">
                                    <?php echo $rating4; ?></span>
                                  </span>
                                </div>
                                <div class="single-progress-bar">
                                  <div class="rating-text"> 3 <i class="fa fa-star" aria-hidden="true"></i> </div>
                                  <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php $rating3 = $downloadController->getCountOfRatingsEqualTo($userId, 3);
                                    if ($ratingTotalCount == 0) {
                                      echo 0;
                                    } else {
                                      echo $rating3 / $ratingTotalCount * 100;
                                    }
                                    ?>%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <span class="rating-value">
                                    <?php echo $rating3; ?></span>
                                  </span>
                                </div>
                                <div class="single-progress-bar">
                                  <div class="rating-text"> 2 <i class="fa fa-star" aria-hidden="true"></i> </div>
                                  <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php $rating2 = $downloadController->getCountOfRatingsEqualTo($userId, 2);
                                    if ($ratingTotalCount == 0) {
                                      echo 0;
                                    } else {
                                      echo $rating2 / $ratingTotalCount * 100;
                                    }
                                    ?>%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <span class="rating-value">
                                    <?php echo $rating2; ?></span>
                                  </span>
                                </div>
                                <div class="single-progress-bar">
                                  <div class="rating-text"> 1 <i class="fa fa-star" aria-hidden="true"></i> </div>
                                  <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: <?php $rating1 = $downloadController->getCountOfRatingsEqualTo($userId, 1);
                                    if ($ratingTotalCount == 0) {
                                      echo 0;
                                    } else {
                                      echo $rating1 / $ratingTotalCount * 100;
                                    }
                                    ?>%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                  </div>
                                  <span class="rating-value">
                                    <?php echo $rating1; ?></span>
                                  </span>
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

              <!-- Rating end-->

              <!-- Top downloads table-->

              <div class="col-lg-12 col-md-12 col-12 mb-4">
                <div class="card">
                  <h5 class="card-header bg-success text-white">Your Top Downloaded Contributions </h5>
                  <div class="table-responsive text-nowrap">
                    <?php
                    // Pagination parameters
                    $documentsPerPage = 10;
                    $currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
                    $offset = ($currentPage - 1) * $documentsPerPage;

                    // Fetch documents for the current page
                    $documents = $documentController->fetchAllDocumentsForUser($userId, $offset, $documentsPerPage);
                    ?>
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>Document title</th>
                          <th>Category</th>
                          <th>Rating</th>
                          <th># of downloads</th>
                          <th>Publishing date</th>
                        </tr>
                      </thead>
                      <tbody class="table-bdownload-bottom-0">
                        <?php foreach ($documents as $document): ?>
                          <tr>
                            <td>
                              <span class="fw-medium"><?php echo htmlspecialchars($document['Title']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($document['Category']); ?></td>
                            <td>
                              <?php
                              $documentId = $document['DocumentId'];
                              $averageRating = $documentController->getAverageRatingByDocumentId($documentId);
                              echo number_format($averageRating, 1); // Display average rating rounded to 1 decimal point
                              ?>
                            </td>
                            <td>
                              <?php
                              $totalDownloads = $documentController->getTotalDownloadsByDocumentId($documentId);
                              echo $totalDownloads;
                              ?>
                            </td>
                            <td><span class="badge bg-label-primary me-1">
                                <?php echo htmlspecialchars(date('Y-m-d', strtotime($document['Date']))); ?>
                              </span>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>

                    <?php
                    // Pagination controls
                    $totalDocuments = $documentController->getDocumentCountByUserId($userId);
                    $totalPages = ceil($totalDocuments / $documentsPerPage);

                    if ($totalPages > 1) {
                      echo '<ul class="pagination justify-content-center">';
                      for ($i = 1; $i <= $totalPages; $i++) {
                        $isActive = ($i === $currentPage) ? 'active' : '';
                        echo '<li class="page-item ' . $isActive . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                      }
                      echo '</ul>';
                    }
                    ?>
                  </div>
                </div>
              </div>
              <!--Top downloads end-->



              <!-- Document Statistics -->
              <div class="col-md-8 col-lg- col-xl-12 download-0 mb-4">
                <div class="card h-100">
                  <div class="card-header d-flex align-items-center justify-content-between pb-0">
                    <div class="card-title mb-0">
                      <h5 class="m-0 me-2">Document Statistics</h5>
                    </div>
                    <!-- <div class="dropdown">
                      <button class="btn p-0" type="button" id="orederStatistics" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                      </button>
                      <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orederStatistics">
                        <a class="dropdown-item" href="javascript:void(0);">Select All</a>
                        <a class="dropdown-item" href="javascript:void(0);">Refresh</a>
                        <a class="dropdown-item" href="javascript:void(0);">Share</a>
                      </div>
                    </div> -->
                  </div>
                  <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <div class="d-flex flex-column align-items-center gap-1">
                        <h2 class="mb-2"><?php echo $totalDownloadsForUser ?></h2>
                        <span>Total Downloads</span>
                      </div>
                      <div id="downloadStatisticsChart"></div>
                    </div>
                    <ul class="p-0 m-0">
                      <li class="d-flex mb-4 pb-1">
                        <div class="avatar flex-shrink-0 me-3">
                          <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-edit"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <h6 class="mb-0">Summaries</h6>
                          </div>
                          <div class="user-progress">
                            <small class="fw-medium"><?php $summaryDownloadCount = $documentController->getTotalDownloadsByUserIdAndType($userId, "Summary");
                            echo $summaryDownloadCount; ?></small>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex mb-4 pb-1">
                        <div class="avatar flex-shrink-0 me-3">
                          <span class="avatar-initial rounded bg-label-success"><i class="bx bx-note"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <h6 class="mb-0">Notes</h6>
                          </div>
                          <div class="user-progress">
                            <small class="fw-medium"><?php $notesDownloadCount = $documentController->getTotalDownloadsByUserIdAndType($userId, "Notes");
                            echo $notesDownloadCount; ?></small>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex mb-4 pb-1">
                        <div class="avatar flex-shrink-0 me-3">
                          <span class="avatar-initial rounded bg-label-info"><i class="bx bx-math"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <h6 class="mb-0">Exercises</h6>
                          </div>
                          <div class="user-progress">
                            <div class="user-progress">
                              <small class="fw-medium"><?php $exercisesDownloadCount = $documentController->getTotalDownloadsByUserIdAndType($userId, "Exercises");
                              echo $exercisesDownloadCount; ?></small>
                            </div>
                          </div>
                        </div>
                      </li>
                      <li class="d-flex">
                        <div class="avatar flex-shrink-0 me-3">
                          <span class="avatar-initial rounded bg-label-secondary"><i
                              class="bx bx-spreadsheet"></i></span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                          <div class="me-2">
                            <h6 class="mb-0">Practice sheets</h6>
                          </div>
                          <div class="user-progress">
                            <small class="fw-medium"><?php $practiceSheetsDownloadCount = $documentController->getTotalDownloadsByUserIdAndType($userId, "Practice Sheets");
                            echo $practiceSheetsDownloadCount; ?></small>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <!--/ Document Statistics -->
            </div>
          </div>
          <!-- / Content -->


          <div class="content-backdrop fade"></div>
        </div>
        <!-- Content wrapper -->
      </div>
      <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
  </div>
  <!-- / Layout wrapper -->



  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->

  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>

  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->
  <script src="../assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="../assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script>

    (function () {
      let cardColor, headingColor, axisColor, shadeColor, bdownloadColor;

      cardColor = config.colors.cardColor;
      headingColor = config.colors.headingColor;
      axisColor = config.colors.axisColor;
      bdownloadColor = config.colors.bdownloadColor; const chartdownloadStatistics = document.querySelector('#downloadStatisticsChart'),
        downloadChartConfig = {
          chart: {
            height: 165,
            width: 130,
            type: 'donut'
          },
          labels: ['Summaries', 'Practice Sheets', 'Exercises ', 'Notes'],
          <?php
          $totalPercentage = $summaryDownloadCount + $practiceSheetsDownloadCount + $exercisesDownloadCount + $notesDownloadCount;

          // If all counts are zero, set them to 25%
          if ($totalPercentage === 0) {
            $summaryPercentage = $practiceSheetsPercentage = $exercisesPercentage = $notesPercentage = 25;
          } else {
            // Calculate the individual percentages
            $summaryPercentage = ($summaryDownloadCount / $totalDownloadsForUser) * 100;
            $practiceSheetsPercentage = ($practiceSheetsDownloadCount / $totalDownloadsForUser) * 100;
            $exercisesPercentage = ($exercisesDownloadCount / $totalDownloadsForUser) * 100;
            $notesPercentage = ($notesDownloadCount / $totalDownloadsForUser) * 100;
          } ?>
          series: [
            <?php echo $summaryPercentage . ",";
            echo $practiceSheetsPercentage . ",";
            echo $exercisesPercentage . ",";
            echo $notesPercentage;
            ?>
          ],
          colors: [config.colors.primary, config.colors.secondary, config.colors.info, config.colors.success],
          stroke: {
            width: 5,
            colors: [cardColor]
          },
          dataLabels: {
            enabled: false,
            formatter: function (val, opt) {
              return parseInt(val) + '%';
            }
          },
          legend: {
            show: false
          },
          grid: {
            padding: {
              top: 0,
              bottom: 0,
              right: 15
            }
          },
          states: {
            hover: {
              filter: { type: 'none' }
            },
            active: {
              filter: { type: 'none' }
            }
          },
          plotOptions: {
            pie: {
              donut: {
                size: '75%',
                labels: {
                  show: true,
                  value: {
                    fontSize: '1.5rem',
                    fontFamily: 'Public Sans',
                    color: headingColor,
                    offsetY: -15,
                    formatter: function (val) {
                      return parseInt(val) + '%';
                    }
                  },
                  name: {
                    offsetY: 20,
                    fontFamily: 'Public Sans'
                  },
                  total: {
                    show: true,
                    fontSize: '0.8125rem',
                    color: axisColor,
                    label: 'Weekly',
                    formatter: function (w) {
                      return '38%';
                    }
                  }
                }
              }
            }
          }
        };
      if (typeof chartdownloadStatistics !== undefined && chartdownloadStatistics !== null) {
        const statisticsChart = new ApexCharts(chartdownloadStatistics, downloadChartConfig);
        statisticsChart.render();
      }
    })();

  </script>
</body>

</html>