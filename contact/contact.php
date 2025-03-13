<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact to admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="container mt-5">
    <h2 class="mb-4">Contact to Admin</h2>

    <?php if (isset($_SESSION['contact_message'])): ?>
        <div class="alert <?= $_SESSION['contact_status'] === 'success' ? 'alert-success' : 'alert-danger' ?>" role="alert">
            <?= htmlspecialchars($_SESSION['contact_message']) ?>
        </div>
        <?php unset($_SESSION['contact_message'], $_SESSION['contact_status']); ?>
    <?php endif; ?>

    <form method="POST" action="../actions/process_contact.php" class="card p-4 shadow-sm">
        <div class="mb-3">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="Your name" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
        </div>

        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea name="message" id="message" class="form-control" placeholder="Message" required rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Send</button>
    </form>

    <p class="mt-3">
        <a href="../index.php" class="btn btn-secondary">Back</a>
    </p>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>


</html>