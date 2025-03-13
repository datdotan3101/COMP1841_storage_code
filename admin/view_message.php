<?php
session_start();
include '../includes/config.php';

if (!isset($_GET['id'])) {
    header("Location: messages.php");
    exit();
}

$id = $_GET['id'];

// Get message data
$query = "SELECT * FROM contacts WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->execute([':id' => $id]);
$message = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$message) {
    $_SESSION['message'] = "Message not found!";
    $_SESSION['message_status'] = "error";
    header("Location: messages.php");
    exit();
}
// Process feedback when admin sends
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply'])) {
    $reply = trim($_POST['reply']);

    if (empty($reply)) {
        $_SESSION['message'] = "Feedback content cannot be blank!";
        $_SESSION['message_status'] = "error";
    } else {
        // Update response to database
        $updateQuery = "UPDATE contacts SET reply = :reply WHERE id = :id";
        $stmt = $conn->prepare($updateQuery);
        $stmt->execute([
            ':reply' => htmlspecialchars($reply),
            ':id' => $id
        ]);

        // Send feedback email to user
        $subject = "Reponse from Admin";
        $headers = "From: admin@example.com\r\n";
        $headers .= "Reply-To: admin@example.com\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        $emailMessage = "Hello" . htmlspecialchars($message['name']) . ",\n\n";
        $emailMessage .= "You sent a message to the admin and here is our response:\n\n";
        $emailMessage .= "---------------------------------\n";
        $emailMessage .= $reply . "\n";
        $emailMessage .= "---------------------------------\n\n";
        $emailMessage .= "Thank you for contacting us..\n";
        $emailMessage .= "Admin";

        mail($message['email'], $subject, $emailMessage, $headers);

        $_SESSION['message'] = "Feedback has been sent!";
        $_SESSION['message_status'] = "success";

        // Update data after sending response
        header("Location: view_message.php?id=" . $id);
        exit();
    }
}
?>
<?php
if (isset($_GET['id'])) {
    $messageId = $_GET['id'];
    // Update read status
    $updateQuery = "UPDATE contacts SET is_read = 1 WHERE id = :id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->execute([':id' => $messageId]);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Message</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">
    <h2 class="mb-4">Message</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_status'] == 'success' ? 'success' : 'danger' ?>">
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']);
        unset($_SESSION['message_status']); ?>
    <?php endif; ?>

    <div class="card p-4 shadow-sm">
        <p><strong>Fullname:</strong> <?= htmlspecialchars($message['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($message['email']) ?></p>
        <p><strong>Message:</strong> <?= nl2br(htmlspecialchars($message['message'])) ?></p>
        <p><strong>Sent:</strong> <?= $message['created_at'] ?></p>

        <?php if (!empty($message['reply'])): ?>
            <div class="alert alert-info mt-3">
                <strong>Admin responded:</strong><br>
                <?= nl2br(htmlspecialchars($message['reply'])) ?>
            </div>
        <?php else: ?>
            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label class="form-label">Message:</label>
                    <textarea name="reply" class="form-control" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Send</button>
            </form>
        <?php endif; ?>
    </div>

    <a href="messages.php" class="btn btn-secondary mt-3">Bakc to dashboard</a>
</body>

</html>