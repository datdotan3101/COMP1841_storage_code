<?php
session_start();
include __DIR__ . '/../../includes/config.php';

// Check admin rights
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete post
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->execute(['id' => $id]);

    $_SESSION['success'] = "Post deleted successfully!";
    header("Location: posts.php");
    exit();
} else {
    $_SESSION['error'] = "No posts found!";
    header("Location: posts.php");
    exit();
}
