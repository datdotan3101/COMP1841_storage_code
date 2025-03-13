<?php
include '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imagePath = NULL; 

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/"; // Photo storage folder
        $imageName = time() . "_" . basename($_FILES['image']['name']); 
        $targetFile = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($imageFileType, $validExtensions)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $imagePath = $targetFile;
            }
        }
    }

    $query = "INSERT INTO posts (title, content, image) VALUES (:title, :content, :image)";
    $statement = $conn->prepare($query);
    $statement->execute([
        ':title' => $title,
        ':content' => $content,
        ':image' => $imagePath 
    ]);

    header("Location: ../index.php");
    exit;
}
?>
