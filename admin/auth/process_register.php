<?php
session_start();
include __DIR__ . '/../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(htmlspecialchars($_POST['username']));
    $email = trim(strtolower($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'] ?? 'user';

    $_SESSION['errors'] = [];

    // Check username
    if (empty($username)) {
        $_SESSION['errors']['username'] = "Please enter username.";
    } elseif (strlen($username) < 4) {
        $_SESSION['errors']['username'] = "Username must be at least 4 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $_SESSION['errors']['username'] = "Usernames must contain only letters, numbers, and underscores.";
    }

    // Check email
    if (empty($email)) {
        $_SESSION['errors']['email'] = "Please enter email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['errors']['email'] = "Invalid email.";
    }

    // Check password
    if (empty($password)) {
        $_SESSION['errors']['password'] = "Please enter password.";
    } elseif (strlen($password) < 8) {
        $_SESSION['errors']['password'] = "The password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[\W]/', $password)) {
        $_SESSION['errors']['password'] = "The password must contain at least one uppercase letter, one number, and one special character.";
    }

    // Check password confirmation
    if (empty($confirm_password)) {
        $_SESSION['errors']['confirm_password'] = "Please re-enter password.";
    } elseif ($password !== $confirm_password) {
        $_SESSION['errors']['confirm_password'] = "Passwords do not match.";
    }

    // Check if username & email already exist
    if (empty($_SESSION['errors'])) {
        $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE username = :username OR email = :email");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row['username'] === $username) {
                $_SESSION['errors']['username'] = "Username already exists.";
            }
            if ($row['email'] === $email) {
                $_SESSION['errors']['email'] = "Email is already in use.";
            }
        }
    }

    // If there is an error, go back to the registration page
    if (!empty($_SESSION['errors'])) {
        // Save the entered data
        $_SESSION['old_input'] = $_POST;
        header("Location: register.php");
        exit();
    }

    // Encrypt password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Add user to database
    try {
        $sql = "INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->execute();

        $_SESSION['success'] = "Registration successful! Please log in.";
        header("Location: login.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['errors']['database'] = "Lỗi đăng ký: " . $e->getMessage();
        header("Location: register.php");
        exit();
    }
}
