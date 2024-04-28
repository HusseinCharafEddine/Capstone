<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION["username"])) {
  header("location:../index.php");
}

require ("../BE/common/commonFunctions.php");
require ("../BE/userController.php");
$username = $_SESSION['username'];
$userController = new UserController();
$userId = $userController->getUserByUsername($username)['UserId'];
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

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="../assets/css/badgeslider.css" />

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
            <a href="../landing.html" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Home">Home</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="history.html" class="menu-link">
              <i class="menu-icon tf-icons bx bx-history"></i>
              <div data-i18n="History">History</div>
            </a>
          </li>
          <li class="menu-item  ">
            <a href="downloads.html" class="menu-link">
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
          <li class="menu-item active">
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
              <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="Search..."
                  aria-label="Search...">
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
                              <img src="../../assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle">
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
                              <img src="../../assets/img/avatars/2.png" alt="" class="w-px-40 h-auto rounded-circle">
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
                              <img src="../../assets/img/avatars/9.png" alt="" class="w-px-40 h-auto rounded-circle">
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
                              <img src="../../assets/img/avatars/5.png" alt="" class="w-px-40 h-auto rounded-circle">
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
                              <img src="../../assets/img/avatars/6.png" alt="" class="w-px-40 h-auto rounded-circle">
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
                style="position: absolute; visibility: hidden; white-space: pre; font-family: Public Sans, -apple-system, BlinkMacSystemFont, ;">sf</pre>
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
              <h6 class="mb-5">League Standings</h6>
              <div class="badge-container">
                <div class="slider-wrapper">
                  <button id="prev-slide" class="slide-button material-symbols-rounded">

                  </button>
                  <ul class="image-list">
                    <img class="image-item" src="../assets/badges/Iron_1_Rank.png">
                    <img class="image-item" src="../assets/badges/Bronze_2_Rank.png">
                    <img class="image-item" src="../assets/badges/Silver_3_Rank.png">
                    <img class="image-item" src="../assets/badges/Gold_4_Rank.png">
                    <img class="image-item" src="../assets/badges/Platinum_5_Rank.png">
                    <img class="image-item" src="../assets/badges/Diamond_6_Rank.png">
                    <img class="image-item" src="../assets/badges/Ascendant_7_Rank.png">
                    <img class="image-item" src="../assets/badges/Immortal_8_Rank.png">
                    <button id="next-slide" class="slide-button material-symbols-rounded">

                    </button>
                </div>
                <div class="slider-scrollbar">
                  <div class="scrollbar-track">
                    <div class="scrollbar-thumb"></div>
                  </div>
                </div>
              </div>
              <?php
              // Fetch users based on selected standings and rows per page
              $selectedStandings = isset($_GET['standings']) ? $_GET['standings'] : 'worldwide';
              $rowsPerPage = isset($_GET['rowsPerPage']) ? $_GET['rowsPerPage'] : 10; // Default rows per page
              $users = $userController->fetchUsersForStandings($selectedStandings, $userId, $rowsPerPage);
              $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

              $allUsers = $userController->getAllUsers();
              if ($selectedStandings != 'worldwide') {
                $count = $userController->getCountOfUsersInLeague($userId);
              } else {
                $count = $userController->getCountOfAllUsers();
              }
              ?>

              <!-- Your HTML structure with PHP integration -->
              <div class="card">
                <div class="card-datatable table-responsive">
                  <div class="row mx-1">
                    <div
                      class="col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start gap-3 mb-3 mt-3">
                      <div class="UsersPerTable">
                        <label> <select id="rowsPerPageSelect" class="form-select">
                            <option value="2" <?php echo ($rowsPerPage == 2) ? 'selected' : ''; ?>>2</option>
                            <option value="10" <?php echo ($rowsPerPage == 10) ? 'selected' : ''; ?>>10</option>
                            <option value="50" <?php echo ($rowsPerPage == 50) ? 'selected' : ''; ?>>50</option>
                            <option value="100" <?php echo ($rowsPerPage == 100) ? 'selected' : ''; ?>>100</option>
                          </select>
                      </div>
                      <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start mt-md-0 mt-3">
                        <div class="dt-buttons btn-group flex-wrap"></span>
                          <!-- Additional buttons or actions can be placed here -->
                        </div>
                      </div>
                    </div>

                    <div
                      class="col-12 col-md-6 d-flex align-items-center justify-content-end flex-column flex-md-row pe-3 gap-md-3">
                      <div>
                        <select id="standingsOptionSelect" class="form-select">
                          <option value="worldwide" <?php echo ($selectedStandings == 'worldwide') ? 'selected' : ''; ?>>
                            Worldwide Standings
                          </option>
                          <option value="league" <?php echo ($selectedStandings == 'league') ? 'selected' : ''; ?>>
                            Your League Standings
                          </option>
                        </select>
                      </div>
                    </div>

                  </div>
                  <table class="invoice-list-table table border-top dataTable no-footer dtr-column" style="width:100%;">
                    <thead>
                      <tr>
                        <th style="width: 25%;">Rank</th>
                        <th style="width: 25%;">Username</th>
                        <th style="width: 25%;">Title</th>
                        <th style="width: 25%;">Contribution Score</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($users as $index => $user): ?>
                        <tr>
                          <td><?php echo $index + $rowsPerPage * ($currentPage - 1) + 1; ?></td>
                          <td><?php echo $user['Username']; ?></td>
                          <td>
                            <?php
                            // Determine badge/title based on user's rating
                            $badge = 'Iron'; // Default badge (lowest)
                            $badgeImg = 1;
                            if ($user['ContributionScore'] >= 2101) {
                              $badge = 'Immortal';
                              $badgeImg = 8;
                            } elseif ($user['ContributionScore'] >= 1700) {
                              $badge = 'Ascendant';
                              $badgeImg = 7;
                            } elseif ($user['ContributionScore'] >= 1300) {
                              $badge = 'Diamond';
                              $badgeImg = 6;
                            } elseif ($user['ContributionScore'] >= 1000) {
                              $badge = 'Platinum';
                              $badgeImg = 5;
                            } elseif ($user['ContributionScore'] >= 700) {
                              $badge = 'Gold';
                              $badgeImg = 4;
                            } elseif ($user['ContributionScore'] >= 400) {
                              $badge = 'Silver';
                              $badgeImg = 3;
                            } elseif ($user['ContributionScore'] >= 200) {
                              $badge = 'Bronze';
                              $badgeImg = 2;
                            }
                            ?>

                            <div class="d-flex justify-content-start align-items-center">
                              <div class="avatar-wrapper">
                                <div class="avatar avatar-sm me-2">
                                  <?php
                                  echo '<img src="../assets/badges/' . $badge . "_" . $badgeImg . '_Rank.png" alt="' . $badge . '">';
                                  ?>
                                </div>
                              </div>
                              <div class="d-flex flex-column">
                                <span class="fw-medium"><?php echo ucfirst($badge); ?></span>
                              </div>
                            </div>
                          </td>
                          <td><?php echo $user['ContributionScore']; ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>


                  <!-- Pagination -->
                  <div class="row mx-2 mt-3">
                    <div class="col-sm-12 col-md-5">
                      <div>
                        <?php
                        // Calculate the range of displayed entries
                        $startIndex = ($currentPage - 1) * $rowsPerPage + 1;
                        $endIndex = min($startIndex + $rowsPerPage - 1, $count);
                        $totalEntries = $count;

                        echo "Showing $startIndex to $endIndex of $totalEntries entries";
                        ?>
                      </div>
                    </div>

                    <div class="col-sm-12 col-md-6">
                      <ul class="pagination">
                        <?php

                        // Determine total number of pages based on total users and rows per page
                        $totalPages = ceil($count / $rowsPerPage);
                        // Ensure $currentPage is defined and set to 1 if not provided in the URL
                        $currentUrl = $_SERVER['REQUEST_URI'];

                        for ($page = 1; $page <= $totalPages; $page++):
                          $pageUrl = buildUrlWithParams($currentUrl, ['page' => $page]);
                          $isActive = ($page == $currentPage) ? 'active' : '';

                          // Output pagination link
                          echo '<li class="paginate_button page-item ' . $isActive . '">';
                          echo '<a href="' . $pageUrl . '" class="page-link">' . $page . '</a>';
                          echo '</li>'; ?>


                        <?php endfor; ?>
                      </ul>
                    </div>
                  </div>
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
      <script src="../assets/js/badgeslider.js" defer></script>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const rowsPerPageSelect = document.getElementById('rowsPerPageSelect');
          const standingsOptionSelect = document.getElementById('standingsOptionSelect');

          // Event listener for rows per page selection
          rowsPerPageSelect.addEventListener('change', function () {
            const rowsPerPage = rowsPerPageSelect.value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('rowsPerPage', rowsPerPage);
            window.location.href = currentUrl.toString();
          });

          // Event listener for standings option selection
          standingsOptionSelect.addEventListener('change', function () {
            const standings = standingsOptionSelect.value;
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('standings', standings);
            window.location.href = currentUrl.toString();
          });
        });
      </script>


</body>

</html>