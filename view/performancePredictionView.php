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
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 900px;
            margin: auto;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }
        .card-title {
            font-weight: bold;
            font-size: 1.25rem;
        }
        .card h3 {
            font-size: 1.75rem;
            color: #007bff;
        }
        .row {
            margin-bottom: 20px;
        }
        @media (max-width: 576px) {
            .card-title {
                font-size: 1rem;
            }
            .card h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Performance Prediction</h2>
        <h5 style="color:rgb(241, 50, 7);" class="text-center card-title mb-4">Your Current CGPA: <?php echo $cgpa; ?></h5>

        <!-- <div class="text-center mb-4">
            <h5 class="card-title">Your Current CGPA</h5>
            <h3 style="color:#007bff;"><?php echo $cgpa; ?></h3>
        </div> -->

        <!-- Highest and Lowest CGPA in a single row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Predicted Highest CGPA</h5>
                        <p class="card-text">Based on your current academic record, your highest predicted CGPA after graduation is:</p>
                        <h3><?php echo $highest_cgpa; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Predicted Lowest CGPA</h5>
                        <p class="card-text">Based on your current academic record, your lowest predicted CGPA after graduation is:</p>
                        <h3><?php echo $lowest_cgpa; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts in a single row -->
        <div class="row">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Predicted CGPA Range (Bar Chart)</h5>
                        <canvas id="cgpaBarChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title">Predicted CGPA Range (Line Chart)</h5>
                        <canvas id="cgpaLineChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="profile.php" class="btn btn-primary mt-3">Back to Profile</a>
            <br><br>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Bar Chart
        const barCtx = document.getElementById('cgpaBarChart').getContext('2d');
        const cgpaBarChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['Highest CGPA', 'Current CGPA', 'Lowest CGPA'],
                datasets: [{
                    label: 'CGPA',
                    data: [<?php echo $highest_cgpa; ?>, <?php echo $cgpa; ?>, <?php echo $lowest_cgpa; ?>],
                    backgroundColor: ['#4CAF50', '#d1da3a', '#FF5733'],
                    // borderColor: ['#388E3C', '#C0392B'],
                    // borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 4.0 // Assuming 4.0 as the maximum CGPA scale
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Line Chart
        const lineCtx = document.getElementById('cgpaLineChart').getContext('2d');
        const cgpaLineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: ['Highest CGPA', 'Current CGPA', 'Lowest CGPA'], // Updated labels
                datasets: [
                    {
                        label: ['CGPA'],
                        data: [<?php echo $highest_cgpa; ?>, <?php echo $cgpa; ?>, <?php echo $lowest_cgpa; ?>],
                        borderColor: ['#4CAF50', '#d1da3a', '#FF5733'],
                        backgroundColor: ['#4CAF50', '#d1da3a', '#FF5733'],
                        // fill: true,
                        // tension: 0.3
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 4.0 // Assuming a maximum CGPA of 4.0
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                },
                // elements: {
                //     point: {
                //         pointStyle: 'rectRounded',
                //         backgroundColor: '#FF5733'
                //     }
                // }
            }
        });
    </script>
</body>
</html>