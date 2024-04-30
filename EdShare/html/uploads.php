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

$userController = new UserController();
$courseController = new CoursesController();
$documentController = new DocumentController();
$universityController = new UniversityController();
$downloadController = new DownloadController();

$db = DBConnect();
$username = $_SESSION['username'];
$user = $userController->getUserByUsername($username);


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
  <link rel="stylesheet" href="../assets/css/spinner.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/spinkit/spinkit.css" />

  <!-- Page CSS -->

  <!-- Helpers -->

  <script src="../assets/vendor/js/helpers.js"></script>
  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  <script src="../assets/js/config.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>‌​

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
          <li class="menu-item">
            <a href="../landing.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Home">Home</div>
            </a>
          </li>
          <li class="menu-item ">
            <a href="favorites.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-heart"></i>
              <div data-i18n="Favorites">Favorites</div>
            </a>
          </li>
          <li class="menu-item  ">
            <a href="downloads.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-download"></i>
              <div data-i18n="Downloads">Downloads</div>
            </a>
          </li>
          <li class="menu-item active">
            <a href="uploads.php" class="menu-link">
              <i class="menu-icon tf-icons bx bx-upload"></i>
              <div data-i18n="Uploads">Uploads</div>
            </a>
          </li>
          <li class="menu-item ">
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
 <!-- /Search -->
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
            <!-- /Search -->





            <ul class="navbar-nav flex-row align-items-center ms-auto">





              <!-- User -->
              <li class="nav-item navbar-dropdown dropdown-user dropdown">
    <div class="avatar avatar-online">
        <a class="dropdown-item" href="pages-account-settings-account.php">
            <img src="../assets/img/avatars/1.png" alt="" class="w-px-40 h-auto rounded-circle">
        </a>
    </div>
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
            <div class="row">
              <h6 class="mb-5">Upload a File</h6>
              <div class="row">
                <div class="col-md-12">
                  <div class="card mb-4">
                    <h5 class="card-header">Upload a File</h5>
                    <div class="card-body demo-vertical-spacing demo-only-element">
                      <form action="../BE/uploads.php" method="post" enctype="multipart/form-data" id="uploadForm">
                        <div class="input-group">
                          <input type="text" class="form-control" aria-label="University Name" id="course-name-input"
                            name="course-name" placeholder="Enter Course Name">
                          <label class="input-group-text">Course Name</label>
                        </div>
                        <div class="input-group">
                          <input type="text" class="form-control" aria-label="Text input with dropdown button"
                            id="course-code-input" name="course-code" placeholder="Enter Course Code">
                          <label class="input-group-text">Course Code</label>
                        </div>
                        <div class="input-group">
                          <input type="text" class="form-control" aria-label="Text input with dropdown button"
                            name="title" placeholder="Enter Document Title">
                          <label class="input-group-text">Document Title</label>
                        </div>
                        <div class="input-group">
                          <input type="text" class="form-control" aria-label="Text input with dropdown button"
                            id="category-input" name="category" placeholder="Enter Document Category">
                          <label class="input-group-text">Category</label>
                        </div>
                        <div class="input-group">
                          <input type="hidden" name="type" id="type-hidden">
                          <!-- Hidden input field to store the selected value -->
                          <select id="type" class="form-select form-select-md" placeholder="type"
                            onchange="updateHiddenInput()" aria-label="Document Type">
                            <option value="" disabled selected>Select Document Type</option>
                            <option value="Summary">Summary</option>
                            <option value="Notes">Notes</option>
                            <option value="Exercises">Exercises</option>
                            <option value="Practice Sheets">Practice Sheets</option>
                          </select>
                          <label class="input-group-text">Type</label>
                        </div>
                        <div class="input-group">
                          <input type="file" class="form-control" id="inputGroupFile02" name="file">
                          <label class="input-group-text">Upload</label>
                        </div>
                        <div class="container d-flex justify-content-center">
                          <input type="submit" class="btn btn-outline-primary" value="Upload" name="submit">
                        </div>
                      </form>
                      <div id="loadingContainer">
                        <div id="loadingSpinner" class="sk-fold sk-primary">
                          <div class="sk-fold-cube"></div>
                          <div class="sk-fold-cube"></div>
                          <div class="sk-fold-cube"></div>
                          <div class="sk-fold-cube"></div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php
            // Assuming $uploadedDocuments contains all your uploads
            $uploadsPerPage = 20;
            $user = $userController->getUserByUsername($username);
            $totalUploads = $user['UploadCount'];
            $totalPages = ceil($totalUploads / $uploadsPerPage);

            // Get the current page number
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;

            // Calculate the offset
            $offset = ($page - 1) * $uploadsPerPage;
            $searchTerm = isset($_GET['searchTerm']) ? $_GET['searchTerm'] : '';
            // Fetch uploads for the current page from your data source
            $uploadsForPage = $documentController->fetchDocumentsForPage($user['UserId'], $offset, $uploadsPerPage, $searchTerm);
            ?>

            <h6 class="mb-5 mt-5">My Uploads</h6>

            <div class="row row-cols-1 row-cols-md-4 g-3 mb-3">
              <?php foreach ($uploadsForPage as $document): ?>
                <div class="col">
                  <div class="card h-100">
                    <img class="card-img-top"
                      src="../thumbnails/<?php echo $username ?>/<?php echo $document['ThumbnailPath']; ?>">
                    <div class="card-body">
                      <hr>
                      <h5 class="card-title">

                      </h5>
                      <a href="app-academy-course-details.html" class="h5">
                        <?php echo $document['Title']; ?>
                      </a>
                      <br>
                      <div class="d-flex justify-content-between align-items-center mb-3">

                        <span class="badge bg-label-primary">
                          <?php
                          $course = $courseController->getCourse($document['CourseId']);
                          if ($course) {
                            $university = $universityController->getUniversityById($course['UniversityId']);
                            if ($university) {
                              echo $university['UniversityAcronym']; // Assuming 'CourseName' is a field in your courses table
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
                            echo $course['CourseCode']; // Assuming 'CourseName' is a field in your courses table
                          } else {
                            echo "Course not found"; // Or handle the case where the course is not found
                          }
                          ?>
                        </span>
                        <h6 class="d-flex align-items-center justify-content-center gap-1 mb-0">
                        </h6>
                        <?php echo round($downloadController->getAverageRatingByDocumentId($document["DocumentId"]), 1) ?>
                        <span class="text-warning"><i class="bx bxs-star me-1"></i></span><span class="text-muted">
                          <?php echo "(" . $downloadController->getTotalRatingByDocumentId($document["DocumentId"]) . ")" ?>
                        </span>

                        </h6>

                        <br>

                      </div>
                      <span class="text">Author:
                        <?php
                        echo $username; // Assuming 'Username' is a field in your users table
                      
                        ?>
                      </span>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <!-- Pagination -->
            <ul class="pagination justify-content-center mt-5">
              <?php if ($page > 1): ?>
                <li class="page-item prev">
                  <a class="page-link" href="?page=<?php echo $page - 1; ?>"><i
                      class="tf-icon bx bx-chevrons-left"></i></a>
                </li>
              <?php endif; ?>

              <?php
              // Calculate the starting page number for display
              $startPage = max(1, $page - 2);
              // Calculate the ending page number for display
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



            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">

                </div>

                <div class="d-none d-lg-inline-block">


                  <a href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/documentation/"
                    target="_blank" class="footer-link me-4">Documentation</a>

                  <a href="https://github.com/themeselection/sneat-html-admin-template-free/issues" target="_blank"
                    class="footer-link">Support</a>
                </div>
              </div>
            </footer>
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

    <!-- <script src="../assets/vendor/libs/jquery/jquery.js"></script> -->
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
      // JavaScript code for autocomplete
      $(document).ready(function () {
        $('#course-code-input').autocomplete({
          source: '../BE/autoCompleteCourseCode.php',
          minLength: 2
        });
      });

    </script>
    <script>
      // JavaScript code for autocomplete
      $(document).ready(function () {
        $('#course-name-input').autocomplete({
          source: '../BE/autoCompleteCourseName.php',
          minLength: 2
        });
      });

    </script>
    <script>
      // Function to show the loading spinner
      function showLoadingSpinner() {
        console.log("hit 1");
        document.getElementById('loadingContainer').style.display = 'block';
      }

      // Function to hide the loading spinner
      function hideLoadingSpinner() {
        console.log("hit 2");
        document.getElementById('loadingContainer').style.display = 'none';
      }

      // Event listener for form submission
      document.getElementById('uploadForm').addEventListener('submit', function () {
        showLoadingSpinner(); // Show loading spinner when form is submitted
      });

      // Simulate upload completion after 3 seconds (you can replace this with actual upload logic)
      setTimeout(function () {
        hideLoadingSpinner(); // Hide loading spinner after 3 seconds (simulating upload completion)
      }, 3000);
    </script>
    <script>
      // JavaScript code for autocomplete
      $(document).ready(function () {
        $('#category-input').autocomplete({
          source: '../BE/autoCompleteCategory.php',
          minLength: 2
        });
      });

    </script>
    <script>
      function updateHiddenInput() {
        var select = document.getElementById('type');
        var hiddenInput = document.getElementById('type-hidden');
        hiddenInput.value = select.value;
      }
    </script>

    <script>

function submitFilter() {

                var searchTerm = document.getElementById("searchInput").value;

                // Construct the URL with filter parameters and search term
                var url = window.location.pathname +
                  "?searchTerm=" + encodeURIComponent(searchTerm); // Encode search term

                // Redirect to the constructed URL
                window.location.href = url;
              }


    //search suggestions
    const UserId = <?php echo isset($_SESSION['userId']) ? $_SESSION['userId'] : 'null'; ?>;
    function fetchSearchSuggestions() {
                var searchTerm = document.getElementById("searchInput").value;
                if (searchTerm.trim() === '') {
                  return; // No suggestions for empty search term
                }

                // AJAX call to fetch search suggestions based on the input
                $.ajax({
                  url: '../BE/fetchSearchSuggestionsUploads.php',
                  method: 'GET',
                  data: { searchTerm: searchTerm,
                  UserId: UserId},
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
</body>

</html>