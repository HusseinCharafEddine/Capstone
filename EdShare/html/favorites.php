<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("location:../index.php");
}



require ("../BE/common/commonFunctions.php");
require ("../BE/coursesController.php");
require ("../BE/userController.php");
require ("../BE/documentController.php");
require ("../BE/universityController.php");
require ("../BE/downloadController.php");
require ("../BE/favoriteController.php");

$userController = new UserController();
$courseController = new CoursesController();
$documentController = new DocumentController();
$universityController = new UniversityController();
$downloadController = new DownloadController();
$favoriteController = new FavoriteController();

$db = DBConnect();
$username = $_SESSION['username'];
$user = $userController->getUserByUsername($username);
$userId = $user['UserId'];
$getUploadedDocumentsQuery = "SELECT * FROM Document WHERE UserId = (SELECT UserId FROM User WHERE Username = ?)";
$stmt = $db->prepare($getUploadedDocumentsQuery);
$stmt->execute([$username]);
$uploadedDocuments = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <link rel="stylesheet" href="../assets/css/favs.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

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
          <li>
            <div id="token-container" style="display: flex; margin: 18px; margin-top:0px">
              <div id="uploads-token" style="margin-right: auto;">
                <span>
                  <img src="../assets/img/icons/tokens/uploadstoken.png">
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
                  <img src="../assets/img/icons/tokens/downloadstoken.png">
                  <?php
                  echo $contributionScore;
                  ?>
                </span>
              </div>
            </div>
          </li>
          <!-- Dashboards -->
          <li class="menu-item ">
            <a href="../landing.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Home">Home</div>
            </a>
          </li>
          <li class="menu-item active">
            <a href="favorite.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-history"></i>
              <div data-i18n="History">Favorites</div>
            </a>
          </li>
          <li class="menu-item ">
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
          <li class="menu-item ">
            <a href="analytics.html" class="menu-link">
              <i class="menu-icon tf-icons bx bx-chart"></i>
              <div data-i18n="Analytics">Analytics</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="league-standings.html" class="menu-link">
              <i class="menu-icon tf-icons bx bx-crown"></i>
              <div data-i18n="LeagueStandings">League Standings</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="studybuddy.html" class="menu-link">
              <i class="menu-icon tf-icons bx bx-brain"></i>
              <div data-i18n="StudyBuddy">StudyBuddy AI</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="contact-us.html" class="menu-link">
              <i class="menu-icon tf-icons bx bx-phone"></i>
              <div data-i18n="ContactUs">Contact Us</div>
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


            <!-- /Search -->





            <ul class="navbar-nav flex-row align-items-center ms-auto">





              <!-- Notification -->
              <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-1">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                  data-bs-auto-close="outside" aria-expanded="false">
                  <i class="bx bx-bell bx-sm"></i>
                  <span class="badge bg-danger rounded-pill badge-notifications">5</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end py-0">
                  <li class="dropdown-menu-header border-bottom">
                    <div class="dropdown-header d-flex align-items-center py-3">
                      <h5 class="text-body mb-0 me-auto">Notification</h5>
                      <a href="javascript:void(0)" class="dropdown-notifications-all text-body" data-bs-toggle="tooltip"
                        data-bs-placement="top" aria-label="Mark all as read"
                        data-bs-original-title="Mark all as read"><i class="bx fs-4 bx-envelope-open"></i></a>
                    </div>
                  </li>
                  <li class="dropdown-notifications-list scrollable-container ps">
                    <ul class="list-group list-group-flush">
                      <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <img src="../assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle">
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">Congratulation Lettie 🎉</h6>
                            <p class="mb-0">Won the monthly best seller gold badge</p>
                            <small class="text-muted">1h ago</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <span class="avatar-initial rounded-circle bg-label-danger">CF</span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">Charles Franklin</h6>
                            <p class="mb-0">Accepted your connection</p>
                            <small class="text-muted">12hr ago</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <img src="../assets/img/avatars/2.png" alt="" class="w-px-40 h-auto rounded-circle">
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">New Message ✉️</h6>
                            <p class="mb-0">You have new message from Natalie</p>
                            <small class="text-muted">1h ago</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <span class="avatar-initial rounded-circle bg-label-success"><i
                                  class="bx bx-cart"></i></span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">Whoo! You have new order 🛒 </h6>
                            <p class="mb-0">ACME Inc. made new order $1,154</p>
                            <small class="text-muted">1 day ago</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <img src="../assets/img/avatars/9.png" alt="" class="w-px-40 h-auto rounded-circle">
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">Application has been approved 🚀 </h6>
                            <p class="mb-0">Your ABC project application has been approved.</p>
                            <small class="text-muted">2 days ago</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <span class="avatar-initial rounded-circle bg-label-success"><i
                                  class="bx bx-pie-chart-alt"></i></span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">Monthly report is generated</h6>
                            <p class="mb-0">July monthly financial report is generated </p>
                            <small class="text-muted">3 days ago</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <img src="../assets/img/avatars/5.png" alt="" class="w-px-40 h-auto rounded-circle">
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">Send connection request</h6>
                            <p class="mb-0">Peter sent you connection request</p>
                            <small class="text-muted">4 days ago</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <img src="../assets/img/avatars/6.png" alt="" class="w-px-40 h-auto rounded-circle">
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">New message from Jane</h6>
                            <p class="mb-0">Your have new message from Jane</p>
                            <small class="text-muted">5 days ago</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                      <li class="list-group-item list-group-item-action dropdown-notifications-item marked-as-read">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                              <span class="avatar-initial rounded-circle bg-label-warning"><i
                                  class="bx bx-error"></i></span>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <h6 class="mb-1">CPU is running high</h6>
                            <p class="mb-0">CPU Utilization Percent is currently at 88.63%,</p>
                            <small class="text-muted">5 days ago</small>
                          </div>
                          <div class="flex-shrink-0 dropdown-notifications-actions">
                            <a href="javascript:void(0)" class="dropdown-notifications-read"><span
                                class="badge badge-dot"></span></a>
                            <a href="javascript:void(0)" class="dropdown-notifications-archive"><span
                                class="bx bx-x"></span></a>
                          </div>
                        </div>
                      </li>
                    </ul>
                    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                      <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                    </div>
                    <div class="ps__rail-y" style="top: 0px; right: 0px;">
                      <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div>
                    </div>
                  </li>
                  <li class="dropdown-menu-footer border-top p-3">
                    <button class="btn btn-primary text-uppercase w-100">view all notifications</button>
                  </li>
                </ul>
              </li>
              <!--/ Notification -->
              <!-- User -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                  <div class="avatar avatar-online">
                    <img src="../assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle">
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li>
                    <a class="dropdown-item" href="pages-account-settings-account.html">
                      <div class="d-flex">
                        <div class="flex-shrink-0 me-3">
                          <div class="avatar avatar-online">
                            <img src="../assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle">
                          </div>
                        </div>
                        <div class="flex-grow-1">
                          <span class="fw-medium d-block">John Doe</span>
                          <small class="text-muted">Admin</small>
                        </div>
                      </div>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="pages-profile-user.html">
                      <i class="bx bx-user me-2"></i>
                      <span class="align-middle">My Profile</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="pages-account-settings-account.html">
                      <i class="bx bx-cog me-2"></i>
                      <span class="align-middle">Settings</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="pages-account-settings-billing.html">
                      <span class="d-flex align-items-center align-middle">
                        <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                        <span class="flex-grow-1 align-middle">Billing</span>
                        <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                      </span>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="pages-faq.html">
                      <i class="bx bx-help-circle me-2"></i>
                      <span class="align-middle">FAQ</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="pages-pricing.html">
                      <i class="bx bx-dollar me-2"></i>
                      <span class="align-middle">Pricing</span>
                    </a>
                  </li>
                  <li>
                    <div class="dropdown-divider"></div>
                  </li>
                  <li>
                    <a class="dropdown-item" href="auth-login-cover.html" target="_blank">
                      <i class="bx bx-power-off me-2"></i>
                      <span class="align-middle">Log Out</span>
                    </a>
                  </li>
                </ul>
              </li>
              <!--/ User -->


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
            <div class="app-academy">
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

                  // Get the current page number
                  $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

                  // Calculate the offset
                  $offset = ($page - 1) * $uploadsPerPage;

                  // Fetch uploads for the current page from your data source
                  // Get filter values from the AJAX request
                  $universityId = isset($_GET['universityId']) ? intval($_GET['universityId']) : 0;
                  $courseId = isset($_GET['courseId']) ? intval($_GET['courseId']) : 0;
                  $rating = isset($_GET['rating']) ? intval($_GET['rating']) : 0;
                  $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';

                  // Build the filter string based on the selected filter values
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
                  $favoritesForPage = $favoriteController->fetchFavorites($userId, $offset, $uploadsPerPage, $filter, $searchTerm);
                  ?>

                  <h6 class="mb-5 mt-5">Favorites</h6>

                  <div class="row row-cols-1 row-cols-md-4 g-3 mb-3">
                    <?php foreach ($favoritesForPage as $favorite): ?>
                      <?php
                      $document = $documentController->getDocumentById($favorite['DocumentId']);
                      $author = $userController->getUser((int) $document['UserId'])['Username'];
                      $isFavorited = $favoriteController->getFavoriteByUserIdAndDocumentId($userId, $document['DocumentId']);
                      $imgSrc = ($isFavorited ? '../assets/img/icons/unicons/heartfilled.png' : '../assets/img/icons/unicons/heart.png'); ?>
                      <div class="col">
                        <div class="card h-100">
                          <img class="card-img-top" src="../thumbnails/<?php
                          echo $author ?>/<?php echo $document['ThumbnailPath']; ?>">
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
                                  if ($university) {
                                    echo $university['UniversityAcronym'];
                                  } else {
                                    echo "University Not Found";
                                  }
                                } else {
                                  echo "Course Not Found";
                                }
                                ?>
                              </span>
                              <span class="badge bg-label-primary">
                                <?php
                                if ($course) {
                                  echo $course['CourseCode'];
                                } else {
                                  echo "Course not found";
                                }
                                ?>
                              </span>
                              <?php echo round($downloadController->getAverageRatingByDocumentId($document["DocumentId"]), 1) ?>
                              <span class="text-warning"><i class="bx bxs-star me-1"></i></span><span class="text-muted">
                                <?php echo "(" . $downloadController->getTotalRatingByDocumentId($document["DocumentId"]) . ")" ?>
                              </span>
                            </div>
                            <div id="starRating_<?php echo $document['DocumentId']; ?>">
                              <?php
                              // Fetch the user's rating for the document
                              $canRate;
                              $testRating = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                              if ($testRating) {
                                $userRating = $testRating['Rating'];
                                $canRate = true;
                              } else {
                                $userRating = 0;
                                $canRate = false;
                              }


                              // Render stars based on user's rating
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
                                <?php
                                echo $author;
                                ?>
                              </span>

                            </div>
                            <div class="d-flex justify-content-center gap-3 mb-2 text-white ">
                              <!-- First Download Button -->
                              <div class="d-flex align-items-center bg-primary rounded p-1">
                                <?php
                                $test = $downloadController->getDownloadByUserAndDocument($userId, $document['DocumentId']);
                                $downloadAllowed = ($tokenScore > 2 || $test);
                                if ($downloadAllowed): ?>
                                  <button class="btn btn-primary download-btn"
                                    data-document-id="<?php echo $document['DocumentId']; ?>"
                                    onclick="initiateDownload(<?php echo $userId; ?>, <?php echo $document['DocumentId']; ?>, '../uploads/<?php echo $author ?>/<?php echo $document['FilePath']; ?>', '<?php echo $document['Title']; ?>')">
                                    Download </button>
                                <?php else: ?>
                                  <button class="btn btn-primary download-btn"> Insufficient Tokens! </button>
                                <?php endif; ?>
                              </div>


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


          <script>
            document.getElementById("applyFilterBtn").addEventListener("click", function () {
              var universityId = document.getElementById("University").value;
              var courseId = document.getElementById("Category").value;
              var rating = document.getElementById("Rating").value;

              // Redirect to the same page with filter parameters
              window.location.href = window.location.pathname + "?universityId=" + universityId + "&courseId=" + courseId + "&rating=" + rating;
            });
          </script>
          <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
          <!-- <script>
            $(document).ready(function () {
              $('.toggle-favorite').click(function () {
                var documentId = $(this).data('document-id');
                var button = $(this); // Reference to the clicked button

                // AJAX request to toggle favorite status
                $.ajax({
                  type: 'POST',
                  url: '../BE/toggleFavorite.php',
                  data: {
                    documentId: documentId
                  },
                  success: function (response) {
                    if (response === 'Favorite added successfully') {
                      button.text('Remove from Favorites');
                    } else if (response === 'Favorite removed successfully') {
                      button.text('Add to Favorites');
                    }

                  },
                  error: function () {
                  }
                });
              });
            });
          </script> -->
          <script>
            const userId = <?php echo isset($_SESSION['userId']) ? $_SESSION['userId'] : 'null'; ?>;
            function toggleStar(index, documentId) {
              const stars = document.querySelectorAll('#starRating_' + documentId + ' .bx-star');
              console.log(stars);
              const newRating = index + 1;

              // Update star styles (visual feedback)
              for (let i = 0; i < stars.length; i++) {
                const starGroupIndex = Math.floor(i / 5); // Calculate the index of the star group
                const starIndex = i % 5; // Calculate the index within the star group

                if (starIndex <= index) {
                  stars[i].classList.add('bxs-star');
                  stars[i].style.color = '#ffab00';
                } else {
                  stars[i].classList.remove('bxs-star');
                  stars[i].style.color = ''; // Reset color for empty stars
                }
              }

              // Send a POST request to update the rating
              fetch('../BE/updateRating.php', {
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
            // Function to handle filter submission
            function submitFilter() {
              var universityId = document.getElementById("University").value;
              var courseId = document.getElementById("Category").value;
              var rating = document.getElementById("Rating").value;
              var searchTerm = document.getElementById("searchInput").value;

              // Construct the URL with filter parameters and search term
              var url = window.location.pathname +
                "?universityId=" + universityId +
                "&courseId=" + courseId +
                "&rating=" + rating +
                "&searchTerm=" + encodeURIComponent(searchTerm); // Encode search term

              // Redirect to the constructed URL
              window.location.href = url;
            }
          </script>

          <!-- search -->

          <script>
            const UserId = <?php echo isset($_SESSION['userId']) ? $_SESSION['userId'] : 'null'; ?>;
            function fetchSearchSuggestions() {
              var searchTerm = document.getElementById("searchInput").value;
              if (searchTerm.trim() === '') {
                return; // No suggestions for empty search term
              }

              // AJAX call to fetch search suggestions based on the input
              $.ajax({
                url: '../BE/fetchSearchSuggestionsfavs.php',
                method: 'GET',
                data: { searchTerm: searchTerm, UserId: UserId},
                success: function (response) {
                  // Update the search suggestions dropdown with retrieved suggestions
                  var suggestionsDropdown = document.getElementById("searchSuggestions");
                  suggestionsDropdown.innerHTML = response;
                  suggestionsDropdown.style.display = 'block'; // Show the suggestions dropdown
                },
                error: function (xhr, status, error) {
                  console.error(error);
                }
              });
            }

            // Add event listeners to search icon, search input field, and search suggestions
            document.addEventListener("DOMContentLoaded", function () {
              // Event listener for clicking the search icon
              document.querySelector(".bx-search").addEventListener("click", function () {
                submitFilter(); // Trigger filter submission
              });

              // Event listener for Enter key press in the search input field
              document.getElementById("searchInput").addEventListener("keypress", function (event) {
                if (event.key === "Enter") {
                  submitFilter(); // Trigger filter submission
                }
              });

              // Event listener for input change in the search input field (for suggestions)
              document.getElementById("searchInput").addEventListener("input", function () {
                fetchSearchSuggestions(); // Fetch search suggestions as user types
              });
              document.addEventListener("click", function (event) {
                var clickedElement = event.target;
                if (clickedElement.classList.contains("search-suggestion")) {
                  // Set the search input value to the clicked suggestion
                  document.getElementById("searchInput").value = clickedElement.textContent.trim();
                  // Hide the suggestions container after selection
                  document.getElementById("searchSuggestions").style.display = "none";
                }
              });

              var applyFilterBtn = document.getElementById('applyFilterBtn');

              // Add a click event listener to the button
              applyFilterBtn.addEventListener('click', function () {
                // Call the submitFilter() function when the button is clicked
                submitFilter();
              });

            });
          </script>
          <script>
            function addToFavorites(documentId) {
              // AJAX request to toggle favorite status
              $.ajax({
                type: 'POST',
                url: '../BE/toggleFavorite.php',
                data: {
                  documentId: documentId
                },
                success: function (response) {
                  // Update all heart icons with matching documentId
                  var heartIcons = document.querySelectorAll('[id^="heart-fav' + documentId + '"]');

                  heartIcons.forEach(function (heartIcon) {
                    // Update src based on response
                    if (response === 'Favorite added successfully') {
                      heartIcon.src = '../assets/img/icons/unicons/heartfilled.png';
                    } else if (response === 'Favorite removed successfully') {
                      heartIcon.src = '../assets/img/icons/unicons/heart.png';
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
              // Send AJAX request to decrement token score and initiate download
              $.ajax({
                url: "../BE/download.php",
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
</body>

</html>