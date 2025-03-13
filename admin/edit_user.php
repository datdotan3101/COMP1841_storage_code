<?php
session_start();
include '../includes/config.php';

// Initialize CSRF token if not already present
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check for valid user ID
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("⚠ User not found.");
}

$id = $_GET['id'];

// Query user information
$query = "SELECT id, username, email, role FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user not found
if (!$user) {
    die("⚠ User not found.");
}

// Process information updates
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("⚠ Invalid CSRF token!");
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    // Check for valid data
    if (empty($username) || empty($email) || empty($role)) {
        echo "<p style='color:red;'>⚠ Please enter complete information.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>⚠ Invalid email.</p>";
    } else {
        // Update user information
        $updateQuery = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        if ($updateStmt->execute([$username, $email, $role, $id])) {
            echo "<p style='color:green;'>Update successful!</p>";
            header("Location: users.php");
            exit();
        } else {
            echo "<p style='color:red;'>Update failed!</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Information</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card shadow-lg p-4" style="max-width: 500px; width: 100%;">
            <h2 class="mb-4 text-center text-primary">Edit Information</h2>

            <form action="" method="POST">
                <!-- CSRF Token -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Username:</label>
                    <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email:</label>
                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Role:</label>
                    <select class="form-select" name="role">
                        <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Update</button>
            </form>

            <p class="mt-3 text-center">
                <a href="users.php" class="btn btn-outline-secondary w-100">Back</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>