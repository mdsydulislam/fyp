<nav class="sb-topnav navbar navbar-expand navbar-light" style="background-color: #83d1c3;">
    <a class="navbar-brand" href="#">
        <!-- <img src="assets/img/logo.png" alt="ACPS Logo" width="40" height="40" class="d-inline-block align-top">
        Academic Course Planner System -->
        <h5>Academic Course Planner System</h5>
    </a>

    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>
    
    <ul class="navbar-nav ml-auto ml-md-6">
        <li class="nav-item">
            <a href="logout.php" class="btn btn-light" style="color: black;">Logout</a>
        </li>
    </ul>
</nav>



<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion" style="background-color: #d5f0ed;">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <!-- Updated for Profile Information and a clean design -->
                <div class="sb-sidenav-menu-heading" style="color: cadetblue; text-align: center;">
                    <img src="assets/img/logo.png" alt="ACPS Logo" width="100" style="margin-bottom: 10px;">
                </div>

                <!-- Profile Information -->
                <a class="nav-link" href="profile.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                    Profile Information
                </a>

                <!-- Course Scheduling -->
                <a class="nav-link" href="courseScheduling.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                    Course Scheduling
                </a>

                <!-- Performance Prediction -->
                <a class="nav-link" href="performancePrediction.php">
                    <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                    Performance Prediction
                </a>
            </div>
        </div>
    </nav>
</div>


<nav class="sb-topnav navbar navbar-expand navbar-light" style="background-color: #83d1c3;">
    <a class="text-responsive" href="#" style="margin-right: 15px;">
        <h5>Academic Course Planner System</h5>
    </a>
    <button style="background-color: #f5f5f5;" class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
        <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" />
            <div class="input-group-append">
                <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
            </div>
        </div>
    </form>

    <!-- Navbar User Section -->
    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="#">Settings</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">Logout</a>
            </div>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto ml-md-6">
        <li class="nav-item">
            <a href="logout.php" class="btn btn-light" style="color: black;">Logout</a>
        </li>
    </ul>
</nav>

<!-- Necessary Scripts for Bootstrap and Sidebar Toggle -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="js/scripts.js"></script>



<?php 
session_start();
if (isset($_SESSION['email']) && isset($_SESSION['id'])) {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>ACPS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <style>
        body {
            background-color: #b3e7dc; /* Match background color */
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }
        .form-container img {
            display: block;
            margin: 0 auto;
        }
        .form-container h4, .form-container h2 {
            text-align: center;
        }
        .form-container .btn-primary {
            width: 100%;
        }
        .form-container a {
            text-decoration: none;
            color: #007bff;
        }
        .form-container a:hover {
            text-decoration: underline;
        }
        .form-container .link-container {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh">
        <form class="form-container" action="php/check-login.php" method="post" style="width: 450px;">
            <!-- Logo Image -->
            <img src="assets/img/logo.png" alt="ACPS Logo" width="100" height="100">
            <!-- Title -->
            <h4>Academic Course Planner System</h4>
            <h2>Login</h2>
            <!-- Error Message -->
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_GET['error'] ?>
                </div>
            <?php } ?>
            <!-- Email Input -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter Your Email" required>
            </div>
            <!-- Password Input -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter Your Password" required>
            </div>
            <!-- User Type Dropdown -->
            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select class="form-select" name="role" required>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <!-- Login Button -->
            <button type="submit" class="btn btn-primary">Login</button>
            <!-- Links for Register and Forgot Password -->
            <div class="link-container mt-3">
                <a href="register.php">Register</a>
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </form>
    </div>
</body>
</html>


<?php if (isset($_SESSION['message'])): ?>
        <div class="alert <?= ($_SESSION['message_type'] == 'error') ? 'alert-danger' : 'alert-success' ?>">
            <?= $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); // Clear the message ?>
            <?php unset($_SESSION['message_type']); // Clear the message type ?>
        </div>
    <?php endif; ?>



    <div class="row mt-5">
        <div class="col-md-12">
            <h2>Your Courses</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Credit</th>
                        <th>Course Name</th>
                        <th>Grade</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($grade = mysqli_fetch_assoc($grade_result)) { ?>
                        <tr>
                            <td><?= $grade['course_code'] ?></td>
                            <td><?= $grade['credit'] ?></td>
                            <td><?= $grade['course_name'] ?></td>
                            <td><?= $grade['grade'] ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="grade_id" value="<?= $grade['id'] ?>">
                                    <button type="submit" name="remove_course" class="btn btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>


//prfilr
<?php
// session_start();
include 'db_conn.php';
// Ensure only students can access this page
if ($_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}
$error = ""; // To store error messages
$success = ""; // To store success messages
// Fetch user information
$user_id = $_SESSION['id'];
$sql = "SELECT * FROM user WHERE id='$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
// Handle update requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile update
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $update_sql = "UPDATE user SET name='$name', email='$email' WHERE id='$user_id'";
        mysqli_query($conn, $update_sql);
        // header("Location: profile.php");
        // exit();
    }
    // Handle password change
    if (isset($_POST['change_password'])) {
        $old_password = md5($_POST['old_password']);
        $new_password = md5($_POST['new_password']);
        if ($old_password === $user['password']) {
            $update_password_sql = "UPDATE user SET password='$new_password' WHERE id='$user_id'";
            mysqli_query($conn, $update_password_sql);
            $success = "Password changed successfully";
            // header("Location: profile.php");
            // exit();
        } else {
            $error = "Old password does not match.";
        }
    }
    // Handle adding a course and grade
    if (isset($_POST['add_course'])) {
        $course_id = $_POST['course_id'];
        $grade = $_POST['grade'];
        // Check if the course already exists for this user
        $check_course_sql = "SELECT * FROM grades WHERE user_id='$user_id' AND course_id='$course_id'";
        $check_course_result = mysqli_query($conn, $check_course_sql);
        if (mysqli_num_rows($check_course_result) > 0) {
            // Course already in the list
            $error = "This course is already in your list.";
        } else {
            // Add the course and grade
            $add_grade_sql = "INSERT INTO grades (user_id, course_id, grade) VALUES ('$user_id', '$course_id', '$grade')";
            mysqli_query($conn, $add_grade_sql);
            $success = "Course added successfully!";
        }
        // header("Location: profile.php");
        // exit();
    }
    if (isset($_POST['remove_course'])) {
        $grade_id = $_POST['grade_id'];
        $remove_grade_sql = "DELETE FROM grades WHERE id='$grade_id'";
        mysqli_query($conn, $remove_grade_sql);
        $success = "Course removed successfully";
        // header("Location: profile.php");
        // exit();
    }
}
// Fetch available courses
$course_query = "SELECT * FROM courses";
$course_result = mysqli_query($conn, $course_query);
// Fetch user's courses and grades
$grade_query = "SELECT grades.*, courses.course_code, courses.course_name, courses.credit 
                FROM grades 
                JOIN courses ON grades.course_id = courses.id 
                WHERE grades.user_id='$user_id'";
$grade_result = mysqli_query($conn, $grade_query);
// Display success message if set in the URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
// Clear success message if there's an error
if (!empty($error)) {
    $success = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="container mt-5">
    <!-- Display message -->
    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php } ?>
    <?php if (!empty($success)) { ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php } ?>
    <div class="row">
        <!-- Left side: Update Profile -->
        <div class="col-md-6">
            <!-- <h2 class="mt-5">Update Profile</h2> -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" name="name" value="<?= $user['name'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
        <!-- Right side: Change Password -->
        <div class="col-md-6">
            <!-- <h2 class="mt-5">Change Password</h2> -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="old_password">Old Password:</label>
                    <input type="password" class="form-control" name="old_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password:</label>
                    <input type="password" class="form-control" name="new_password" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
    <!-- Below the row: Add Course and Grade -->
    <div class="row mt-5">
        <div class="col-md-12">
            <h2>Add Course and Grade</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="course_id">Select Course:</label>
                    <select class="form-control" name="course_id" required>
                        <?php while ($course = mysqli_fetch_assoc($course_result)) { ?>
                            <option value="<?= $course['id'] ?>"><?= $course['course_code'] ?> - <?= $course['course_name'] ?> (<?= $course['credit'] ?> Credits)</option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="grade">Add Grade:</label>
                    <select class="form-control" name="grade" required>
                        <option value="0.00">0.00 - F</option>
                        <option value="0.67">0.67 - E</option>
                        <option value="1.00">1.00 - D</option>
                        <option value="1.33">1.33 - D+</option>
                        <option value="1.67">1.67 - C-</option>
                        <option value="2.00">2.00 - C</option>
                        <option value="2.33">2.33 - C+</option>
                        <option value="2.67">2.67 - B-</option>
                        <option value="3.00">3.00 - B</option>
                        <option value="3.33">3.33 - B+</option>
                        <option value="3.67">3.67 - A-</option>
                        <option value="4.00">4.00 - A</option>
                    </select>
                </div>
                <button type="submit" name="add_course" class="btn btn-primary">Add</button>
            </form>
        </div>
    </div>
    <!-- Below the row: Your Courses -->
    <br>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table mr-1"></i>
            Your Courses
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Credit</th>
                        <th>Course Name</th>
                        <th>Grade</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Age</th>
                            <th>Start date</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php while ($grade = mysqli_fetch_assoc($grade_result)) { ?>
                        <tr>
                            <td><?= $grade['course_code'] ?></td>
                            <td><?= $grade['credit'] ?></td>
                            <td><?= $grade['course_name'] ?></td>
                            <td><?= $grade['grade'] ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="grade_id" value="<?= $grade['id'] ?>">
                                    <button type="submit" name="remove_course" class="btn btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-12">
            <h2>Your Courses</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Credit</th>
                        <th>Course Name</th>
                        <th>Grade</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($grade = mysqli_fetch_assoc($grade_result)) { ?>
                        <tr>
                            <td><?= $grade['course_code'] ?></td>
                            <td><?= $grade['credit'] ?></td>
                            <td><?= $grade['course_name'] ?></td>
                            <td><?= $grade['grade'] ?></td>
                            <td>
                                <form method="POST" action="">
                                    <input type="hidden" name="grade_id" value="<?= $grade['id'] ?>">
                                    <button type="submit" name="remove_course" class="btn btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>