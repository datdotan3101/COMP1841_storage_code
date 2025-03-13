<?php
session_start();
include '../../includes/config.php';

$errors = ['username' => '', 'password' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Kiểm tra nếu trống
    if (empty($username)) {
        $errors['username'] = "Please enter your username.";
    }
    if (empty($password)) {
        $errors['password'] = "Please enter your password.";
    }

    if (empty($errors['username']) && empty($errors['password'])) {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username AND role = 'admin'");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = htmlspecialchars($user['username']);
                $_SESSION['role'] = 'admin';

                header("Location: ../index.php");
                exit();
            } else {
                $errors['username'] = "Incorrect username or password!";
            }
        } catch (PDOException $e) {
            $errors['username'] = "An error occurred, please try again later!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow-sm p-4" style="max-width: 400px; width: 100%;">
        <h2 class="text-center mb-3">Admin Login</h2>

        <form method="POST" id="loginForm">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control <?= !empty($errors['username']) ? 'is-invalid' : '' ?>" placeholder="Username" value="<?= htmlspecialchars($username ?? '') ?>">
                <div class="invalid-feedback"><?= $errors['username'] ?></div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>" placeholder="Password">
                <div class="invalid-feedback"><?= $errors['password'] ?></div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="text-center mt-3">
            Don't have an account? <a href="register.php" class="text-decoration-none">Register</a>
        </p>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            let username = document.querySelector('input[name="username"]');
            let password = document.querySelector('input[name="password"]');
            let valid = true;

            if (username.value.trim() === '') {
                username.classList.add('is-invalid');
                username.nextElementSibling.textContent = "Please enter your username.";
                valid = false;
            } else {
                username.classList.remove('is-invalid');
                username.nextElementSibling.textContent = "";
            }

            if (password.value.trim() === '') {
                password.classList.add('is-invalid');
                password.nextElementSibling.textContent = "Please enter your password.";
                valid = false;
            } else {
                password.classList.remove('is-invalid');
                password.nextElementSibling.textContent = "";
            }

            if (!valid) event.preventDefault();
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>