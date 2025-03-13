<?php
session_start();
include '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You have to sign in to comment");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'] ?? null;
    $comment = trim($_POST['comment'] ?? '');

    // Check for valid data
    if (!$post_id || empty($comment)) {
        die("Lỗi: Dữ liệu không hợp lệ.");
    }

    // Add comments to database
    $query = "INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([$post_id, $user_id, $comment])) {
        header("Location: ../index.php");
        exit;
    } else {
        die("Error: Cannot add comment.");
    }
} else {
    die("Error: Invalid method.");
}
