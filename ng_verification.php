<?php
include 'sidebar.php';
include 'db_connect.php';

try {
    $sql = "SELECT * FROM repair_process WHERE judgement_ll = 'NO GOOD' OR judgement_vi = 'NO GOOD' ORDER BY created_at DESC";

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
    <link rel="stylesheet" href="css/ng_verification.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="container">
        <h2>No Good Verification</h2>
        <div class="table-container">
            <div style="text-align: right; margin-bottom: 10px;">
                <input type="text" id="searchSerialCode" placeholder="Search Serial Code..." style="padding: 5px; width: 250px;" minlength="13" maxlength="13">
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Model Name</th>
                        <th>Assy Code</th>
                        <th>KEPI Lot</th>
                        <th>QR Code</th>
                        <th>Serial Code</th>
                        <th>Defect</th>
                        <th>Location</th>
                        <th>Board Number</th>
                        <th>Operator Name</th>
                        <th>Batchlot</th>
                        <th>Action</th>
                        <th>LCR Reading</th>
                        <th>Parts Code</th>
                        <th>Parts Lot</th>
                        <th>Unit Measurement</th>
                        <th>LL Verification</th>
                        <th>VI Verification</th>
                        <th>Line</th>
                        <th>Shift</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($nogood_data) > 0): ?>
                        <?php foreach ($nogood_data as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['model_name']) ?></td>
                                <td><?= htmlspecialchars($row['assy_code']) ?></td>
                                <td><?= htmlspecialchars($row['kepi_lot']) ?></td>
                                <td><?= htmlspecialchars($row['qr_code']) ?></td>
                                <td><?= htmlspecialchars($row['serial_code']) ?></td>
                                <td><?= htmlspecialchars($row['defect']) ?></td>
                                <td><?= htmlspecialchars($row['location']) ?></td>
                                <td><?= htmlspecialchars($row['board_number']) ?></td>
                                <td><?= htmlspecialchars($row['operator_name']) ?></td>
                                <td><?= htmlspecialchars($row['batchlot']) ?></td>
                                <td><?= htmlspecialchars($row['action_rp']) ?></td>
                                <td><?= htmlspecialchars($row['lcr_reading']) ?></td>
                                <td><?= htmlspecialchars($row['parts_code']) ?></td>
                                <td><?= htmlspecialchars($row['parts_lot']) ?></td>
                                <td><?= htmlspecialchars($row['unitmeasurement']) ?></td>
                                <td><?= htmlspecialchars($row['judgement_ll']) ?></td>
                                <td><?= htmlspecialchars($row['judgement_vi']) ?></td>
                                <td><?= htmlspecialchars($row['line']) ?></td>
                                <td><?= htmlspecialchars($row['shift']) ?></td>
                                <td><button onclick='openModal(<?= json_encode($row) ?>)'>Repair</button></td>
                            </tr>
                            <tr id="noResultsRow" style="display: none; text-align: center;">
                                <td colspan="18">No records found</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="18">No records found.</td>
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
                    <label class="form-label">Repaired By</label>
                    <input class="form-input" type="text" name="repaired_by" id="repaired_by" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Action</label>
                    <input class="form-input" type="text" name="action_rp" id="action_rp" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">LCR Reading</label>
                    <input class="form-input" type="text" name="lcr_reading" id="lcr_reading" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Parts Code</label>
                    <input class="form-input" type="text" name="parts_code" id="parts_code" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Parts Lot</label>
                    <input class="form-input" type="text" name="parts_lot" id="parts_lot" autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Unit of Measurement</label>
                    <select class="form-input" name="unitmeasurement" required autocomplete="off">
                        <option value="">Select Measurement</option>
                        <option value="N/A">N/A</option>
                        <option value="Ohms">Ohms</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Batch Lot</label>
                    <select class="form-input" name="batchlot" required autocomplete="off">
                        <option value="">Select here</option>
                        <option value="YES">Yes</option>
                        <option value="NO">No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Repairable</label>
                    <select class="form-input" name="repairable" required autocomplete="off">
                        <option value="">Select here</option>
                        <option value="YES">Yes</option>
                        <option value="NO">No</option>
                    </select>
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
            document.getElementById('actionModal').style.display = 'block';
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

            fetch('ng_verificationsubmit.php', {
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
                            title: data.message || 'Verify Repair successfully!',
                            showConfirmButton: false,
                            // timer: 3000,
                            // timerProgressBar: true
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
                            // timer: 3000,
                            // timerProgressBar: true
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
                        // timer: 3000,
                        // timerProgressBar: true
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

                const serialCodeCell = row.cells[4];
                if (serialCodeCell) {
                    const text = serialCodeCell.textContent || serialCodeCell.innerText;
                    const isMatch = text.toUpperCase().includes(filter);
                    row.style.display = isMatch ? '' : 'none';
                    if (isMatch) matchCount++;
                }
            });

            document.getElementById('noResultsRow').style.display = matchCount === 0 ? '' : 'none';
        });
    </script>
</body>

</html>