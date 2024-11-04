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
        if (mysqli_query($conn, $update_sql)) {
            // Refresh user data immediately after updating
            $user['name'] = $name;
            $user['email'] = $email;
            $success = "Profile updated successfully!";
        } else {
            $error = "Error updating profile.";
        }
    }
    // Handle password change
    if (isset($_POST['change_password'])) {
        $old_password = md5($_POST['old_password']);
        $new_password = md5($_POST['new_password']);
        if ($old_password === $user['password']) {
            $update_password_sql = "UPDATE user SET password='$new_password' WHERE id='$user_id'";
            mysqli_query($conn, $update_password_sql);
            $success = "Password changed successfully";
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
    }
    // Handle updating the grade
    if (isset($_POST['update_grade'])) {
        $grade_id = $_POST['grade_id'];
        $new_grade = $_POST['new_grade'];
    
        $update_grade_sql = "UPDATE grades SET grade='$new_grade' WHERE id='$grade_id'";
        if (mysqli_query($conn, $update_grade_sql)) {
            $success = "Grade updated successfully!";
            // Optionally, you can redirect to the same page to avoid form resubmission.
            // header("Location: " . $_SERVER['PHP_SELF']);
            // exit();
        } else {
            $error = "Error updating grade.";
        }
    }

    if (isset($_POST['remove_course'])) {
        $grade_id = $_POST['grade_id'];
        $remove_grade_sql = "DELETE FROM grades WHERE id='$grade_id'";
        mysqli_query($conn, $remove_grade_sql);
        $success = "Course removed successfully";
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


$total_credits = 0;           // Total credits for all courses
$total_credits_pass = 0;      // Total credits passed (only those meeting the passing grade)
$weighted_grade_sum = 0;      // Weighted sum of grades for CGPA calculation
$excluded_courses = ['BUM1153']; // Courses to exclude from CGPA calculation
$excluded_credits = 0;        // Credits from excluded courses
$earned_credits = 0;          // Total earned credits, including passing excluded courses


// Define minimum passing grades
$minimum_passing_grade = 1.00; // Default passing grade for all courses
$minimum_passing_grade_required = 2.00; // Minimum passing grade for required courses

// Define courses that require the minimum passing grade of 2.00
$pass_required_courses = [
    'BUM2413', 'BCI1023'
];
$required_course_credits = 0;

// Fetch grades and calculate totals
while ($grade = mysqli_fetch_assoc($grade_result)) {
    $credit = $grade['credit'];
    $grade_value = $grade['grade'];
    $course_code = $grade['course_code'];

    // Add to Total Credits Taken
    $total_credits += $credit;

    // Check if the course is excluded from CGPA calculation
    if (in_array($course_code, $excluded_courses)) {
        // Track excluded credits
        $excluded_credits += $credit; // Add to excluded credits
        
        // Include in earned credits if the grade meets the passing requirement
        if ($grade_value >= $minimum_passing_grade) {
            $earned_credits += $credit; // Count excluded credits that pass
        }
        continue; // Skip further calculations for excluded courses
    }


    // Check if the course is in the pass_required_courses
    if (in_array($course_code, $pass_required_courses)) {
        // Include in Total Credits Passed if the grade meets the higher minimum passing grade
        if ($grade_value >= $minimum_passing_grade_required) {
            $required_course_credits += $credit;
            $total_credits_pass += $credit; // Count towards passed credits
            $earned_credits += $credit; // Also count these towards earned credits
        }
    } else {
        // For all other courses, check against the default minimum passing grade
        if ($grade_value >= $minimum_passing_grade) {
            $total_credits_pass += $credit; // Count these credits as passed
            $earned_credits += $credit; // Also count these towards earned credits
        }
    }

    // Add to CGPA calculation for all non-excluded courses
    $weighted_grade_sum += ($credit * $grade_value);
}

// Calculate effective credits for CGPA excluding the credits of excluded courses
$effective_credits = $total_credits - $excluded_credits; // Total credits minus excluded credits
$cgpa = ($effective_credits > 0) ? round($weighted_grade_sum / $effective_credits, 2) : 0.00;

$performance_query = "INSERT INTO student_data (user_id, cgpa, attempted_credits, earned_credits, excluded_credits, required_course_credits)
                      VALUES ('$user_id', '$cgpa', '$total_credits', '$earned_credits', '$excluded_credits', '$required_course_credits')
                      ON DUPLICATE KEY UPDATE 
                          cgpa='$cgpa', 
                          attempted_credits='$total_credits', 
                          earned_credits='$earned_credits', 
                          excluded_credits='$excluded_credits',
                          required_course_credits='$required_course_credits'";
$conn->query($performance_query);

// Reset the query result to use it again in the table
mysqli_data_seek($grade_result, 0);
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
        <div class="col-md-6 mb-4 mb-md-0">
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
        <div class="col-md-6 mb-4 mb-md-0">
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
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
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
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal<?= $grade['id'] ?>">Edit</button>
                                    <form method="POST" action="" style="display:inline;">
                                        <input type="hidden" name="grade_id" value="<?= $grade['id'] ?>">
                                        <button type="submit" name="remove_course" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this course?');">Remove</button>
                                    </form>
                                </td>
                            </tr>
        
                            <!-- Edit Course Modal -->
                            <div class="modal fade" id="editModal<?= $grade['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Edit Grade for <?= htmlspecialchars($grade['course_code']) ?></h5>
                                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="">
                                                <input type="hidden" name="grade_id" value="<?= $grade['id'] ?>">
                                                <div class="form-group">
                                                    <label for="grade">Select New Grade:</label>
                                                    <select class="form-control" name="new_grade" required>
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
                                                <button type="submit" name="update_grade" class="btn btn-primary">Update Grade</button>
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </tbody>
                </table>
                <!-- Display Total Credits Taken, Total Credits Passed, and CGPA -->
                <!-- <div class="mt-3 row justify-content-center">
                    <div class="col-12 col-md-4 text-center">
                        <h5 class="font-weight-bold">Attempted Credits: <span class="badge badge-info"><?= $total_credits ?></span></h5>
                    </div>
                    <div class="col-12 col-md-4 text-center">
                        <h5 class="font-weight-bold">Earned Credits: <span class="badge badge-info"><?= $earned_credits ?></span></h5>
                    </div>
                    <div class="col-12 col-md-4 text-center">
                        <h5 class="font-weight-bold">CGPA: <span class="badge badge-success"><?= $cgpa ?></span></h5>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center mt-3">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-info h-100">
            <div class="card-body text-center">
                <h5 class="card-title"><i class="fas fa-book-open"></i> Attempted Credits</h5>
                <span class="badge badge-light p-2" style="font-size: 1.2rem; font-weight: bold;"><?= $total_credits ?></span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-warning h-100">
            <div class="card-body text-center">
                <h5 class="card-title"><i class="fas fa-check-circle"></i> Earned Credits</h5>
                <span class="badge badge-light p-2" style="font-size: 1.2rem; font-weight: bold;"><?= $earned_credits ?></span>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card text-white bg-success h-100">
            <div class="card-body text-center">
                <h5 class="card-title"><i class="fas fa-star"></i> CGPA</h5>
                <span class="badge badge-light p-2" style="font-size: 1.2rem; font-weight: bold;"><?= $cgpa ?></span>
            </div>
        </div>
    </div>
</div>
</body>
</html>