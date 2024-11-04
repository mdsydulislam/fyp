<?php
session_start();
include "../db_conn.php";
if (isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['confirm_password'])) {
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $name = test_input($_POST['name']);
    $email = test_input($_POST['email']);
    $password = test_input($_POST['password']);
    $confirm_password = test_input($_POST['confirm_password']);
    // Validate the input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        header("Location: ../register.php?error=All fields are required");
        exit();
    }
    if ($password !== $confirm_password) {
        header("Location: ../register.php?error=Passwords do not match");
        exit();
    }
    // Hashing the password
    $password = md5($password);
    // Check if the email already exists
    $sql = "SELECT * FROM user WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        header("Location: ../register.php?error=Email already exists");
        exit();
    }
    // Insert the new student into the database
    $role = 'student'; // Automatically assigning the role as student
    $sql = "INSERT INTO user (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
    if (mysqli_query($conn, $sql)) {
        header("Location: ../index.php?success=Registration successful, please login");
    } else {
        header("Location: ../register.php?error=Error: " . mysqli_error($conn));
    }
} else {
    header("Location: ../register.php");
}