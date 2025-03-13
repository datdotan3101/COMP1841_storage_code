<?php
include '../includes/config.php';

// Get list of subjects from database
$query = "SELECT * FROM subjects";
$statement = $conn->prepare($query);
$statement->execute();
$subjects = $statement->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm p-4">
                    <h2 class="text-primary text-center mb-4">Post</h2>
                    <form action="../actions/store.php" method="post" enctype="multipart/form-data">

                        <!-- Title  -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Title:</label>
                            <input type="text" name="title" id="title" class="form-control" required placeholder="Enter article title...">
                        </div>

                        <!-- Content   -->
                        <div class="mb-3">
                            <label for="content" class="form-label fw-bold">Question:</label>
                            <textarea name="content" id="content" class="form-control" rows="6" required placeholder="Enter article content..."></textarea>
                        </div>

                        Modules:
                        <div class="mb-3">
                            <select name="subject_id" id="subject" class="form-select" required>
                                <option value="">-- Modules --</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= htmlspecialchars($subject['id']) ?>"><?= htmlspecialchars($subject['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label fw-bold">Images</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>


                        <div class="d-flex justify-content-center gap-3">
                            <button type="submit" class="btn btn-primary px-4">Post</button>
                            <a href="../index.php" class="btn btn-secondary px-4">Cancel</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</body>

</html>