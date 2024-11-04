<?php
session_start();
if (isset($_SESSION['email']) && isset($_SESSION['id'])) {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <style>
        body {
            background-color: #b3e7dc; /* Match the light background color */
        }
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }
        .form-container img {
            display: block;
            margin: 0 auto;
        }
        .form-container h4, .form-container h2 {
            text-align: center;
        }
        .form-container .btn-primary {
            width: 100%;
        }
        .form-container a {
            text-decoration: none;
            color: #007bff;
        }
        .form-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh">
        <form class="form-container" action="php/register-handler.php" method="post" style="width: 450px;">
            <!-- Logo Image -->
            <img src="assets/img/logo.png" alt="ACPS Logo" width="100" height="100">
            <!-- Title -->
            <h4>Academic Course Planner System</h4>
            <h2>Registration</h2>
            <!-- Error Message -->
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_GET['error'] ?>
                </div>
            <?php } ?>
            <!-- Name Input -->
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Your Name" required>
            </div>
            <!-- Email Input -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter Your Email" required>
            </div>
            <!-- Password Input -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password" required>
            </div>
            <!-- Confirm Password Input -->
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" id="confirm_password" placeholder="Confirm Password" required>
            </div>
            <!-- Register Button -->
            <button type="submit" class="btn btn-primary">Register</button>
            <!-- Already have an account Link -->
            <div class="mt-3 text-center">
                <a href="index.php">Already have an account?</a>
            </div>
        </form>
    </div>
</body>
</html>