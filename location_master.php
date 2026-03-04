<?php
include 'sidebar.php';
include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

if (!isset($_SESSION['user_namefl'])) {
    echo "<script>alert('Please login first!'); window.location.href='login.php';</script>";
    exit;
}
if (isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'UPDATE location_master SET 
                deleted_by = :deleted_by,
                deleted_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)
                WHERE id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':deleted_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Location deleted successfully!'); 
                window.location.href='location_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting location: ".$e->getMessage()."');</script>";
    }
}
if (isset($_POST['add_location'])) {
    $location = strtoupper($_POST['add_location']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'INSERT INTO location_master (location, created_by, created_at) 
                VALUES (:location, :created_by, DATEADD(HOUR, 8, CURRENT_TIMESTAMP))';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('New location added successfully!'); 
                window.location.href='location_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error adding location: ".$e->getMessage()."');</script>";
    }
}

if (isset($_POST['update_location'])) {
    $id = $_POST['edit_id'];
    $location = strtoupper($_POST['edit_location']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'UPDATE location_master SET 
                location = :location,
                last_modified_by = :last_modified_by,
                last_modified_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)
                WHERE id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':last_modified_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('location updated successfully!'); 
                window.location.href='location_master.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error updating location: ".$e->getMessage()."');</script>";
    }
}

try {
    $sql =
        'SELECT * FROM location_master WHERE deleted_at IS NULL';
    $stmt = $conn->query($sql);
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Error fetching location: ".$e->getMessage()."');</script>";
    $locations = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/location_master.css">
</head>

<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Location List</h2>
            <button class="btn btn-add" onclick="openAddModal()">ADD</button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Location</th>
                        <th>Last Modified By</th>
                        <th>Last Modified At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($locations as $locations) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($locations['location']); ?></td>
                            <td><?php echo htmlspecialchars($locations['last_modified_by'] ?? 'N/A'); ?></td>
                            <td><?php if (isset($locations['last_modified_at'])) {
                                echo htmlspecialchars(date('m-d-Y h:i:s A', strtotime($locations['last_modified_at'])));
                            } else {
                                echo 'N/A';
                            } ?></td>

                            <td>
                                <button class="btn btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($locations)); ?>)">EDIT</button>
                                <button class="btn btn-delete" onclick="confirmDelete(<?php echo htmlspecialchars($locations['id']); ?>)">DELETE</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h3>Add New Location</h3>
                <form method="POST">
                    <label>Location:</label>
                    <input type="text" id="add_location" name="add_location" required autocomplete="off">

                    <button type="submit" name="add_location" class="btn btn-add">Add</button>
                </form>
            </div>
        </div>
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h3>Edit Location</h3>
                <form method="POST">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <label>Location:</label>
                    <input type="text" id="edit_location" name="edit_location" required autocomplete="off">
                    <button type="submit" name="update_location" class="btn btn-edit">Update</button>
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

        function openEditModal(locations) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('edit_id').value = locations.id;
            document.getElementById('edit_location').value = locations.location;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this location?')) {
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