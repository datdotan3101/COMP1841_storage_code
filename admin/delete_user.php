<?php
session_start();
include '../includes/config.php';

// Check if not admin then redirect
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

// Check if there is an ID to delete
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];
    $admin_id = $_SESSION['user_id'];
    // Check if admin is deleting himself
    if ($id === $admin_id) {
        $_SESSION['error'] = "You cannot delete your account yourself!";
        header("Location: users.php");
        exit();
    }

    // Get information of user to delete
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $user = $stmt->fetch();
    // Check if user exists
    if (!$user) {
        $_SESSION['error'] = "User does not exist!";
        header("Location: users.php");
        exit();
    }

    // If the user is an admin, only other admins can delete
    if ($user['role'] === 'admin') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = "Admin deleted successfully!";
        header("Location: users.php");
        exit();
    } else {

        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $_SESSION['success'] = "User deleted successfully!";
        header("Location: users.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid ID!";
    header("Location: users.php");
    exit();
}
