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
        $sql = "UPDATE bonding_master SET deleted_by = :deleted_by, deleted_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP) WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':deleted_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Bonding deleted successfully!'); 
                window.location.href='ad_bonding.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error deleting bonding: " . $e->getMessage() . "');</script>";
    }
}
if (isset($_POST['add_stencil'])) {
    $bonding = strtoupper($_POST['add_bonding']);
    $serial_bonding = strtoupper($_POST['add_serial_bonding']);
    $part_lot = strtoupper($_POST['add_part_lot']);
    $time_pulledout = strtoupper($_POST['add_time_pulledout']);
    $time_use = strtoupper($_POST['add_time_use']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = "INSERT INTO bonding_master (bonding, serial_bonding, part_lot, time_pulledout, time_use, created_by, created_at) 
                VALUES (:bonding, :serial_bonding, :part_lot, :time_pulledout, :time_use,:created_by, DATEADD(HOUR, 8, CURRENT_TIMESTAMP))";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':bonding', $bonding, PDO::PARAM_STR);
        $stmt->bindParam(':serial_bonding', $serial_bonding, PDO::PARAM_STR);
        $stmt->bindParam(':part_lot', $part_lot, PDO::PARAM_STR);
        $stmt->bindParam(':time_pulledout', $time_pulledout, PDO::PARAM_STR);
        $stmt->bindParam(':time_use', $time_use, PDO::PARAM_STR);
        $stmt->bindParam(':created_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('New bonding added successfully!'); 
                window.location.href='ad_bonding.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error adding bonding: " . $e->getMessage() . "');</script>";
    }
}

if (isset($_POST['update_bonding'])) {
    $id = $_POST['edit_id'];
    $bonding = strtoupper($_POST['edit_bonding']);
    $serial_bonding = strtoupper($_POST['edit_serial_bonding']);
    $part_lot = strtoupper($_POST['edit_part_lot']);
    $time_pulledout = strtoupper($_POST['edit_time_pulledout']);
    $time_use = strtoupper($_POST['edit_time_use']);
    $user_namefl = $_SESSION['user_namefl'];

    try {
        $sql = "UPDATE bonding_master SET bonding = :bonding, serial_bonding = :serial_bonding, part_lot = :part_lot, time_pulledout = :time_pulledout, time_use = :time_use, last_modified_by = :last_modified_by, last_modified_at = DATEADD(HOUR, 8, CURRENT_TIMESTAMP) WHERE id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':bonding', $bonding, PDO::PARAM_STR);
        $stmt->bindParam(':serial_bonding', $serial_bonding, PDO::PARAM_STR);
        $stmt->bindParam(':part_lot', $part_lot, PDO::PARAM_STR);
        $stmt->bindParam(':time_pulledout', $time_pulledout, PDO::PARAM_STR);
        $stmt->bindParam(':time_use', $time_use, PDO::PARAM_STR);
        $stmt->bindParam(':last_modified_by', $user_namefl, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "<script>
                alert('Bonding updated successfully!'); 
                window.location.href='ad_bonding.php';
            </script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Error updating Bonding: " . $e->getMessage() . "');</script>";
    }
}

try {
    $sql =
        "SELECT * FROM bonding_master WHERE deleted_at IS NULL";
    $stmt = $conn->query($sql);
    $bonding = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<script>alert('Error fetching Bonding: " . $e->getMessage() . "');</script>";
    $bonding = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bonding.css">
</head>

<body>
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Bonding</h2>
            <button class="btn btn-add" onclick="openAddModal()">PULL OUT BONDING</button>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Bonding</th>
                        <th>Serial Code</th>
                        <th>Part Lot</th>
                        <th>Time Pulled Out</th>
                        <th>Time Use</th>
                        <th>Performed By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bonding as $bonding): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($bonding['bonding']); ?></td>
                            <td><?php echo htmlspecialchars($bonding['serial_bonding']); ?></td>
                            <td><?php echo htmlspecialchars($bonding['part_lot']); ?></td>
                            <td><?php echo htmlspecialchars($bonding['time_pulledout']); ?></td>
                            <td><?php echo htmlspecialchars($bonding['time_use']); ?></td>
                            <td><?php echo htmlspecialchars($bonding['created_by']); ?></td>
                            <td>
                                <button class="btn btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($bonding)); ?>)">EDIT</button>
                                <button class="btn btn-delete" onclick="confirmDelete(<?php echo htmlspecialchars($bonding['id']); ?>)">DELETE</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div id="addModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeAddModal()">&times;</span>
                <h3>Pull Out Bonding</h3>
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Bonding</label>
                        <select class="form-input" name="add_bonding" required>
                            <option value="">Select Bonding</option>
                            <option value="SOMAR IR 130">SOMAR IR 130</option>
                            <option value="THREEBOND 2217H">THREEBOND 2217H</option>
                        </select>
                    </div>
                    <label>Serial Code</label>
                    <input type="text" id="add_serial_bonding" name="add_serial_bonding" required autocomplete="off">

                    <label>Part Lot</label>
                    <input type="text" id="add_part_lot" name="add_part_lot" required autocomplete="off">

                    <label>Time Pulled Out</label>
                    <input type="text" id="add_time_pulledout" name="add_time_pulledout" required autocomplete="off">

                    <label>Time Use</label>
                    <input type="text" id="add_time_use" name="add_time_use" required autocomplete="off">

                    <button type="submit" name="add_stencil" class="btn btn-add">Add</button>
                </form>
            </div>
        </div>
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <h3>Edit Bonding</h3>
                <form method="POST">
                    <input type="hidden" id="edit_id" name="edit_id">
                    <label>Bonding</label>
                    <input type="text" id="edit_bonding" name="edit_bonding" required autocomplete="off">
                    <label>Bonding</label>
                    <input type="text" id="edit_serial_bonding" name="edit_serial_bonding" required autocomplete="off">
                    <label>Part Lot</label>
                    <input type="text" id="edit_part_lot" name="edit_part_lot" required autocomplete="off">
                    <label>Time Pulled Out</label>
                    <input type="text" id="edit_time_pulledout" name="edit_time_pulledout" required autocomplete="off" readonly>
                    <label>Time Use</label>
                    <input type="text" id="edit_time_use" name="edit_time_use" required autocomplete="off" readonly>
                    <button type="submit" name="update_bonding" class="btn btn-edit">Update</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function openAddModal() {
            document.getElementById('addModal').style.display = 'flex';

            const form = document.querySelector("#addModal form");
            if (form) form.reset();

            document.getElementById('add_time_pulledout').value = "";
            document.getElementById('add_time_use').value = "";
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function openEditModal(bonding) {
            document.getElementById('editModal').style.display = 'flex';
            document.getElementById('edit_id').value = bonding.id;
            document.getElementById('edit_bonding').value = bonding.bonding;
            document.getElementById('edit_serial_bonding').value = bonding.serial_bonding;
            document.getElementById('edit_part_lot').value = bonding.part_lot;
            document.getElementById('edit_time_pulledout').value = bonding.time_pulledout;
            document.getElementById('edit_time_use').value = bonding.time_use;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this Bonding?')) {
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

            const bondingSelect = document.querySelector("select[name='add_bonding']");
            bondingSelect.addEventListener("change", function() {
                let selectedBonding = this.value;

                if (selectedBonding) {
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

                    if (selectedBonding === "SOMAR IR 130") {
                        now.setHours(now.getHours() + 6);
                    } else if (selectedBonding === "THREEBOND 2217H") {
                        now.setHours(now.getHours() + 1);
                    }

                    document.getElementById('add_time_use').value = now.toLocaleTimeString('en-PH', options);
                } else {

                    document.getElementById('add_time_pulledout').value = "";
                    document.getElementById('add_time_use').value = "";
                }
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