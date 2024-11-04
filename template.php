<?php if ($_SESSION['role'] == 'student') {?>
      	<!-- For Student -->      
<?php include_once("includes/head.php") ?>
    <body class="sb-nav-fixed">
    <?php include_once("includes/topnav.php") ?>
        <div id="layoutSidenav">
        <?php include_once("includes/sidenavStudent.php")?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <?php
                            if(isset($view)){
                                if($view=="profile"){
                                    include("view/profileView.php");
                                }
                                elseif($view=="courseScheduling"){
                                    include("view/courseSchedulingView.php");
                                }
                                elseif($view=="performancePrediction"){
                                    include("view/performancePredictionView.php");
                                }
                            }
                        ?>
                    </div>
                </main>
                <?php include_once("includes/footer.php") ?>
            </div>
        </div>
        <?php include_once("includes/script.php") ?>
    </body>
</html>
<?php }
else { ?>
    <!-- for Staff -->
    <?php include_once("includes/head.php") ?>
    <body class="sb-nav-fixed">
    <?php include_once("includes/topnav.php") ?>
        <div id="layoutSidenav">
        <?php include_once("includes/sidenavStaff.php") ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid">
                        <?php
                            if(isset($view)){
                                if($view=="courseCatalogue"){
                                    include("view/courseCatalogueView.php");
                                }
                                elseif($view=="manageCourses"){
                                    include("view/manageCoursesView.php");
                                }
                                // elseif($view=="teacherDetailsModel"){
                                //     include("view/teacherDetailsView.php");
                                // }
                                // elseif($view=="subjectDetailsModel"){
                                //     include("view/subjectDetailsView.php");
                                // }
                                // elseif($view=="activitiesModel"){
                                //     include("view/activitiesView.php");
                                // }
                            }
                        ?>
                    </div>
                </main>
                <?php include_once("includes/footer.php") ?>
            </div>
        </div>
        <?php include_once("includes/script.php") ?>
    </body>
</html>
<?php } 
?>