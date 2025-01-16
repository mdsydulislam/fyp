<?php
include 'db_conn.php'; // Include database connection

// Ensure only staff can access this page
if ($_SESSION['role'] !== 'staff') {
    header("Location: index.php");
    exit();
}

$error = ""; // To store error messages
$success = ""; // To store success messages

// Fetch courses assigned to the logged-in staff member
$courses_query = "SELECT * FROM courses WHERE staff_id='{$_SESSION['id']}'";
$courses_result = mysqli_query($conn, $courses_query);

// Add Section or Lab
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle adding a section
    if (isset($_POST['add_section'])) {
        $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
        $section_number = mysqli_real_escape_string($conn, $_POST['section_number']);
        $time_slot = mysqli_real_escape_string($conn, $_POST['time_slot']);
        $day = mysqli_real_escape_string($conn, $_POST['day']);

        $sql = "INSERT INTO sections (course_id, section_number, time_slot, day) VALUES ('$course_id', '$section_number', '$time_slot', '$day')";
        if (mysqli_query($conn, $sql)) {
            $success = "Section added successfully!";
        } else {
            $error = "Error adding section: " . mysqli_error($conn);
        }
    }

    // Handle adding a lab
    if (isset($_POST['add_lab'])) {
        $section_id = mysqli_real_escape_string($conn, $_POST['section_id']);
        $lab_code = mysqli_real_escape_string($conn, $_POST['lab_code']);
        $time_slot = mysqli_real_escape_string($conn, $_POST['time_slot']);
        $day = mysqli_real_escape_string($conn, $_POST['day']);

        $sql = "INSERT INTO labs (section_id, lab_code, time_slot, day) VALUES ('$section_id', '$lab_code', '$time_slot', '$day')";
        if (mysqli_query($conn, $sql)) {
            $success = "Lab added successfully!";
        } else {
            $error = "Error adding lab: " . mysqli_error($conn);
        }
    }

// Add section edit functionality
// if (isset($_POST['edit_section'])) {
//     $section_id = mysqli_real_escape_string($conn, $_POST['section_id']);
//     $section_number = mysqli_real_escape_string($conn, $_POST['section_number']);
//     $time_slot = mysqli_real_escape_string($conn, $_POST['time_slot']);
//     $day = mysqli_real_escape_string($conn, $_POST['day']);

//     $sql = "UPDATE sections SET section_number='$section_number', time_slot='$time_slot', day='$day' WHERE id='$section_id'";
//     if (mysqli_query($conn, $sql)) {
//         $_SESSION['success'] = "Section updated successfully!";
//     } else {
//         $_SESSION['error'] = "Error updating section: " . mysqli_error($conn);
//     }
//     // Reload the page without header redirect
//     echo "<script>window.location.href = 'courseCatalogue.php';</script>";
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_section'])) {
    $section_id = mysqli_real_escape_string($conn, $_POST['section_id']);
    $section_number = mysqli_real_escape_string($conn, $_POST['section_number']);
    $time_slot = mysqli_real_escape_string($conn, $_POST['time_slot']);
    $day = mysqli_real_escape_string($conn, $_POST['day']);

    $sql = "UPDATE sections SET section_number='$section_number', time_slot='$time_slot', day='$day' WHERE id='$section_id'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Section updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating section: " . mysqli_error($conn);
    }
    echo "<script>window.location.href = 'courseCatalogue.php';</script>";
    exit();
}


// Add lab edit functionality
if (isset($_POST['edit_lab'])) {
    $lab_id = mysqli_real_escape_string($conn, $_POST['lab_id']);
    $lab_code = mysqli_real_escape_string($conn, $_POST['lab_code']);
    $time_slot = mysqli_real_escape_string($conn, $_POST['time_slot']);
    $day = mysqli_real_escape_string($conn, $_POST['day']);

    $sql = "UPDATE labs SET lab_code='$lab_code', time_slot='$time_slot', day='$day' WHERE id='$lab_id'";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Lab updated successfully!";
    } else {
        $_SESSION['error'] = "Error updating lab: " . mysqli_error($conn);
    }
    // Reload the page without header redirect
    echo "<script>window.location.href = 'courseCatalogue.php';</script>";
    exit();
}
}


// Remove Section
if (isset($_GET['remove_section'])) {
    $section_id = mysqli_real_escape_string($conn, $_GET['remove_section']);

    // Delete all labs associated with the section
    $delete_labs_sql = "DELETE FROM labs WHERE section_id='$section_id'";
    mysqli_query($conn, $delete_labs_sql);

    // Delete the section
    $delete_section_sql = "DELETE FROM sections WHERE id='$section_id'";
    // mysqli_query($conn, $delete_section_sql);
    // $success = "Section and associated labs removed successfully!";
    if (mysqli_query($conn, $delete_section_sql)) {
        $_SESSION['success'] = "Section and associated labs removed successfully!";
    } else {
        $_SESSION['error'] = "Error removing section: " . mysqli_error($conn);
    }
    // Reload the page without header redirect
    echo "<script>window.location.href = 'courseCatalogue.php';</script>";
    exit();
    
}

// Remove Lab
if (isset($_GET['remove_lab'])) {
    $lab_id = mysqli_real_escape_string($conn, $_GET['remove_lab']);
    $sql = "DELETE FROM labs WHERE id='$lab_id'";
    // mysqli_query($conn, $sql);
    // $success = "Lab removed successfully!";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['success'] = "Lab removed successfully!";
    } else {
        $_SESSION['error'] = "Error removing lab: " . mysqli_error($conn);
    }
    // Reload the page without header redirect
    echo "<script>window.location.href = 'courseCatalogue.php';</script>";
    exit();
}

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
    <title>Course Catalogue</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" crossorigin="anonymous"></script>
    <style>
        .card { margin-bottom: 20px; }
        .popup-form { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #fff; padding: 20px; border: 1px solid #ddd; z-index: 1000; width: 90%; max-width: 400px; }
        .popup-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 999; }
        
        /* New styles for button layout */
        .action-buttons {
            display: flex;
            gap: 5px;
            align-items: center;
        }
        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.4;
        }
        .table td {
            padding: 0.5rem !important;
        }
        .action-column {
            width: 250px;
            max-width: 250px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Course Catalogue Timetable</h2>

    <!-- Display success or error messages -->
    <?php if (!empty($error)) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php } ?>
    <?php if (!empty($success)) { ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php } ?>

    <!-- Course List with Cards -->
    <?php while ($course = mysqli_fetch_assoc($courses_result)) { ?>
        <div class="card mb-3">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><?= htmlspecialchars($course['course_name']) ?> (<?= htmlspecialchars($course['course_code']) ?>)</h6>
                <button class="btn btn-sm btn-light add-section-btn" data-course-id="<?= $course['id'] ?>">
                    <i class="fas fa-plus"></i> Add Section
                </button>
            </div>

            <div class="card-body p-2">
                <!-- Sections for each course -->
                <?php
                $sections_query = "SELECT * FROM sections WHERE course_id='{$course['id']}'";
                $sections_result = mysqli_query($conn, $sections_query);
                ?>
                <div class="table-responsive-sm table-hover">
                    <table class="table table-sm table-borderless mb-0">
                        <thead class="text-light bg-dark">
                            <tr>
                                <th style="width: 20%">Section</th>
                                <th style="width: 20%">Time Slot</th>
                                <th style="width: 20%">Day</th>
                                <th class="action-column">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($section = mysqli_fetch_assoc($sections_result)) { 
                            ?>
                            <tr class="table-success">
                                <td>Section <?= htmlspecialchars($section['section_number']) ?></td>
                                <td><?= htmlspecialchars($section['time_slot']) ?></td>
                                <td><?= htmlspecialchars($section['day']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-light add-lab-btn" data-section-id="<?= $section['id'] ?>">
                                        <i class="fas fa-plus"></i></i> Add Lab
                                    </button>
                                    <button class="btn btn-sm btn-warning edit-section-btn" data-section-id="<?= $section['id'] ?>" data-section-number="<?= $section['section_number'] ?>" data-time-slot="<?= $section['time_slot'] ?>" data-day="<?= $section['day'] ?>">
                                        Edit
                                    </button>
                                    <!-- <button class="btn btn-sm btn-success" sectionEdit-button-id="<?= $section['id'] ?>"><i class="fas fa-edit"></i></button> -->
                                    <!-- <button class="btn btn-sm btn-warning" sectionEdit-button-id="<?= $section['id'] ?>">Edit</button> -->
                                    <a href="?remove_section=<?= $section['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this section and all associated labs?');">
                                        <i class="far fa-trash-alt"></i> Remove
                                    </a>
                                </td>
                            </tr>

                            <!-- Labs for each section -->
                            <?php
                            $labs_query = "SELECT * FROM labs WHERE section_id='{$section['id']}'";
                            $labs_result = mysqli_query($conn, $labs_query);
                            ?>
                            <tr>
                                <td colspan="4">
                                    <div class="table-responsive-sm">
                                        <table class="table table-sm table-bordered mt-2 mb-2">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 20%">Lab</th>
                                                    <th style="width: 20%">Time Slot</th>
                                                    <th style="width: 20%">Day</th>
                                                    <th class="action-column">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php while ($lab = mysqli_fetch_assoc($labs_result)) { ?>
                                                <tr>
                                                    <td>Lab <?= htmlspecialchars($lab['lab_code']) ?></td>
                                                    <td><?= htmlspecialchars($lab['time_slot']) ?></td>
                                                    <td><?= htmlspecialchars($lab['day']) ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-warning" labEdit-button-id="<?= $lab['id'] ?>">Edit</button>
                                                        <a href="?remove_lab=<?= $lab['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this lab?');">
                                                            <i class="far fa-trash-alt"></i> Remove
                                                        </a>
                                                    </td> 
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<!-- Popups for adding sections and labs -->
<div class="popup-overlay" id="popup-overlay"></div>

<!-- Add Section Popup -->
<div class="popup-form" id="add-section-popup">
    <form method="POST" action="">
        <h5>Add Section</h5>
        <input type="hidden" name="course_id" id="popup-course-id">
        <div class="form-group">
            <label>Section:</label>
            <input type="text" name="section_number" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="time_slot">Time Slot:</label>
            <select name="time_slot" class="form-control">
                <option value="8-10">8-10</option>
                <option value="10-12">10-12</option>
                <option value="12-14">12-14</option>
                <option value="14-16">14-16</option>
                <option value="16-18">16-18</option>
            </select>
        </div>
        <div class="form-group">
            <label for="day">Day:</label>
            <select name="day" class="form-control">
                <option value="Mon">Monday</option>
                <option value="Tue">Tuesday</option>
                <option value="Wed">Wednesday</option>
                <option value="Thu">Thursday</option>
                <option value="Fri">Friday</option>
            </select>
        </div>
        <button type="submit" name="add_section" class="btn btn-primary btn-sm">Add Section</button>
        <button type="button" class="btn btn-secondary btn-sm close-popup" onclick="closeAddSectionPopup()">Cancel</button>
    </form>
</div>

<!-- Add Lab Popup -->
<div class="popup-form" id="add-lab-popup">
    <form method="POST" action="">
        <h5>Add Lab</h5>
        <input type="hidden" name="section_id" id="popup-section-id">
        <div class="form-group">
            <label>Lab:</label>
            <input type="text" name="lab_code" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="time_slot">Time Slot:</label>
            <select name="time_slot" class="form-control">
                <option value="8-10">8-10</option>
                <option value="10-12">10-12</option>
                <option value="12-14">12-14</option>
                <option value="14-16">14-16</option>
                <option value="16-18">16-18</option>
            </select>
        </div>
        <div class="form-group">
            <label for="day">Day:</label>
            <select name="day" class="form-control">
                <option value="Mon">Monday</option>
                <option value="Tue">Tuesday</option>
                <option value="Wed">Wednesday</option>
                <option value="Thu">Thursday</option>
                <option value="Fri">Friday</option>
            </select>
        </div>
        <button type="submit" name="add_lab" class="btn btn-primary btn-sm">Add Lab</button>
        <button type="button" class="btn btn-secondary btn-sm close-popup" onclick="closeAddLabPopup()">Cancel</button>
    </form>
</div>

<!-- Edit Section Popup -->
<div class="popup-form" id="edit-section-popup">
    <form method="POST" action="">
        <h5>Edit Section</h5>
        <input type="hidden" name="section_id" id="edit-section-id">
        <div class="form-group">
            <label for="edit-section-number">Section:</label>
            <input type="text" name="section_number" id="edit-section-number" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="edit-time-slot">Time Slot:</label>
            <select name="time_slot" id="edit-time-slot" class="form-control">
                <option value="8-10">8-10</option>
                <option value="10-12">10-12</option>
                <option value="12-14">12-14</option>
                <option value="14-16">14-16</option>
                <option value="16-18">16-18</option>
            </select>
        </div>

        <div class="form-group">
            <label for="edit-day">Day:</label>
            <select name="day" id="edit-day" class="form-control">
                <option value="Mon">Monday</option>
                <option value="Tue">Tuesday</option>
                <option value="Wed">Wednesday</option>
                <option value="Thu">Thursday</option>
                <option value="Fri">Friday</option>
            </select>
        </div>

        <button type="submit" name="edit_section" class="btn btn-primary btn-sm">Save Changes</button>
        <button type="button" class="btn btn-secondary btn-sm" onclick="closeEditSectionPopup()">Cancel</button>
    </form>
</div>
<div class="popup-overlay" id="popup-overlay"></div>



<!-- Lab Edit Popup -->
<div class="popup-form" id="edit-lab-popup">
    <form method="POST" action="">
        <h5>Edit Lab</h5>
        <input type="hidden" name="lab_id" id="popup-lab-id">
        <div class="form-group">
            <label>Lab:</label>
            <input type="text" name="lab_code" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="time_slot">Time Slot:</label>
            <select name="time_slot" class="form-control">
                <option value="8-10">8-10</option>
                <option value="10-12">10-12</option>
                <option value="12-14">12-14</option>
                <option value="14-16">14-16</option>
                <option value="16-18">16-18</option>
            </select>
        </div>
        <div class="form-group">
            <label for="day">Day:</label>
            <select name="day" class="form-control">
                <option value="Mon">Monday</option>
                <option value="Tue">Tuesday</option>
                <option value="Wed">Wednesday</option>
                <option value="Thu">Thursday</option>
                <option value="Fri">Friday</option>
            </select>
        </div>
        <button type="submit" name="edit_lab" class="btn btn-primary btn-sm">Save Changes</button>
        <button type="button" class="btn btn-secondary btn-sm close-popup" onclick="closeEditLabPopup()">Cancel</button>
    </form>
</div>


<script>
document.querySelectorAll('.add-section-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('popup-course-id').value = this.dataset.courseId;
        document.getElementById('popup-overlay').style.display = 'block';
        document.getElementById('add-section-popup').style.display = 'block';
    });
});

function closeAddSectionPopup() {
    document.getElementById('add-section-popup').style.display = 'none';
    document.getElementById('popup-overlay').style.display = 'none';
}


document.querySelectorAll('.add-lab-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('popup-section-id').value = this.dataset.sectionId;
        document.getElementById('popup-overlay').style.display = 'block';
        document.getElementById('add-lab-popup').style.display = 'block';
    });
});

function closeAddLabPopup() {
    document.getElementById('add-lab-popup').style.display = 'none';
    document.getElementById('popup-overlay').style.display = 'none';
}


document.querySelectorAll('.edit-section-btn').forEach(button => {
        button.addEventListener('click', function() {
            // Populate the form fields with current values
            document.getElementById('edit-section-id').value = this.getAttribute('data-section-id');
            document.getElementById('edit-section-number').value = this.getAttribute('data-section-number');
            
            // Set the dropdown values
            document.getElementById('edit-time-slot').value = this.getAttribute('data-time-slot');
            document.getElementById('edit-day').value = this.getAttribute('data-day');

            // Show the popup
            document.getElementById('edit-section-popup').style.display = 'block';
            document.getElementById('popup-overlay').style.display = 'block';
        });
    });

function closeEditSectionPopup() {
    document.getElementById('edit-section-popup').style.display = 'none';
    document.getElementById('popup-overlay').style.display = 'none';
}

document.querySelectorAll('[labEdit-button-id]').forEach(btn => {
    btn.addEventListener('click', function () {
        // Populate the lab edit popup form with the current values
        document.getElementById('popup-lab-id').value = this.getAttribute('labEdit-button-id');
        document.getElementById('add-lab-popup').style.display = 'none';
        document.getElementById('edit-lab-popup').style.display = 'block';
        document.getElementById('popup-overlay').style.display = 'block';

        document.getElementById('edit-lab-popup').style.display = 'block';
        document.getElementById('popup-overlay').style.display = 'block';
    });
});

function closeEditLabPopup() {
    document.getElementById('edit-lab-popup').style.display = 'none';
    document.getElementById('popup-overlay').style.display = 'none';
}

</script>
</body>
</html>