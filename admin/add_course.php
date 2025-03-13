<?php
require __DIR__ . '/../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_name'])) {
    $course_name = trim($_POST['course_name']);

    if (!empty($course_name)) {
        $sql = "INSERT INTO subjects (name) VALUES (:name)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $course_name, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $newId = $conn->lastInsertId();
            echo json_encode(["success" => true, "id" => $newId, "name" => $course_name]);
            exit;
        }
    }
}

echo json_encode(["success" => false]);
