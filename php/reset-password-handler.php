<?php
session_start();
include "../db_conn.php";
if (isset($_SESSION['reset_user'], $_POST['new_password'], $_POST['confirm_password'])) {
    $email = $_SESSION['reset_user'];
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    // Validate the input
    if (empty($new_password) || empty($confirm_password)) {
        header("Location: ../reset-password.php?error=All fields are required");
        exit();
    }
    if ($new_password !== $confirm_password) {
        header("Location: ../reset-password.php?error=Passwords do not match");
        exit();
    }
    // Hash the new password
    $hashed_password = md5($new_password);
    // Update the password in the database
    $sql = "UPDATE user SET password='$hashed_password' WHERE email='$email'";
    if (mysqli_query($conn, $sql)) {
        unset($_SESSION['reset_user']); // Remove session variable
        header("Location: ../index.php?success=Password successfully updated");
    } else {
        header("Location: ../reset-password.php?error=Error updating password: " . mysqli_error($conn));
    }
} else {
    header("Location: ../forgot_password.php");
}