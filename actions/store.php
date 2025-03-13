<?php
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    // Get content from form
    $content = $_POST['content'];
    $subject_id = isset($_POST['subject_id']) && !empty($_POST['subject_id']) ? $_POST['subject_id'] : NULL;

    // Image upload processing
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "../uploads/";
        $file_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $file_name;

        // Check file format
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "uploads/" . $file_name;
            } else {
                $image_path = NULL;
            }
        } else {
            die("Error: Only JPG, JPEG, PNG, GIF uploads allowed");
        }
    } else {
        $image_path = NULL;
    }

    $query = "INSERT INTO posts (title, content, image, subject_id) VALUES (:title, :content, :image, :subject_id)";
    $statement = $conn->prepare($query);

    if (!$statement->execute([
        ':title' => $title,
        ':content' => $content,
        ':image' => $image_path,
        ':subject_id' => $subject_id
    ])) {
        print_r($statement->errorInfo());
        exit();
    }
    header("Location: ../index.php");
    exit();
}
