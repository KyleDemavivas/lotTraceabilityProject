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
        $sql = 'UPDATE solderpaste_master SET deleted_by = :deleted_by, deleted_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP)
                WHERE id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':deleted_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Solder Paste deleted successfully!'); 
                window.location.href='ad_solderpaste.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting solder paste: ".$e->getMessage()."');</script>";
    }
}
if (isset($_POST['add_stencil'])) {
    $solder_paste = strtoupper($_POST['add_solder_paste']);
    $serial_paste = strtoupper($_POST['add_serial_paste']);
    $part_lot = strtoupper($_POST['add_part_lot']);
    $time_pulledout = strtoupper($_POST['add_time_pulledout']);
    $time_use = strtoupper($_POST['add_time_use']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'INSERT INTO solderpaste_master (solder_paste, serial_paste, part_lot, time_pulledout, time_use, created_by, created_at) 
                VALUES (:solder_paste, :serial_paste, :part_lot, :time_pulledout, :time_use,:created_by, DATEADD(HOUR, 8, CURRENT_TIMESTAMP))';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':solder_paste', $solder_paste, PDO::PARAM_STR);
        $stmt->bindParam(':serial_paste', $serial_paste, PDO::PARAM_STR);
        $stmt->bindParam(':part_lot', $part_lot, PDO::PARAM_STR);
        $stmt->bindParam(':time_pulledout', $time_pulledout, PDO::PARAM_STR);
        $stmt->bindParam(':time_use', $time_use, PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('New solder paste added successfully!'); 
                window.location.href='ad_solderpaste.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error adding solder paste: ".$e->getMessage()."');</script>";
    }
}

if (isset($_POST['update_solderpaste'])) {
    $id = $_POST['edit_id'];
    $solder_paste = strtoupper($_POST['edit_solder_paste']);
    $serial_paste = strtoupper($_POST['edit_serial_paste']);
    $part_lot = strtoupper($_POST['edit_part_lot']);
    $time_pulledout = strtoupper($_POST['edit_time_pulledout']);
    $time_use = strtoupper($_POST['edit_time_use']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = 'UPDATE solderpaste_master SET solder_paste = :solder_paste, serial_paste = :serial_paste, part_lot = :part_lot, time_pulledout = :time_pulledout, time_use = :time_use, last_modified_by = :last_modified_by, last_modified_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP) WHERE id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':solder_paste', $solder_paste, PDO::PARAM_STR);
        $stmt->bindParam(':serial_paste', $serial_paste, PDO::PARAM_STR);
        $stmt->bindParam(':part_lot', $part_lot, PDO::PARAM_STR);
        $stmt->bindParam(':time_pulledout', $time_pulledout, PDO::PARAM_STR);
        $stmt->bindParam(':time_use', $time_use, PDO::PARAM_STR);
        $stmt->bindParam(':last_modified_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Solder Paste updated successfully!'); 
                window.location.href='ad_solderpaste.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error updating solder paste: ".$e->getMessage()."');</script>";
    }
}

try {
    $sql =
        'SELECT * FROM solderpaste_master WHERE deleted_at IS NULL';
    $stmt = $conn->query($sql);
    $solderpaste = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Error fetching solder paste: ".$e->getMessage()."');</script>";
    $solderpaste = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/solderpaste_master.css">
</head>

<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Solder Paste</h2>
            <button class="btn btn-add" onclick="openAddModal()">PULL OUT SOLDER PASTE</button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Solder Paste</th>
                        <th>Serial Code</th>
                        <th>Part Lot</th>
                        <th>Time Pulled Out</th>
                        <th>Time Use</th>
                        <th>Performed By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($solderpaste as $solderpaste) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($solderpaste['solder_paste']); ?></td>
                            <td><?php echo htmlspecialchars($solderpaste['serial_paste']); ?></td>
                            <td><?php echo htmlspecialchars($solderpaste['part_lot']); ?></td>
                            <td><?php echo htmlspecialchars($solderpaste['time_pulledout']); ?></td>
                            <td><?php echo htmlspecialchars($solderpaste['time_use']); ?></td>
                            <td><?php echo htmlspecialchars($solderpaste['created_by']); ?></td>
                            <td>
                                <button class="btn btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($solderpaste)); ?>)">EDIT</button>
                                <button class="btn btn-delete" onclick="confirmDelete(<?php echo htmlspecialchars($solderpaste['id']); ?>)">DELETE</button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h3>Pull Out Solder Paste</h3>
                <form method="POST">
                    <label>Solder Paste</label>
                    <input type="text" id="add_solder_paste" name="add_solder_paste" required autocomplete="off" value="M40 LS720" readonly>

                    <label>Serial Code</label>
                    <input type="text" id="add_serial_paste" name="add_serial_paste" required autocomplete="off" autofocus>

                    <label>Part Lot</label>
                    <input type="text" id="add_part_lot" name="add_part_lot" required autocomplete="off">

                    <label>Time Pulled Out</label>
                    <input type="text" id="add_time_pulledout" name="add_time_pulledout" required autocomplete="off" readonly>

                    <label>Time Use</label>
                    <input type="text" id="add_time_use" name="add_time_use" required autocomplete="off" readonly>

                    <button type="submit" name="add_stencil" class="btn btn-add">Add</button>
                </form>
            </div>
        </div>
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h3>Edit Solder Paste</h3>
                <form method="POST">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <label>Solder Paste</label>
                    <input type="text" id="edit_solder_paste" name="edit_solder_paste" required autocomplete="off">
                    <label>Serial Code</label>
                    <input type="text" id="edit_serial_paste" name="edit_serial_paste" required autocomplete="off">
                    <label>Part Lot</label>
                    <input type="text" id="edit_part_lot" name="edit_part_lot" required autocomplete="off">
                    <label>Time Pulled Out</label>
                    <input type="text" id="edit_time_pulledout" name="add_time_pulledout" required autocomplete="off">
                    <label>Time Use</label>
                    <input type="text" id="edit_time_use" name="edit_time_use" required autocomplete="off">
                    <button type="submit" name="update_solderpaste" class="btn btn-edit">Update</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';

            let now = new Date().toLocaleString("en-PH", {
                timeZone: "Asia/Manila"
            });
            now = new Date(now);

            let options = {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            };

            let formattedPulledOut = now.toLocaleTimeString('en-PH', options);
            document.getElementById('add_time_pulledout').value = formattedPulledOut;

            now.setHours(now.getHours() + 2);
            let formattedTimeUse = now.toLocaleTimeString('en-PH', options);
            document.getElementById('add_time_use').value = formattedTimeUse;
            document.getElementById('add_serial_paste').focus();


        }


        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal(solderpaste) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('edit_id').value = solderpaste.id;
            document.getElementById('edit_solder_paste').value = solderpaste.solder_paste;
            document.getElementById('edit_serial_paste').value = solderpaste.serial_paste;
            document.getElementById('edit_part_lot').value = solderpaste.part_lot;
            document.getElementById('edit_time_pulledout').value = solderpaste.time_pulledout;
            document.getElementById('edit_time_use').value = solderpaste.time_use;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this solder paste?')) {
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