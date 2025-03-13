<?php
require __DIR__ . '/../includes/config.php'; 

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $course_id = $_GET['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM subjects WHERE id = :id");
        $stmt->bindParam(':id', $course_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: courses.php?deleted=1");
        exit();
    } catch (PDOException $e) {
        die("Error when deleting course: " . $e->getMessage());
    }
} else {
    die("Invalid course ID.");
}
