<?php
session_start();

// Check if not logged in, redirect to login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: auth/login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
?>

<?php
include '../includes/config.php';

// Count the number of unread messages
$query = "SELECT COUNT(*) AS unread_count FROM contacts WHERE is_read = 0";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$unreadCount = $result['unread_count'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm p-4">
            <h2 class="text-center">Hello, <?= $username ?>👋</h2>
            <p class="text-center text-muted">Welcome to the Admin Dashboard</p>

            <div class="d-flex justify-content-center gap-3 mt-3">
                <a href="users.php" class="btn btn-primary px-4 py-2 fw-bold">User Management</a>
                <a href="courses.php" class="btn btn-warning px-4 py-2 fw-bold text-dark">Module Management</a>
                <a href="../admin/messages.php" class="btn btn-primary position-relative">
                    Mailbox
                    <?php if ($unreadCount > 0): ?>
                        <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">
                            <?= $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </a>
                <a href="auth/logout.php" class="btn btn-danger px-4 py-2 fw-bold">Logout</a>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>