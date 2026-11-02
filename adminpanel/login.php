<?php
ob_start();
session_start();
require "../koneksi.php";

$loginError = '';

if (isset($_POST['loginbtn'])) {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');

    $query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
    $countdata = mysqli_num_rows($query);

    if ($countdata > 0) {
        $data = mysqli_fetch_array($query);
        if (password_verify($password, $data['PASSWORD'])) {
            $_SESSION['username'] = $data['username'];
            $_SESSION['login'] = true;
            header("Location: index.php");
            exit;
        } else {
            $loginError = "Password salah!";
        }
    } else {
        $loginError = "Akun tidak tersedia!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.5-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1d3557, #457b9d);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-box {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;

            .login-box img {
                display: block;
                margin: 0 auto;
                max-width: 120px;
            }

        }

        .form-control:focus {
            box-shadow: 0 0 5px #1d3557;
            border-color: #1d3557;
        }

        .show-password {
            cursor: pointer;
            position: absolute;
            right: 15px;
            top: 35px;
            color: #aaa;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <div class="text-center mb-4">
            <img src="../image/BekasButClassy.png" alt="BekasButClassy Logo"
                style="width: 100px; height: auto; margin-bottom: 10px;">
            <h3 class="text-primary"><i class="fas fa-user-shield me-2"></i>Login Admin</h3>
        </div>


        <?php if ($loginError): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?= $loginError ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="mb-3 position-relative">
                <label for="username" class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" name="username" id="username" required
                        placeholder="Masukkan username">
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" name="password" id="password" required
                        placeholder="Masukkan password">
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()" tabindex="-1">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="d-grid">
                <button class="btn btn-primary" type="submit" name="loginbtn"><i
                        class="fas fa-sign-in-alt me-2"></i>Login</button>
            </div>
        </form>
    </div>

    <script src="../bootstrap-5.3.5-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../fontawesome/js/all.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>