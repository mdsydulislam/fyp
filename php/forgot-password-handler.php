<?php
session_start();
include "../db_conn.php";
if (isset($_POST['name'], $_POST['email'], $_POST['role'])) {
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $name = test_input($_POST['name']);
    $email = test_input($_POST['email']);
    $role = test_input($_POST['role']);
    // Validate the input
    if (empty($name) || empty($email) || empty($role)) {
        header("Location: ../forgot_password.php?error=All fields are required");
        exit();
    }
    // Check if the name, email, and role match
    $sql = "SELECT * FROM user WHERE name='$name' AND email='$email' AND role='$role'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        // Name, email, and role matched
        $_SESSION['reset_user'] = $email; // Store email for password reset
        header("Location: ../reset-password.php");
    } else {
        header("Location: ../forgot_password.php?error=Invalid details provided");
    }
} else {
    header("Location: ../forgot_password.php");
}