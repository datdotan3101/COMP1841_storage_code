<?php
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Check for valid ID
    if (!is_numeric($id) || $id <= 0) {
        die("Error: Invalid post ID!");
    }

    // Get current image from database
    $query = "SELECT image FROM posts WHERE id = ?";
    $statement = $conn->prepare($query);
    $statement->execute([$id]);
    $post = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("Error: No post found!");
    }

    $currentImage = $post['image'];

    // Keep old image if no new image is selected
    $imagePath = $currentImage;
    // Process new image if uploaded
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../uploads/";
        // Rename file to avoid duplication
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Check file format
        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = "uploads/" . $imageName;

                // Delete old photos if any
                if (!empty($currentImage) && file_exists("../" . $currentImage)) {
                    unlink("../" . $currentImage);
                }
            }
        }
    }

    // Update post in database
    $query = "UPDATE posts SET title = :title, content = :content, image = :image WHERE id = :id";
    $statement = $conn->prepare($query);
    $statement->execute([
        ':title' => $title,
        ':content' => $content,
        ':image' => $imagePath,
        ':id' => $id
    ]);

    header("Location: ../index.php");
    exit;
}
