<?php
include 'sidebar.php';
include 'db_connect.php';

try {
    $sql = "SELECT * FROM repair_process_verify ORDER BY created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $nogood_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching No Good data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/repair_process.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <h2>Process Repair Verification</h2>
        <div class="table-container">
            <div style="text-align: right; margin-bottom: 10px;">
                <input type="text" id="searchSerialCode" placeholder="Search Serial Code..." style="padding: 5px; width: 250px;" minlength="13" maxlength="13">
            </div>
            <table style="font-size: 12px;">
                <thead>
                    <tr>
                        <th>QR Code</th>
                        <th>Serial Code</th>
                        <th>Assembly Code</th>
                        <th>KEPI Lot</th>
                        <th>Repaired By</th>
                        <th>Operator Name</th>
                        <th>Action Taken</th>
                        <th>LCR Reading</th>
                        <th>Parts Code</th>
                        <th>Parts Lot</th>
                        <th>Measurement</th>
                        <th>Batch Lot</th>
                        <th>Repairability</th>
                        <th>Shift</th>
                        <th>Defect</th>
                        <th>Location</th>
                        <th>Line</th>
                        <th>Process Location</th>
                        <th>Line Leader</th>
                        <th>Board Number</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($nogood_data) > 0): ?>
                        <?php foreach ($nogood_data as $row): ?>
                            <?php $currentDateTime = $row['created_at']; ?>
                            <tr>
                                <td><?= htmlspecialchars($row['qr_code']) ?></td>
                                <td><?= htmlspecialchars($row['serial_code']) ?></td>
                                <td><?= htmlspecialchars($row['assy_code']) ?></td>
                                <td><?= htmlspecialchars($row['kepi_lot']) ?></td>
                                <td><?= htmlspecialchars($row['repaired_by']) ?></td>
                                <td><?= htmlspecialchars($row['operator_name']) ?></td>
                                <td><?= htmlspecialchars($row['action_rp']) ?></td>
                                <td><?= htmlspecialchars($row['lcr_reading']) ?></td>
                                <td><?= htmlspecialchars($row['parts_code']) ?></td>
                                <td><?= htmlspecialchars($row['parts_lot']) ?></td>
                                <td><?= htmlspecialchars($row['unitmeasurement']) ?></td>
                                <td><?= htmlspecialchars($row['batchlot']) ?></td>
                                <td><?= htmlspecialchars($row['repairable']) ?></td>
                                <td><?= htmlspecialchars($row['shift']) ?></td>
                                <td><?= htmlspecialchars($row['defect']) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td><?= htmlspecialchars($row['line']) ?></td>
                                <td><?= htmlspecialchars($row['process_location']) ?></td>
                                <td><?= htmlspecialchars($row['verified_ll']) ?></td>
                                <td><?= htmlspecialchars($row['board_number']) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                                <td><button onclick='openModal(<?= json_encode($row) ?>)'>Verify</button></td>
                            </tr>
                            <tr id="noResultsRow" style="display: none; text-align: center;">
                                <td colspan="14">No records found</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="14">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div id="actionModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3 class="modal-header">Repair Action Form</h3>
            <form id="repairForm" class="form-container">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="qr_code" id="qr_code">
                <input type="hidden" name="model_name" id="model_name">
                <input type="hidden" name="assy_code" id="assy_code">
                <input type="hidden" name="kepi_lot" id="kepi_lot">
                <input type="hidden" name="shift" id="shift">
                <input type="hidden" name="line" id="line">
                <input type="hidden" name="board_number" id="board_number">
                <input type="hidden" name="created_at" id="created_at">

                <div class="form-group">
                    <label class="form-label">Serial Code</label>
                    <input class="form-input" type="text" name="serial_code" id="serial_code" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Defect</label>
                    <input class="form-input" type="text" name="defect" id="defect" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Operator Name</label>
                    <input class="form-input" type="text" name="operator_name" id="operator_name" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Location</label>
                    <input class="form-input" type="text" name="location" id="location" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Process Location</label>
                    <input class="form-input" type="text" name="process_location" id="process_location" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Repaired By</label>
                    <input class="form-input" type="text" name="repaired_by" id="repaired_by" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Action</label>
                    <input class="form-input" type="text" name="action_rp" id="action_rp" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">LCR Reading</label>
                    <input class="form-input" type="text" name="lcr_reading" id="lcr_reading" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Unit of Measurement</label>
                    <input class="form-input" type="text" name="unitmeasurement" id="unitmeasurement" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Batch Lot</label>
                    <input class="form-input" type="text" name="batchlot" id="batchlot" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Judgement</label>
                    <select class="form-input" name="judgement_pl" required autocomplete="off">
                        <option value="">Select here</option>
                        <option value="GOOD">GOOD</option>
                        <option value="NO GOOD">NO GOOD</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Line Leader</label>
                    <input class="form-input" type="text" name="verified_ll" id="verified_ll" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Verified by</label>
                    <input class="form-input" type="text" name="verified_pl" id="verified_pl" value=<?= htmlspecialchars($_SESSION['user_namefl']) ?> readonly>
                </div>

                <div class="modal-footer">
                    <button type="submit">Submit</button>
                    <button type="button" class="button-close" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });

        function openModal(data) {
            document.getElementById('id').value = data.id;
            document.getElementById('qr_code').value = data.qr_code;
            document.getElementById('model_name').value = data.model_name;
            document.getElementById('assy_code').value = data.assy_code;
            document.getElementById('kepi_lot').value = data.kepi_lot;
            document.getElementById('serial_code').value = data.serial_code;
            document.getElementById('defect').value = data.defect;
            document.getElementById('operator_name').value = data.operator_name;
            document.getElementById('location').value = data.location;
            document.getElementById('shift').value = data.shift;
            document.getElementById('line').value = data.line;
            document.getElementById('board_number').value = data.board_number;
            document.getElementById('repaired_by').value = data.repaired_by;
            document.getElementById('action_rp').value = data.action_rp;
            document.getElementById('lcr_reading').value = data.lcr_reading;
            document.getElementById('unitmeasurement').value = data.unitmeasurement;
            document.getElementById('batchlot').value = data.batchlot;
            document.getElementById('process_location').value = data.process_location;
            document.getElementById('actionModal').style.display = 'block';
            document.getElementById('created_at').value = data.created_at;
            document.getElementById('verified_ll').value = data.verified_ll;
        }

        function closeModal() {
            document.getElementById('repairForm').reset();
            document.getElementById('actionModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('actionModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        document.getElementById('repairForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch('repairVerifiedSubmit.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message || 'Verification submitted successfully!',
                            text: data.testMessage,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        form.reset();
                        closeModal();
                        setTimeout(() => location.reload(), 1500);

                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: data.message || 'Submission failed!',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Unexpected error occurred.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    console.error('Fetch error:', error);
                });
        });

        document.getElementById('searchSerialCode').addEventListener('keyup', function() {
            const filter = this.value.toUpperCase();
            const rows = document.querySelectorAll('table tbody tr');
            let matchCount = 0;

            rows.forEach(row => {
                if (row.id === 'noResultsRow') return;

                const serialCodeCell = row.cells[1];
                if (serialCodeCell) {
                    const text = serialCodeCell.textContent || serialCodeCell.innerText;
                    const isMatch = text.toUpperCase().includes(filter);
                    row.style.display = isMatch ? '' : 'none';
                    if (isMatch) matchCount++;
                }
            });

            const noResultsRow = document.getElementById('noResultsRow');
            noResultsRow.style.display = matchCount === 0 ? '' : 'none';
        });
    </script>
</body>

</html>