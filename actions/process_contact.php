<?php
session_start();
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Check input data
    if (empty($name) || empty($email) || empty($message)) {
        $_SESSION['contact_message'] = "Please enter complete information!";
        $_SESSION['contact_status'] = "error";
        header("Location: ../contact/contact.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['contact_message'] = "Invalid email!";
        $_SESSION['contact_status'] = "error";
        header("Location: ../contact/contact.php");
        exit();
    }

    try {
        $query = "INSERT INTO contacts (name, email, message) VALUES (:name, :email, :message)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message
        ]);

        $_SESSION['contact_message'] = "Contact sent successfully! Admin will respond soon.";
        $_SESSION['contact_status'] = "success";
    } catch (PDOException $e) {
        $_SESSION['contact_message'] = "System error, please try again later!";
        $_SESSION['contact_status'] = "error";
    }

    header("Location: ../contact/contact.php");
    exit();
}
