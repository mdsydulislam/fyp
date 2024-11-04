<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion" style="background-color: #d5f0ed;">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <!-- Profile Heading with Logo (Responsive Centered Design) -->
                <div class="sb-sidenav-menu-heading d-flex flex-column align-items-center" style="color: cadetblue; text-align: center;">
                    <img src="assets/img/logo.png" alt="ACPS Logo" width="100" class="img-fluid mb-2">
                </div>
                <!-- Profile Information Link -->
                <a class="nav-link" href="profile.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                    Profile Information
                </a>
                <!-- Course Scheduling Link -->
                <a class="nav-link" href="courseScheduling.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Course Scheduling
                </a>
                <!-- Performance Prediction Link -->
                <a class="nav-link" href="performancePrediction.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                    Performance Prediction
                </a>
            </div>
        </div>
        <!-- Footer Section with Flexbox for Responsiveness -->
        <div class="sb-sidenav-footer d-flex justify-content-between align-items-center" style="background-color: #d5f0ed; padding: 10px;">
            <!-- Left Side: Logged-in Status -->
            <div class="small text-left">
                <div>Logged in as:</div>
                <div>Student</div>
            </div>
            <!-- Right Side: Logout Button -->
            <a href="logout.php" class="btn btn-light" style="color: black;">Logout</a>
        </div>   
    </nav>
</div>
<!-- CSS for Responsive Adjustments -->
<style>
    /* Adjusts font and padding for smaller screens */
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