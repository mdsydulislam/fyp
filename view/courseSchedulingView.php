<?php
include 'db_conn.php';

if ($_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

// Initialize variables
$courses = [];
$sections = [];
$labs = [];
$selectedCourses = [];

// Fetch all courses from the database
$courseQuery = "SELECT id, course_code, course_name, credit FROM courses";
$courseResult = $conn->query($courseQuery);
while ($row = $courseResult->fetch_assoc()) {
    $courses[] = $row;
}

// Persist selected data in session if not already set
if (!isset($_SESSION['selected_courses'])) {
    $_SESSION['selected_courses'] = [];
}
$selectedCourses = &$_SESSION['selected_courses'];

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch sections for the selected course
    if (isset($_POST['get_sections']) && $_POST['get_sections'] == 1 && !empty($_POST['course_id'])) {
        $courseId = intval($_POST['course_id']);
        $sectionsQuery = "SELECT id, section_number, time_slot, day FROM sections WHERE course_id = ?";
        $stmt = $conn->prepare($sectionsQuery);
        $stmt->bind_param('i', $courseId);
        $stmt->execute();
        $sectionsResult = $stmt->get_result();
        $sections = [];
        while ($row = $sectionsResult->fetch_assoc()) {
            $sections[] = $row;
        }
        $_SESSION['selected_course_id'] = $courseId;
    }

    // Fetch labs for the selected section
    if (isset($_POST['get_labs']) && $_POST['get_labs'] == 1 && !empty($_POST['section_id'])) {
        $sectionId = intval($_POST['section_id']);
        $labsQuery = "SELECT id, lab_code, time_slot, day FROM labs WHERE section_id = ?";
        $stmt = $conn->prepare($labsQuery);
        $stmt->bind_param('i', $sectionId);
        $stmt->execute();
        $labsResult = $stmt->get_result();
        $labs = [];
        while ($row = $labsResult->fetch_assoc()) {
            $labs[] = $row;
        }
        $_SESSION['selected_section_id'] = $sectionId;
    }

    // Add course, section, and lab to the selected courses list
    if (isset($_POST['add_course']) && isset($_POST['course_id'], $_POST['section_id']) && !empty($_POST['course_id']) && !empty($_POST['section_id'])) {
        $courseId = intval($_POST['course_id']);
        $sectionId = intval($_POST['section_id']);
        $labId = isset($_POST['lab_id']) && !empty($_POST['lab_id']) ? intval($_POST['lab_id']) : null;

        // Fetch course details
        $courseQuery = "SELECT course_code, course_name, credit FROM courses WHERE id = ?";
        $stmt = $conn->prepare($courseQuery);
        $stmt->bind_param('i', $courseId);
        $stmt->execute();
        $courseResult = $stmt->get_result();
        $course = $courseResult->fetch_assoc();

        // Check if course is already added
        $courseAlreadyAdded = false;
        foreach ($selectedCourses as $existingCourse) {
            if ($existingCourse['course']['course_code'] === $course['course_code']) {
                $courseAlreadyAdded = true;
                $conflict_error = "Course {$course['course_code']} is already added to your schedule.";
                break;
            }
        }

        if (!$courseAlreadyAdded) {
            // Fetch section details
            $sectionQuery = "SELECT section_number, time_slot, day FROM sections WHERE id = ?";
            $stmt = $conn->prepare($sectionQuery);
            $stmt->bind_param('i', $sectionId);
            $stmt->execute();
            $sectionResult = $stmt->get_result();
            $section = $sectionResult->fetch_assoc();

            // Fetch lab details if a lab is selected
            $lab = null;
            if ($labId) {
                $labQuery = "SELECT lab_code, time_slot, day FROM labs WHERE id = ?";
                $stmt = $conn->prepare($labQuery);
                $stmt->bind_param('i', $labId);
                $stmt->execute();
                $labResult = $stmt->get_result();
                $lab = $labResult->fetch_assoc();
            }

            // Check for time conflicts
            $hasConflict = false;
            $conflict_message = '';
            
            foreach ($selectedCourses as $existingCourse) {
                // Check new section time against existing sections and labs
                if ($existingCourse['section']['day'] == $section['day'] && 
                    $existingCourse['section']['time_slot'] == $section['time_slot']) {
                    $hasConflict = true;
                    $conflict_message = "{$course['course_code']} Section {$section['section_number']} clashes with {$existingCourse['course']['course_code']} Section {$existingCourse['section']['section_number']}";
                    break;
                }
                
                if ($existingCourse['lab'] && 
                    $existingCourse['lab']['day'] == $section['day'] && 
                    $existingCourse['lab']['time_slot'] == $section['time_slot']) {
                    $hasConflict = true;
                    $conflict_message = "{$course['course_code']} Section {$section['section_number']} clashes with {$existingCourse['course']['course_code']} Lab {$existingCourse['lab']['lab_code']}";
                    break;
                }

                // Check new lab time against existing sections and labs
                if ($lab) {
                    if ($existingCourse['section']['day'] == $lab['day'] && 
                        $existingCourse['section']['time_slot'] == $lab['time_slot']) {
                        $hasConflict = true;
                        $conflict_message = "{$course['course_code']} Lab {$lab['lab_code']} clashes with {$existingCourse['course']['course_code']} Section {$existingCourse['section']['section_number']}";
                        break;
                    }
                    
                    if ($existingCourse['lab'] && 
                        $existingCourse['lab']['day'] == $lab['day'] && 
                        $existingCourse['lab']['time_slot'] == $lab['time_slot']) {
                        $hasConflict = true;
                        $conflict_message = "{$course['course_code']} Lab {$lab['lab_code']} clashes with {$existingCourse['course']['course_code']} Lab {$existingCourse['lab']['lab_code']}";
                        break;
                    }
                }
            }

            // Add to the selected courses array if no conflict
            if (!$hasConflict) {
                $selectedCourses[] = [
                    'course' => $course,
                    'section' => $section,
                    'lab' => $lab
                ];
                // Reset session variables after successful addition
                unset($_SESSION['selected_course_id']);
                unset($_SESSION['selected_section_id']);
            } else {
                $conflict_error = $conflict_message;
            }
        }
    }
}

// Handle course removal
if (isset($_GET['remove'])) {
    $courseCodeToRemove = $_GET['remove'];
    foreach ($selectedCourses as $key => $course) {
        if ($course['course']['course_code'] === $courseCodeToRemove) {
            unset($selectedCourses[$key]);
            // Re-index array after removal
            $selectedCourses = array_values($selectedCourses);
            break;
        }
    }
}

// Populate sections if a course was previously selected
if (isset($_SESSION['selected_course_id'])) {
    $courseId = intval($_SESSION['selected_course_id']);
    $sectionsQuery = "SELECT id, section_number, time_slot, day FROM sections WHERE course_id = ?";
    $stmt = $conn->prepare($sectionsQuery);
    $stmt->bind_param('i', $courseId);
    $stmt->execute();
    $sectionsResult = $stmt->get_result();
    $sections = [];
    while ($row = $sectionsResult->fetch_assoc()) {
        $sections[] = $row;
    }
}

// Populate labs if a section was previously selected
if (isset($_SESSION['selected_section_id'])) {
    $sectionId = intval($_SESSION['selected_section_id']);
    $labsQuery = "SELECT id, lab_code, time_slot, day FROM labs WHERE section_id = ?";
    $stmt = $conn->prepare($labsQuery);
    $stmt->bind_param('i', $sectionId);
    $stmt->execute();
    $labsResult = $stmt->get_result();
    $labs = [];
    while ($row = $labsResult->fetch_assoc()) {
        $labs[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Scheduling</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', Arial, sans-serif;
            background: #f9fafb;
            color: #333;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 2rem;
            border-bottom: 2px solid #6c757d;
            padding-bottom: 10px;
        }
        form {
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: 500;
        }
        select, button {
            display: block;
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            text-align: center;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .timetable td {
            height: 80px;
            text-align: center;
            vertical-align: top;
        }
        .course-block, .lab-block {
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 0.9em;
            margin: 2px;
        }
        .course-block {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #90caf9;
        }
        .lab-block {
            background-color: #f3e5f5;
            color: #6a1b9a;
            border: 1px solid #ce93d8;
        }
        .timetable th, .timetable td:hover {
            background-color: #f1f1f1;
        }

        /* Navbar toggle button styles */
        .navbar-toggler {
            padding: 0.25rem 0.5rem;
            font-size: 1rem;
            line-height: 1;
            background-color: transparent;
            border: 1px solid black;
            border-radius: 0.25rem;
            width: auto !important;
            min-width: 45px;
            height: 35px;
            position: relative;
            margin: 8px;
        }

        /* Toggle icon bars */
        .navbar-toggler .navbar-toggler-icon {
            display: inline-block;
            width: 1.5em;
            height: 1.5em;
            vertical-align: middle;
            background-size: 100%;
            background-repeat: no-repeat;
            background-position: center;
        }

        /* Hover state */
        .navbar-toggler:hover {
            background-color: rgba(199, 31, 31, 0.05);
        }

        /* Focus state */
        .navbar-toggler:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.25);
        }

        /* Active state */
        .navbar-toggler:active {
            background-color: rgba(0,0,0,.1);
        }

        /* Override any Bootstrap default width */
        @media (max-width: 991.98px) {
            .navbar-toggler {
                width: auto !important;
                display: inline-flex;
                align-items: center;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    

    <?php if (isset($conflict_error)): ?>
        <p class="error text-center"><?= $conflict_error ?></p>
    <?php endif; ?>

    <!-- Course Selection Form -->
    <form method="post">
        <h2>Course Scheduling</h2>
        <label for="course">Select Course:</label>
        <select name="course_id" id="course" onchange="this.form.get_sections.value=1;this.form.submit();">
            <option value="">Select a Course</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>" <?= (isset($_SESSION['selected_course_id']) && $_SESSION['selected_course_id'] == $course['id']) || (isset($_POST['course_id']) && $_POST['course_id'] == $course['id']) ? 'selected' : '' ?>>
                    <?= $course['course_code'] . " - " . $course['course_name'] . " (" . $course['credit'] . ")" ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="section">Select Section:</label>
        <select name="section_id" id="section" onchange="this.form.get_labs.value=1;this.form.submit();">
            <option value="">Select a Section</option>
            <?php foreach ($sections as $section): ?>
                <option value="<?= $section['id'] ?>" <?= (isset($_SESSION['selected_section_id']) && $_SESSION['selected_section_id'] == $section['id']) || (isset($_POST['section_id']) && $_POST['section_id'] == $section['id']) ? 'selected' : '' ?>>
                    <?= $section['section_number'] . " (" . $section['day'] . ", " . $section['time_slot'] . ")" ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="lab">Select Lab:</label>
        <select name="lab_id" id="lab">
            <option value="">Select a Lab</option>
            <?php foreach ($labs as $lab): ?>
                <option value="<?= $lab['id'] ?>" <?= isset($_POST['lab_id']) && $_POST['lab_id'] == $lab['id'] ? 'selected' : '' ?>>
                    <?= $lab['lab_code'] . " (" . $lab['day'] . ", " . $lab['time_slot'] . ")" ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="hidden" name="get_sections" value="0">
        <input type="hidden" name="get_labs" value="0">
        <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
    </form>

    <!-- Selected Courses Table -->
    <h2>Selected Courses</h2>
    <table>
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Credits</th>
                <th>Section</th>
                <th>Lab</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($selectedCourses)): ?>
                <tr>
                    <td colspan="6" class="text-center">No courses selected.</td>
                </tr>
            <?php else: ?>
                <?php
                $totalCredits = 0;
                foreach ($selectedCourses as $selected):
                    $totalCredits += intval($selected['course']['credit']);
                ?>
                    <tr>
                        <td><?= $selected['course']['course_code'] ?></td>
                        <td><?= $selected['course']['course_name'] ?></td>
                        <td><?= $selected['course']['credit'] ?></td>
                        <td><?= $selected['section']['section_number'] ?> | <?= $selected['section']['time_slot'] ?> | <?= $selected['section']['day'] ?></td>
                        <td><?= $selected['lab'] ? $selected['lab']['lab_code'] . " | " . $selected['lab']['time_slot'] . " | " . $selected['lab']['day'] : 'No Lab' ?></td>
                        <td>
                        <a href="?remove=<?= $selected['course']['course_code'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this course?');">Remove</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <!-- Total Credits Row -->
                <!-- <tr class="table-info">
                    <td colspan="2" class="text-right"><strong>Total Credits:</strong></td>
                    <td><strong><?= $totalCredits ?></strong></td>
                    <td colspan="3"></td>
                </tr> -->
                <h6 class="text-right">Total Credits: <?php echo $totalCredits; ?></h6>
            <?php endif; ?>
        </tbody>
    </table>
    <br><br><br>
</body>
</html>
