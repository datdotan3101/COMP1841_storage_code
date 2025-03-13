<?php
session_start();
include '../includes/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Delete previous comment
        $query = "DELETE FROM comments WHERE post_id = :id";
        $statement = $conn->prepare($query);
        $statement->execute([':id' => $id]);

        // Delete post
        $query = "DELETE FROM posts WHERE id = :id";
        $statement = $conn->prepare($query);
        $statement->execute([':id' => $id]);

        $_SESSION['success'] = "The post has been deleted successfully.!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error when deleting post: " . $e->getMessage();
    }
}

header("Location: ../index.php");
exit;
