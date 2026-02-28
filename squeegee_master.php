<?php
include 'sidebar.php';
include 'db_connect.php';

if (!isset($_SESSION['user_namefl'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit();
}
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = "UPDATE squeegee_master SET 
                deleted_by = :deleted_by,
                deleted_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':deleted_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Squeegee deleted successfully!'); 
                window.location.href='squeegee_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting squeegee: " . $e->getMessage() . "');</script>";
    }
}
if (isset($_POST['add_squeegee'])) {
    $squeegee_no = strtoupper($_POST['add_squeegee_no']);
    $squeegeetotal_stroke = strtoupper($_POST['add_squeegeetotal_stroke']);
    $squeegee_status = strtoupper($_POST['add_squeegee_status']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = "INSERT INTO squeegee_master (squeegee_no, squeegeetotal_stroke, squeegee_status, created_by, created_at) 
                VALUES (:squeegee_no, :squeegeetotal_stroke, :squeegee_status, :created_by, DATEADD(HOUR, 8, CURRENT_TIMESTAMP))";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':squeegee_no', $squeegee_no, PDO::PARAM_STR);
        $stmt->bindParam(':squeegeetotal_stroke', $squeegeetotal_stroke, PDO::PARAM_STR);
        $stmt->bindParam(':squeegee_status', $squeegee_status, PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('New Squeegee added successfully!'); 
                window.location.href='squeegee_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error adding Squeegee: " . $e->getMessage() . "');</script>";
    }
}

if (isset($_POST['update_squeegee'])) {
    $id = $_POST['edit_id'];
    $squeegee_no = strtoupper($_POST['edit_squeegee_no']);
    $squeegeetotal_stroke = strtoupper($_POST['edit_squeegeetotal_stroke']);
    $squeegee_status = strtoupper($_POST['edit_squeegee_status']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = "UPDATE squeegee_master SET 
                squeegee_no = :squeegee_no,
                squeegeetotal_stroke = :squeegeetotal_stroke,
                squeegee_status = :squeegee_status,
                last_modified_by = :last_modified_by,
                last_modified_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':squeegee_no', $squeegee_no, PDO::PARAM_STR);
        $stmt->bindParam(':squeegeetotal_stroke', $squeegeetotal_stroke, PDO::PARAM_STR);
        $stmt->bindParam(':squeegee_status', $squeegee_status, PDO::PARAM_STR);
        $stmt->bindParam(':last_modified_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('squeegee updated successfully!'); 
                window.location.href='squeegee_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error updating squeegee: " . $e->getMessage() . "');</script>";
    }
}

try {
    $sql =
        "SELECT * FROM squeegee_master WHERE deleted_at IS NULL";
    $stmt = $conn->query($sql);
    $squeegees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Error fetching squeegees: " . $e->getMessage() . "');</script>";
    $squeegees = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/squeegee_master.css">
</head>

<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Squeegee Data List</h2>
            <button class="btn btn-add" onclick="openAddModal()">ADD</button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Squeegee No.</th>
                        <th>Total Number of Stroke</th>
                        <th>Squeegee Status</th>
                        <th>Last Modified By</th>
                        <th>Last Modified At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($squeegees as $squeegees): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($squeegees['squeegee_no']); ?></td>
                            <td><?php echo htmlspecialchars($squeegees['squeegeetotal_stroke']); ?></td>
                            <td><?php echo htmlspecialchars($squeegees['squeegee_status']); ?></td>
                            <td><?php echo htmlspecialchars($squeegees['last_modified_by'] ?? 'N/A'); ?></td>
                            <td><?php if (isset($squeegees['last_modified_at'])) {
                                    echo htmlspecialchars(date('M-d-Y h:i A', strtotime($squeegees['last_modified_at'])));
                                } else {
                                    echo 'N/A';
                                } ?></td>

                            <td>
                                <button class="btn btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($squeegees)); ?>)">EDIT</button>
                                <button class="btn btn-delete" onclick="confirmDelete(<?php echo htmlspecialchars($squeegees['id']); ?>)">DELETE</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h3>Add New squeegee</h3>
                <form method="POST">
                    <label>Squeegee No.:</label>
                    <input type="text" id="add_squeegee_no" name="add_squeegee_no" required autocomplete="off">

                    <label>Total No. of Stroke:</label>
                    <input type="text" id="add_squeegeetotal_stroke" name="add_squeegeetotal_stroke" required autocomplete="off">

                    <label>Squeegee Status:</label>
                    <input type="text" id="add_squeegee_status" name="add_squeegee_status" required autocomplete="off">

                    <button type="submit" name="add_squeegee" class="btn btn-add">Add</button>
                </form>
            </div>
        </div>
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h3>Edit squeegee</h3>
                <form method="POST">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <label>Squeegee No.:</label>
                    <input type="text" id="edit_squeegee_no" name="edit_squeegee_no" required autocomplete="off">
                    <label>Total No. of Stroke:</label>
                    <input type="text" id="edit_squeegeetotal_stroke" name="edit_squeegeetotal_stroke" required autocomplete="off">
                    <label>Squeegee Status:</label>
                    <input type="text" id="edit_squeegee_status" name="edit_squeegee_status" required autocomplete="off">
                    <button type="submit" name="update_squeegee" class="btn btn-edit">Update</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal(squeegees) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('edit_id').value = squeegees.id;
            document.getElementById('edit_squeegee_no').value = squeegees.squeegee_no;
            document.getElementById('edit_squeegeetotal_stroke').value = squeegees.squeegeetotal_stroke;
            document.getElementById('edit_squeegee_status').value = squeegees.squeegee_status;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this squeegee?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'delete_id';
                input.value = id;
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll("input[type='text']").forEach(function(input) {
                input.addEventListener("input", function() {
                    this.value = this.value.toUpperCase();
                });
            });
        });

        window.onclick = function(event) {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            if (event.target == addModal) {
                addModal.style.display = "none";
            }
            if (event.target == editModal) {
                editModal.style.display = "none";
            }
        }
    </script>
</body>

</html>