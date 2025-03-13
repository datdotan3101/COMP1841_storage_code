<?php
session_start();
include __DIR__ . '/../../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: posts.php");
    exit();
}

$id = $_GET['id'];

$sql = "SELECT posts.title, posts.content, posts.created_at, users.username 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        WHERE posts.id = :id";

$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header("Location: posts.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?></title>
</head>

<body>
    <h1><?= htmlspecialchars($post['title']) ?></h1>
    <p><strong>Author:</strong> <?= htmlspecialchars($post['username']) ?></p>
    <p><strong>Post date:</strong> <?= htmlspecialchars($post['created_at']) ?></p>
    <hr>
    <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
    <br>
    <a href="posts.php">Back</a>
</body>

</html>