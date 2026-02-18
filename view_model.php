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
        $sql = "UPDATE model_data SET 
                deleted_by = :deleted_by,
                deleted_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':deleted_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Model deleted successfully!'); 
                window.location.href='view_model.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting model: " . $e->getMessage() . "');</script>";
    }
}

if (isset($_POST['update_model'])) {
    $id = $_POST['edit_id'];
    $assy_code = strtoupper($_POST['edit_assy_code']);
    $model_name = strtoupper($_POST['edit_model_name']);
    $letter_allocation = strtoupper($_POST['edit_letter_allocation']);
    $serial_qty = $_POST['edit_serial_qty'];
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = "UPDATE model_data SET 
                assy_code = :assy_code,
                model_name = :model_name,
                letter_allocation = :letter_allocation,
                serial_qty = :serial_qty,
                last_modified_by = :last_modified_by,
                last_modified_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)
                WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':assy_code', $assy_code, PDO::PARAM_STR);
        $stmt->bindParam(':model_name', $model_name, PDO::PARAM_STR);
        $stmt->bindParam(':letter_allocation', $letter_allocation, PDO::PARAM_STR);
        $stmt->bindParam(':serial_qty', $serial_qty, PDO::PARAM_STR);
        $stmt->bindParam(':last_modified_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Model updated successfully!'); 
                window.location.href='view_model.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error updating model: " . $e->getMessage() . "');</script>";
    }
}

try {
    $sql = "SELECT * FROM model_data WHERE deleted_at IS NULL";
    $stmt = $conn->query($sql);
    $models = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Error fetching models: " . $e->getMessage() . "');</script>";
    $models = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Model Data Management</title>
    <link rel="stylesheet" href="css/view_model.css">
</head>

<body>
    <div class="container">
        <h2>Model Data List</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Assy Code</th>
                        <th>Model Name</th>
                        <th>Letter Allocation</th>
                        <th>Serial Quantity</th>
                        <th>Last Modified By</th>
                        <th>Last Modified At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($models as $model): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($model['assy_code']); ?></td>
                            <td><?php echo htmlspecialchars($model['model_name']); ?></td>
                            <td><?php echo htmlspecialchars($model['letter_allocation']); ?></td>
                            <td><?php echo htmlspecialchars($model['serial_qty']); ?></td>
                            <td><?php echo htmlspecialchars($model['last_modified_by'] ?? 'N/A'); ?></td>
                            <td><?php if (isset($model['last_modified_at'])) {
                                    echo htmlspecialchars(date('m-d-Y h:i:s A', strtotime($model['last_modified_at'])));
                                } else {
                                    echo 'N/A';
                                } ?></td>

                            <td>
                                <button class="btn btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($model)); ?>)">EDIT</button>
                                <button class="btn btn-delete" onclick="confirmDelete(<?php echo htmlspecialchars($model['id']); ?>)">DELETE</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h3>Edit Model</h3>
                <form method="POST">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <label>Assy Code:</label>
                    <input type="text" id="edit_assy_code" name="edit_assy_code" required autocomplete="off">
                    <label>Model Name:</label>
                    <input type="text" id="edit_model_name" name="edit_model_name" required autocomplete="off">
                    <label>Letter Allocation:</label>
                    <input type="text" id="edit_letter_allocation" name="edit_letter_allocation" required autocomplete="off">
                    <label>Serial Quantity:</label>
                    <input type="text" id="edit_serial_qty" name="edit_serial_qty" required autocomplete="off">
                    <button type="submit" name="update_model" class="btn btn-edit">Update</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(model) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('edit_id').value = model.id;
            document.getElementById('edit_assy_code').value = model.assy_code;
            document.getElementById('edit_model_name').value = model.model_name;
            document.getElementById('edit_letter_allocation').value = model.letter_allocation;
            document.getElementById('edit_serial_qty').value = model.serial_qty;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this model?')) {
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

        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>