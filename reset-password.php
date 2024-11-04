<?php
session_start();
if (!isset($_SESSION['reset_user'])) {
    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <style>
        body {
            background-color: #b3e7dc; /* Match background color */
        }
        .form-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
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
        .form-container .link-container {
            text-align: center;
            margin-top: 10px;
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
        <form class="form-container" action="php/reset-password-handler.php" method="post" style="width: 450px;">
            <!-- Title -->
            <h1 class="text-center p-3">Reset Password</h1>
            <!-- Error Message -->
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?= $_GET['error'] ?>
                </div>
            <?php } ?>
            <!-- New Password Input -->
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter New Password" required>
            </div>
            <!-- Confirm Password Input -->
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
            </div>
            <!-- Update Password Button -->
            <button type="submit" class="btn btn-primary">Update Password</button>
            <!-- Remember Password -->
            <div class="link-container mt-3">
                <a href="index.php">Remember Password? Login</a>
            </div>
        </form>
    </div>
</body>
</html>
