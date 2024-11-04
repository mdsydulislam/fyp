<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion" style="background-color: #d5f0ed;">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <!-- Logo Header Section -->
                <div class="sb-sidenav-menu-heading d-flex flex-column align-items-center" style="color: cadetblue; text-align: center;">
                    <img src="assets/img/logo.png" alt="ACPS Logo" width="100" class="img-fluid mb-2">
                </div>

                <!-- Course Catalogue for Staff -->
                <a class="nav-link" href="courseCatalogue.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                    Course Catalogue
                </a>

                <!-- Manage Courses Link -->
                <a class="nav-link" href="manageCourses.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-tasks"></i></div>
                    Manage Courses
                </a>

                <!-- Reports Link -->
                <a class="nav-link" href="reports.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-bar"></i></div>
                    Reports
                </a>
            </div>
        </div>

        <!-- Footer with Responsive Adjustments -->
        <div class="sb-sidenav-footer d-flex justify-content-between align-items-center" style="background-color: #d5f0ed; padding: 10px;">
            <!-- Left Side: Logged-in Status -->
            <div class="small text-left">
                <div>Logged in as:</div>
                <div>Staff</div>
            </div>

            <!-- Right Side: Logout Button -->
            <a href="logout.php" class="btn btn-light" style="color: black;">Logout</a>
        </div>
    </nav>
</div>

<!-- CSS for Responsive Adjustments -->
<style>
    /* Responsive adjustments for small screens */
    @media (max-width: 768px) {
        .sb-sidenav-footer div, .sb-sidenav-menu-heading {
            font-size: 0.85rem;
        }
        .sb-sidenav-footer {
            flex-direction: column;
            text-align: center;
        }
        .sb-sidenav-footer .btn {
            width: 100%;
            margin-top: 5px;
        }
    }
</style>