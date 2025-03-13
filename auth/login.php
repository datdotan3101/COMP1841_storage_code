<?php
session_start();
include '../includes/config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username)) {
        $error = "Please enter your username!";
    } elseif (empty($password)) {
        $error = "Please enter your password!";
    }

    if (!$error) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = htmlspecialchars($user['username']);

            header("Location: ../index.php");
            exit();
        } else {
            $error = "Incorrect username or password!";
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
    <link rel="stylesheet" href="./../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow p-4" style="max-width: 350px; width: 100%;">
        <h2 class="text-center">Login</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger text-center" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateForm()">
            <div class="mb-3">
                <input type="text" id="username" name="username" class="form-control" placeholder="Username" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                <div class="text-danger mt-1" id="usernameError"></div>
            </div>

            <div class="mb-3 position-relative">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                <span class="position-absolute top-50 end-0 translate-middle-y px-2" onclick="togglePassword()" style="cursor: pointer;">👁️</span>
                <div class="text-danger mt-1" id="passwordError"></div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="mt-3 text-center">Don't have an account? <a href="register.php">Sign up</a></p>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var toggleIcon = passwordField.nextElementSibling;

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.textContent = "👁️‍🗨️";
            } else {
                passwordField.type = "password";
                toggleIcon.textContent = "👁️";
            }
        }

        function validateForm() {
            let username = document.getElementById("username").value.trim();
            let password = document.getElementById("password").value.trim();
            let isValid = true;

            document.getElementById("usernameError").textContent = "";
            document.getElementById("passwordError").textContent = "";

            if (!username) {
                document.getElementById("usernameError").textContent = "Please enter your username!";
                isValid = false;
            }
            if (!password) {
                document.getElementById("passwordError").textContent = "Please enter your password!";
                isValid = false;
            }
            return isValid;
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>