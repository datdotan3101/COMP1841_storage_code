<?php
require __DIR__ . '/../includes/config.php';

$sql = "SELECT * FROM subjects";
$stmt = $conn->prepare($sql);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: []; // Nếu không có dữ liệu, trả về mảng rỗng
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Management</title>
    <link rel="stylesheet" href="../styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">
    <h2 class="text-center mb-4">Module</h2>

    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModuleModal">Add Modules</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </div>

    <table class="table table-bordered table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Module</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="moduleList">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td>
                            <a href="edit_course.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $row['id'] ?>)">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">No modules found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Modal Add Module -->
    <div class="modal fade" id="addModuleModal" tabindex="-1" aria-labelledby="addModuleLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModuleLabel">Add New Module</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addModuleForm">
                        <div class="mb-3">
                            <label for="moduleName" class="form-label">Module Name:</label>
                            <input type="text" id="moduleName" name="course_name" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirm Delete -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLabel">Confirm delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this module?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a id="confirmDeleteBtn" href="#" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // AJAX thêm module mới
        document.getElementById("addModuleForm").addEventListener("submit", function(event) {
            event.preventDefault();
            let formData = new FormData(this);

            fetch("add_course.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        let newRow = `<tr>
                        <td>${data.id}</td>
                        <td>${data.name}</td>
                        <td>
                            <a href="edit_course.php?id=${data.id}" class="btn btn-warning btn-sm">Edit</a>
                            <a href="#" class="btn btn-danger btn-sm" onclick="confirmDelete(${data.id})">Delete</a>
                        </td>
                    </tr>`;
                        document.getElementById("moduleList").innerHTML += newRow;
                        document.getElementById("addModuleForm").reset();
                        let addModal = bootstrap.Modal.getInstance(document.getElementById("addModuleModal"));
                        addModal.hide();
                    } else {
                        alert("Error adding module!");
                    }
                })
                .catch(error => console.error("Error:", error));
        });

        // Xác nhận xóa module
        function confirmDelete(courseId) {
            document.getElementById("confirmDeleteBtn").href = "delete_course.php?id=" + courseId;
            let deleteModal = new bootstrap.Modal(document.getElementById("confirmDeleteModal"));
            deleteModal.show();
        }
    </script>
</body>

</html>