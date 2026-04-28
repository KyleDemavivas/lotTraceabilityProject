<?php
include 'sidebar.php';
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

if (!isset($_SESSION['user_namefl'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit;
}
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'UPDATE stencil_master SET deleted_by = :deleted_by, deleted_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP) WHERE id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':deleted_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Stencil deleted successfully!'); 
                window.location.href='stencil_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting stencil: ".$e->getMessage()."');</script>";
    }
}
if (isset($_POST['add_stencil'])) {
    $stencil_no = strtoupper($_POST['add_stencil_no']);
    $total_stroke = strtoupper($_POST['add_total_stroke']);
    $stencil_status = strtoupper($_POST['add_stencil_status']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'INSERT INTO stencil_master (stencil_no, total_stroke, stencil_status, created_by, created_at) 
                VALUES (:stencil_no, :total_stroke, :stencil_status, :created_by, DATEADD(HOUR, 8, CURRENT_TIMESTAMP))';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':stencil_no', $stencil_no, PDO::PARAM_STR);
        $stmt->bindParam(':total_stroke', $total_stroke, PDO::PARAM_STR);
        $stmt->bindParam(':stencil_status', $stencil_status, PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('New stencil added successfully!'); 
                window.location.href='stencil_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error adding stencil: ".$e->getMessage()."');</script>";
    }
}

if (isset($_POST['update_stencil'])) {
    $id = $_POST['edit_id'];
    $stencil_no = strtoupper($_POST['edit_stencil_no']);
    $total_stroke = strtoupper($_POST['edit_total_stroke']);
    $stencil_status = strtoupper($_POST['edit_stencil_status']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'UPDATE stencil_master SET stencil_no = :stencil_no, total_stroke = :total_stroke, stencil_status = :stencil_status, last_modified_by = :last_modified_by,last_modified_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)WHERE id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':stencil_no', $stencil_no, PDO::PARAM_STR);
        $stmt->bindParam(':total_stroke', $total_stroke, PDO::PARAM_STR);
        $stmt->bindParam(':stencil_status', $stencil_status, PDO::PARAM_STR);
        $stmt->bindParam(':last_modified_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Stencil updated successfully!'); 
                window.location.href='stencil_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error updating stencil: ".$e->getMessage()."');</script>";
    }
}

try {
    $sql =
        'SELECT * FROM stencil_master WHERE deleted_at IS NULL';
    $stmt = $conn->query($sql);
    $stencils = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Error fetching stencils: ".$e->getMessage()."');</script>";
    $stencils = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stencil_master.css">
</head>

<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Stencil Data List</h2>
            <button class="btn btn-add" onclick="openAddModal()">ADD</button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Stencil No.</th>
                        <th>Total Number of Stroke</th>
                        <th>Stencil Status</th>
                        <th>Last Modified By</th>
                        <th>Last Modified At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stencils as $stencil) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($stencil['stencil_no']); ?></td>
                            <td><?php echo htmlspecialchars($stencil['total_stroke']); ?></td>
                            <td><?php echo htmlspecialchars($stencil['stencil_status']); ?></td>
                            <td><?php echo htmlspecialchars($stencil['last_modified_by'] ?? 'N/A'); ?></td>
                            <td><?php if (isset($stencil['last_modified_at'])) {
                                echo htmlspecialchars(date('M-d-Y h:i A', strtotime($stencil['last_modified_at'])));
                            } else {
                                echo 'N/A';
                            } ?></td>

                            <td>
                                <button class="btn btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($stencil)); ?>)">EDIT</button>
                                <button class="btn btn-delete" onclick="confirmDelete(<?php echo htmlspecialchars($stencil['id']); ?>)">DELETE</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h3>Add New Stencil</h3>
                <form method="POST">
                    <label>Stencil No.:</label>
                    <input type="text" id="add_stencil_no" name="add_stencil_no" required autocomplete="off">

                    <label>Total No. of Stroke:</label>
                    <input type="text" id="add_total_stroke" name="add_total_stroke" required autocomplete="off">

                    <label>Stencil Status:</label>
                    <input type="text" id="add_stencil_status" name="add_stencil_status" required autocomplete="off">

                    <button type="submit" name="add_stencil" class="btn btn-add">Add</button>
                </form>
            </div>
        </div>
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h3>Edit Stencil</h3>
                <form method="POST">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <label>Stencil No.:</label>
                    <input type="text" id="edit_stencil_no" name="edit_stencil_no" required autocomplete="off">
                    <label>Total No. of Stroke:</label>
                    <input type="text" id="edit_total_stroke" name="edit_total_stroke" required autocomplete="off">
                    <label>Stencil Status:</label>
                    <input type="text" id="edit_stencil_status" name="edit_stencil_status" required autocomplete="off">
                    <button type="submit" name="update_stencil" class="btn btn-edit">Update</button>
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

        function openEditModal(stencils) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('edit_id').value = stencils.id;
            document.getElementById('edit_stencil_no').value = stencils.stencil_no;
            document.getElementById('edit_total_stroke').value = stencils.total_stroke;
            document.getElementById('edit_stencil_status').value = stencils.stencil_status;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this stencil?')) {
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