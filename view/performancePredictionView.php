<?php
// session_start();
include 'db_conn.php';

// Ensure only students can access this page
if ($_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

// Get the user ID from session
$user_id = $_SESSION['id'];

// Fetch student data from the database
$query = "SELECT cgpa, attempted_credits, earned_credits, excluded_credits, required_course_credits FROM student_data WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$pass_required_courses = [
    'BUM2413', 'BCI1023'
];
$total_pass_required_credits = 0;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    // Variables for calculations
    $cgpa = $row['cgpa'];
    $attempted_credits = $row['attempted_credits'];
    $earned_credits = $row['earned_credits'];
    $excluded_credits = $row['excluded_credits'];
    $required_course_credits = $row['required_course_credits'];

    // Determine if the student has passed the excluded courses
    // Assume that passing means earning credits for the excluded courses
    // This can be adjusted based on your specific criteria
    $passed_excluded_courses = ($excluded_credits > 0 && $earned_credits >= $excluded_credits) ? true : false;

    // Calculate the highest CGPA based on whether the student has passed excluded courses
    if ($passed_excluded_courses) {
        $highest_cgpa = (($cgpa * ($attempted_credits - $excluded_credits)) + ((118 - $earned_credits) * 4)) / 118;
    } else {
        $highest_cgpa = (($cgpa * ($attempted_credits - $excluded_credits)) + ((118 - $earned_credits - $excluded_credits) * 4)) / 118;
    }

    if ($passed_excluded_courses) {
        $lowest_cgpa = (($cgpa * ($attempted_credits - $excluded_credits)) + ((118 - $earned_credits) * 1)) / 118;
    } else {
        $lowest_cgpa = (($cgpa * ($attempted_credits - $excluded_credits)) + ((118 - $earned_credits - $excluded_credits) * 1)) / 118;
    }

    // if ($passed_excluded_courses) {
    //     $lowest_cgpa = (($cgpa * ($attempted_credits - $excluded_credits)) + ((20 - $required_course_credits) * 2) + ((118 - $earned_credits - (20 - $required_course_credits)) * 1)) / 118;
    // } else {
    //     $lowest_cgpa = (($cgpa * ($attempted_credits - $excluded_credits)) + ((20 - $required_course_credits) * 2) + ((118 - $earned_credits - $excluded_credits - (20 - $required_course_credits)) * 1)) / 118;
    // }

    // Format the result to two decimal places
    $highest_cgpa = number_format($highest_cgpa, 2);
    $lowest_cgpa = number_format($lowest_cgpa, 2);
} else {
    $highest_cgpa = "No data available.";
    $lowest_cgpa = "No data available.";
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Performance Prediction</title>
</head>
<body>
    <div class="container mt-5">
        <h2>Performance Prediction</h2>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Your Predicted Highest CGPA</h5>
                <p class="card-text">Based on your current academic record, your highest predicted CGPA after graduation is:</p>
                <h3><?php echo $highest_cgpa; ?></h3>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Your Predicted Lowest CGPA</h5>
                <p class="card-text">Based on your current academic record, your lowest predicted CGPA after graduation is:</p>
                <h3><?php echo $lowest_cgpa; ?></h3>
            </div>
        </div>
        <a href="profile.php" class="btn btn-primary mt-3">Back to Profile</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>