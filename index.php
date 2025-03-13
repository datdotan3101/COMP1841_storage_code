<?php
session_start();
include 'includes/config.php';

// Check login
$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : null;
$username = $isLoggedIn ? $_SESSION['username'] : null;

$query = "SELECT posts.*, subjects.name AS subject_name, users.username 
          FROM posts 
          LEFT JOIN subjects ON posts.subject_id = subjects.id 
          LEFT JOIN users ON posts.user_id = users.id
          ORDER BY posts.created_at DESC";
$statement = $conn->prepare($query);
$statement->execute();
$posts = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($posts as &$post) {
    // Fetch comments
    $commentQuery = "SELECT comments.comment, users.username 
                     FROM comments 
                     JOIN users ON comments.user_id = users.id 
                     WHERE post_id = ? ORDER BY comments.created_at DESC";
    $commentStmt = $conn->prepare($commentQuery);
    $commentStmt->execute([$post['id']]);
    $post['comments'] = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>COMP-1841</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script defer src="assets/script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'includes/header.html.php'; ?>

    <div class="container mt-4 d-flex flex-column align-items-center text-center">
        <?php if ($isLoggedIn): ?>
            <a href="posts/create.html.php" class="btn btn-primary mb-3">Create question</a>
        <?php endif; ?>


        <?php if (!empty($posts)): ?>
            <ul class="list-group w-75">
                <?php foreach ($posts as &$post): ?>
                    <li class="list-group-item mb-3 p-4 rounded shadow-sm">
                        <h3 class="fw-bold"><?= htmlspecialchars($post['title']) ?></h3>
                        <p class="text-muted"><strong>Author:</strong> <?= htmlspecialchars($post['username'] ?? 'Anonymous') ?></p>

                        <p><strong>Modules:</strong>
                            <?= !empty($post['subject_name']) ? htmlspecialchars($post['subject_name']) : '<span class="text-danger">No subjects yet</span>' ?>
                        </p>

                        <p class="text-justify"><?= nl2br(htmlspecialchars($post['content'])) ?></p>

                        <?php if (!empty($post['image']) && file_exists($post['image'])): ?>
                            <img src="<?= htmlspecialchars($post['image']) ?>" alt="Images" class="img-fluid rounded mb-3">
                        <?php else: ?>
                            <p class="text-warning">No Image</p>
                        <?php endif; ?>

                        <?php if ($isLoggedIn): ?>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="posts/edit.html.php?id=<?= $post['id'] ?>" class="btn btn-warning">Edit</a>
                                <button class="btn btn-danger delete-btn" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-post-id="<?= $post['id'] ?>">
                                    Delete
                                </button>
                            </div>
                        <?php endif; ?>
                        <div class="comments-section mt-4">
                            <h4>💬 Comment</h4>
                            <ul class="list-group">
                                <?php foreach ($post['comments'] as $comment): ?>
                                    <li class="list-group-item"><strong><?= htmlspecialchars($comment['username']) ?>:</strong> <?= htmlspecialchars($comment['comment']) ?></li>
                                <?php endforeach; ?>
                            </ul>

                            <?php if ($isLoggedIn): ?>
                                <form action="posts/comment.php" method="POST" class="mt-3">
                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                    <textarea name="comment" class="form-control mb-2" placeholder="Write comment..." required></textarea>
                                    <button type="submit" class="btn btn-success">Post</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php unset($post); ?>
            </ul>
        <?php else: ?>
            <p class="alert alert-info">No post</p>
        <?php endif; ?>

    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this post? This action cannot be undo!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var deleteModal = document.getElementById("confirmDeleteModal");
            deleteModal.addEventListener("show.bs.modal", function(event) {
                var button = event.relatedTarget;
                var postId = button.getAttribute("data-post-id");
                var deleteUrl = "posts/delete.php?id=" + postId;
                document.getElementById("confirmDeleteBtn").setAttribute("href", deleteUrl);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>