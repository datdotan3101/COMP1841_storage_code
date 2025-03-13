<?php
session_start();
include '../includes/config.php';

$query = "SELECT * FROM contacts ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin - mailbox</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h2 class="mb-4">Contact mailbox</h2>

    <?php if (isset($_SESSION['message_status'])): ?>
        <div class="alert <?= $_SESSION['message_status'] === 'success' ? 'alert-success' : 'alert-danger' ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message_status'], $_SESSION['message']); ?>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Content</th>
                <th>Sent time</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $msg): ?>
                <tr>
                    <td><?= $msg['id'] ?></td>
                    <td><?= htmlspecialchars($msg['name']) ?></td>
                    <td><?= htmlspecialchars($msg['email']) ?></td>
                    <td><?= htmlspecialchars(substr($msg['message'], 0, 50)) ?>...</td>
                    <td><?= $msg['created_at'] ?></td>
                    <td>
                        <a href="view_message.php?id=<?= $msg['id'] ?>" class="btn btn-primary btn-sm">Seen message</a>
                        <a href="delete_message.php?id=<?= $msg['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <a href="index.php" class="btn btn-secondary">Back to dashboard</a>
</body>

</html>