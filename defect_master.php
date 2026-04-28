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
        $sql = 'UPDATE defect_master SET 
                deleted_by = :deleted_by,
                deleted_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)
                WHERE id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':deleted_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Defect deleted successfully!'); 
                window.location.href='defect_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting defect: ".$e->getMessage()."');</script>";
    }
}
if (isset($_POST['add_defect'])) {
    $defect = strtoupper($_POST['add_defect']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'INSERT INTO defect_master (defect, created_by, created_at) 
                VALUES (:defect, :created_by, DATEADD(HOUR, 8, CURRENT_TIMESTAMP))';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':defect', $defect, PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('New defect added successfully!'); 
                window.location.href='defect_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error adding defect: ".$e->getMessage()."');</script>";
    }
}

if (isset($_POST['update_defect'])) {
    $id = $_POST['edit_id'];
    $defect = strtoupper($_POST['edit_defect']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'UPDATE defect_master SET 
                defect = :defect,
                last_modified_by = :last_modified_by,
                last_modified_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)
                WHERE id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':defect', $defect, PDO::PARAM_STR);
        $stmt->bindParam(':last_modified_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Defect updated successfully!'); 
                window.location.href='defect_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error updating defect: ".$e->getMessage()."');</script>";
    }
}

try {
    $sql =
        'SELECT * FROM defect_master WHERE deleted_at IS NULL';
    $stmt = $conn->query($sql);
    $defects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Error fetching defect: ".$e->getMessage()."');</script>";
    $defects = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/defect_master.css">
</head>

<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Defect List</h2>
            <button class="btn btn-add" onclick="openAddModal()">ADD</button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Defect</th>
                        <th>Last Modified By</th>
                        <th>Last Modified At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($defects as $defects) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($defects['defect']); ?></td>
                            <td><?php echo htmlspecialchars($defects['last_modified_by'] ?? 'N/A'); ?></td>
                            <td><?php if (isset($defects['last_modified_at'])) {
                                echo htmlspecialchars(date('m-d-Y h:i:s A', strtotime($defects['last_modified_at'])));
                            } else {
                                echo 'N/A';
                            } ?></td>

                            <td>
                                <button class="btn btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($defects)); ?>)">EDIT</button>
                                <button class="btn btn-delete" onclick="confirmDelete(<?php echo htmlspecialchars($defects['id']); ?>)">DELETE</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h3>Add New Defect</h3>
                <form method="POST">
                    <label>Defect:</label>
                    <input type="text" id="add_defect" name="add_defect" required autocomplete="off">

                    <button type="submit" name="add_defect" class="btn btn-add">Add</button>
                </form>
            </div>
        </div>
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h3>Edit Defect</h3>
                <form method="POST">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <label>Defect:</label>
                    <input type="text" id="edit_defect" name="edit_defect" required autocomplete="off">
                    <button type="submit" name="update_defect" class="btn btn-edit">Update</button>
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

        function openEditModal(defects) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('edit_id').value = defects.id;
            document.getElementById('edit_defect').value = defects.defect;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this defect?')) {
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