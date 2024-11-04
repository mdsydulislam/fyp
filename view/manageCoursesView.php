<?php
// session_start(); // Ensure session is started
include 'db_conn.php'; // Include database connection
// Ensure only staff can access this page
if ($_SESSION['role'] !== 'staff') {
    header("Location: index.php");
    exit();
}
$error = ""; // To store error messages
$success = ""; // To store success messages
// Add new course
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $credit = mysqli_real_escape_string($conn, $_POST['credit']);
    // Check if the course code or course name already exists
    $check_query = "SELECT * FROM courses WHERE (course_code = '$course_code' OR course_name = '$course_name') AND staff_id = '{$_SESSION['id']}'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Course code or course name already exists!";
    } else {
        // Insert new course if not existing
        $sql = "INSERT INTO courses (course_code, course_name, credit, staff_id) 
                VALUES ('$course_code', '$course_name', '$credit', '{$_SESSION['id']}')";
        if (mysqli_query($conn, $sql)) {
            $success = "Course added successfully!";
        } else {
            $error = "Error adding course: " . mysqli_error($conn);
        }
    }
}
// Remove course
// if (isset($_GET['remove'])) {
//     $course_id = $_GET['remove'];
//     // Remove the course from the database
//     $sql = "DELETE FROM courses WHERE id='$course_id' AND staff_id='{$_SESSION['id']}'";
//     mysqli_query($conn, $sql);
//     // header("Location: manageCourses.php?success=Course removed successfully!");
//     // exit();
//     $success = "Course removed successfully!";
// }
// Remove course
if (isset($_GET['remove'])) {
    $course_id = $_GET['remove'];
    // Remove the course from the database
    $sql = "DELETE FROM courses WHERE id='$course_id' AND staff_id='{$_SESSION['id']}'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Course removed successfully!";
    } else {
        $_SESSION['error'] = "Error removing course: " . mysqli_error($conn);
    }
    // Reload the page without header redirect
    echo "<script>window.location.href = 'manageCourses.php';</script>";
    exit();
}
// Edit course
if (isset($_POST['action']) && $_POST['action'] === 'edit') {
    $course_id = $_POST['course_id'];
    $course_code = mysqli_real_escape_string($conn, $_POST['course_code']);
    $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
    $credit = mysqli_real_escape_string($conn, $_POST['credit']);
    // Check if the updated course code or name already exists for another course
    $check_query = "SELECT * FROM courses WHERE (course_code = '$course_code' OR course_name = '$course_name') 
                    AND id != '$course_id' AND staff_id = '{$_SESSION['id']}'";
    $check_result = mysqli_query($conn, $check_query);
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Course code or course name already exists!";
    } else {
        // Update the course in the database
        $sql = "UPDATE courses SET course_code='$course_code', course_name='$course_name', credit='$credit' 
                WHERE id='$course_id' AND staff_id='{$_SESSION['id']}'";
        mysqli_query($conn, $sql);
        $success = "Course updated successfully!";
    }
}
// Fetch the courses for the logged-in staff member
$courses_query = "SELECT * FROM courses WHERE staff_id='{$_SESSION['id']}'";
$courses_result = mysqli_query($conn, $courses_query);
// Display success message if set in the URL
// Display success message if set in the session
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']); // Clear the message after displaying it
}

// Display error message if set in the session
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']); // Clear the message after displaying it
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Add New Course</h2>
        <?php if (!empty($error)) { ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php } ?>
        <?php if (!empty($success)) { ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php } ?>
        <form method="POST" action="">
            <input type="hidden" name="action" value="add">
            <div class="form-group">
                <label for="course_code">Course Code:</label>
                <input type="text" class="form-control" name="course_code" required>
            </div>
            <div class="form-group">
                <label for="course_name">Course Name:</label>
                <input type="text" class="form-control" name="course_name" required>
            </div>
            <div class="form-group">
                <label for="credit">Credits:</label>
                <input type="number" class="form-control" name="credit" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Course</button>
        </form>
        <br>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
                Courses List
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Credit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($course = mysqli_fetch_assoc($courses_result)) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($course['course_code']) ?></td>
                                    <td><?= htmlspecialchars($course['course_name']) ?></td>
                                    <td><?= htmlspecialchars($course['credit']) ?></td>
                                    <td>
                                    <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editModal<?= $course['id'] ?>">Edit</button>
                                    <a href="?remove=<?= $course['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this course?');">Remove</a>
                                    </td>
                                </tr>
                                <!-- Edit Course Modal -->
                                <div class="modal fade" id="editModal<?= $course['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel">Edit Course</h5>
                                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="">
                                                    <input type="hidden" name="action" value="edit">
                                                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                                                    <div class="form-group">
                                                        <label for="course_code">Course Code:</label>
                                                        <input type="text" class="form-control" name="course_code" value="<?= htmlspecialchars($course['course_code']) ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="course_name">Course Name:</label>
                                                        <input type="text" class="form-control" name="course_name" value="<?= htmlspecialchars($course['course_name']) ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="credit">Credits:</label>
                                                        <input type="number" class="form-control" name="credit" value="<?= htmlspecialchars($course['credit']) ?>" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Update Course</button>
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Include Bootstrap JS (Optional) -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script> -->
</body>
</html>