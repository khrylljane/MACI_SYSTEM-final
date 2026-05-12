<?php
session_start();
require 'include/db.php';

/* LOGIN */
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found!";
    }
}

/* REGISTER */
if (isset($_POST['register'])) {
    $username = $_POST['reg_username'];
    $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT * FROM users WHERE username=?");
    $check->bind_param("s", $username);
    $check->execute();
    $checkResult = $check->get_result();

    if ($checkResult->num_rows > 0) {
        $error = "Username already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();

        $success = "Account created successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login System</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            height: 100vh;
            background: url("../img/B.webp") no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial;
        }

        /* Dark overlay */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: -1;
        }

        .login-card {
            width: 420px;
            background: #fff;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }

        .title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .nav-tabs .nav-link {
            width: 50%;
            text-align: center;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn {
            border-radius: 10px;
        }

        .icon {
            margin-right: 8px;
            color: #0d6efd;
        }
    </style>
</head>

<body>

<div class="login-card">

    <h4 class="title">
        <i class="fa-solid fa-mobile-screen icon"></i>
        Inventory System
    </h4>

    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item w-50">
            <button class="nav-link active w-100" data-bs-toggle="tab" data-bs-target="#login">Login</button>
        </li>
        <li class="nav-item w-50">
            <button class="nav-link w-100" data-bs-toggle="tab" data-bs-target="#register">Register</button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- LOGIN -->
        <div class="tab-pane fade show active" id="login">
            <form method="POST">

                <div class="mb-2">
                    <i class="fa-solid fa-user icon"></i>
                    <input type="text" name="username" class="form-control" placeholder="Username" required>
                </div>

                <div class="mb-3">
                    <i class="fa-solid fa-lock icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <button name="login" class="btn btn-primary w-100">
                    Login
                </button>
            </form>
        </div>

        <!-- REGISTER -->
        <div class="tab-pane fade" id="register">
            <form method="POST">

                <div class="mb-2">
                    <i class="fa-solid fa-user-plus icon"></i>
                    <input type="text" name="reg_username" class="form-control" placeholder="New Username" required>
                </div>

                <div class="mb-3">
                    <i class="fa-solid fa-key icon"></i>
                    <input type="password" name="reg_password" class="form-control" placeholder="New Password" required>
                </div>

                <button name="register" class="btn btn-success w-100">
                    Create Account
                </button>
            </form>
        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>