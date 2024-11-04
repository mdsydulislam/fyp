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
    <title>Forgot Password</title>
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
        .form-container h1 {
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
        <form class="form-container" action="php/forgot-password-handler.php" method="post" style="width: 450px;">
            <!-- Title -->
            <h1 class="text-center p-3">Forgot Password</h1>
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
            <!-- Role Selection -->
            <div class="mb-3">
                <label for="role" class="form-label">Select Role</label>
                <select class="form-select" name="role" id="role" required>
                    <option value="student">Student</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            <!-- Verify Button -->
            <button type="submit" class="btn btn-primary">Verify</button>
            <!-- Remember Password -->
            <div class="link-container mt-3">
                <a href="index.php">Remember Password? Login</a>
            </div>
        </form>
    </div>
</body>
</html>