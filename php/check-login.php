<?php  
session_start();
include "../db_conn.php";
if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['role'])) {
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    $email = test_input($_POST['email']);
    $password = test_input($_POST['password']);
    $role = test_input($_POST['role']);
    if (empty($email)) {
        header("Location: ../index.php?error=Email is Required");
        exit();
    } else if (empty($password)) {
        header("Location: ../index.php?error=Password is Required");
        exit();
    } else {
        // Hashing the password
        $password = md5($password);   
        $sql = "SELECT * FROM user WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            if ($row['password'] === $password && $row['role'] == $role) {
                // Store user session variables
                $_SESSION['name'] = $row['name'];
                $_SESSION['id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['email'] = $row['email'];

                // Redirect based on role
                if ($row['role'] == 'student') {
                    header("Location: ../profile.php"); // Redirect students to profile page
                } elseif ($row['role'] == 'staff') {
                    header("Location: ../courseCatalogue.php"); // Redirect staff to course catalog page
                }
                exit();
            } else {
                header("Location: ../index.php?error=Incorrect Email or Password");
                exit();
            }
        } else {
            header("Location: ../index.php?error=Incorrect Email or Password");
            exit();
        }
    }
} else {
    header("Location: ../index.php");
    exit();
}