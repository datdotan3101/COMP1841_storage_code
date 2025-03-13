<?php
require __DIR__ . '/../includes/config.php';

// Check if there is a course ID in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid course ID.");
}

$course_id = $_GET['id'];

// Get subject information from database
try {
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE id = :id");
    $stmt->bindParam(':id', $course_id, PDO::PARAM_INT);
    $stmt->execute();
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        die("The subject does not exist..");
    }
} catch (PDOException $e) {
    die("Query error: " . $e->getMessage());
}

// Process when form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_name = trim($_POST['name']);

    if (empty($new_name)) {
        $error = "Module name cannot be left blank!";
    } else {
        try {
            $stmt = $conn->prepare("UPDATE subjects SET name = :name WHERE id = :id");
            $stmt->bindParam(':name', $new_name, PDO::PARAM_STR);
            $stmt->bindParam(':id', $course_id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: courses.php?updated=1");
            exit();
        } catch (PDOException $e) {
            $error = "Error updating module: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow p-4">
                    <h2 class="text-center text-primary mb-4">Update Module</h2>

                    <?php if (isset($error)) : ?>
                        <div class="alert alert-danger text-center">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Module Name:</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($course['name']); ?>" class="form-control" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Save</button>
                            <a href="courses.php" class="btn btn-secondary ms-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>