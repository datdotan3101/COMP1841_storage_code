<?php
session_start();
include '../includes/config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "DELETE FROM contacts WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->execute([':id' => $id]);

    $_SESSION['message'] = "Delete message successful!";
    $_SESSION['message_status'] = "success";
}

header("Location: messages.php");
exit();
