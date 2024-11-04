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
    <title>ACPS - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <style>
        body {
            background-color: #b3e7dc;
            font-family: Arial, sans-serif;
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
        }
        .form-container img {
            display: block;
            margin: 0 auto;
            max-width: 80px;
            height: auto;
        }
        .form-container h4, .form-container h2 {
            text-align: center;
            font-weight: 600;
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
        .form-container .link-container {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
                margin: 0 15px;
            }
            .form-container img {
                max-width: 60px;
            }
            .form-container h2, .form-container h4 {
                font-size: 1.2rem;
            }
            .form-container .link-container {
                flex-direction: column;
                text-align: center;
            }
            .form-container .link-container a {
                margin: 5px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh">
        <form class="form-container" action="php/check-login.php" method="post">
            <!-- Logo Image -->
            <img src="assets/img/logo.png" alt="ACPS Logo">
            <!-- Title -->
            <h4>Academic Course Planner System</h4>
            <h2>Login</h2>
            <!-- Error Message -->
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_GET['error'] ?>
                </div>
            <?php } ?>
            <!-- Email Input -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Enter Your Email" required>
            </div>
            <!-- Password Input -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter Your Password" required>
            </div>
            <!-- User Type Dropdown -->
            <div class="mb-3">
                <label class="form-label">User Type</label>
                <select class="form-select" name="role" required>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <!-- Login Button -->
            <button type="submit" class="btn btn-primary">Login</button>
            <!-- Links for Register and Forgot Password -->
            <div class="link-container mt-3">
                <a href="register.php">Register</a>
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
        </form>
    </div>
</body>
</html>