<?php
include 'sidebar.php';
include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

try {
    $sql = "SELECT ng.qr_code, ng.serial_code, ng.defect, ng.location, ng.board_number, vp.operator_name, vp.serial_status, vp.board_status, vp.line, vp.shift, vp.model_name, vp.assy_code, vp.kepi_lot
        FROM vi_nogood ng JOIN vi_process vp ON ng.qr_code = vp.qr_code AND ng.serial_code = vp.serial_code LEFT JOIN repair_process rp ON rp.qr_code = ng.qr_code AND rp.serial_code = ng.serial_code AND rp.defect = ng.defect AND rp.location = ng.location
        WHERE vp.serial_status = 'NO GOOD' AND (rp.repair_status IS NULL OR rp.repair_status != 'GOOD') ORDER BY ng.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $nogood_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('Error fetching No Good data: '.$e->getMessage());
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
        <h2>No Good Serial</h2>
        <div class="table-container">
            <div style="text-align: right; margin-bottom: 10px;">
                <input type="text" id="searchSerialCode" placeholder="Search Serial Code..." style="padding: 5px; width: 250px;" minlength="13" maxlength="13">
            </div>
            <table>
                <thead>
                    <tr>
                        <th>QR Code</th>
                        <th>Serial Code</th>
                        <th>Defect</th>
                        <th>Location</th>
                        <th>Board Number</th>
                        <th>Operator Name</th>
                        <th>Serial Status</th>
                        <th>Board Status</th>
                        <th>Line</th>
                        <th>Shift</th>
                        <th>Model Name</th>
                        <th>Assy Code</th>
                        <th>KEPI Lot</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($nogood_data) > 0) { ?>
                        <?php foreach ($nogood_data as $row) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['qr_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['serial_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['defect']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['board_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['operator_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['serial_status']); ?></td>
                                <td><?php echo htmlspecialchars($row['board_status']); ?></td>
                                <td><?php echo htmlspecialchars($row['line']); ?></td>
                                <td><?php echo htmlspecialchars($row['shift']); ?></td>
                                <td><?php echo htmlspecialchars($row['model_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['assy_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['kepi_lot']); ?></td>
                                <td><button onclick='openModal(<?php echo json_encode($row); ?>)'>Repair</button></td>
                            </tr>
                            <tr id="noResultsRow" style="display: none; text-align: center;">
                                <td colspan="14">No records found</td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="14">No records found.</td>
                        </tr>
                    <?php } ?>
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
                    <input class="form-input" type="text" name="repaired_by" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Action</label>
                    <input class="form-input" type="text" name="action_rp" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">LCR Reading</label>
                    <input class="form-input" type="text" name="lcr_reading" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Parts Code</label>
                    <input class="form-input" type="text" name="parts_code" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Parts Lot</label>
                    <input class="form-input" type="text" name="parts_lot" required autocomplete="off">
                </div>

                <div class="form-group">
                    <label class="form-label">Unit of Measurement</label>
                    <select class="form-input" name="unitmeasurement" required autocomplete="off">
                        <option value="">Select Measurement</option>
                        <option value="N/A">N/A</option>
                        <option value="Ohms">Ohms</option>
                        <option value="kOhms">kOhms</option>
                        <option value="MOhms">MOhms</option>
                        <option value="nf">nf</option>
                        <option value="pF">pF</option>
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
        function openModal(data) {
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

            fetch('repair_submit.php', {
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
                            title: data.message || 'Repair Process successfully!',
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

                const serialCodeCell = row.cells[1];
                if (serialCodeCell) {
                    const text = serialCodeCell.textContent || serialCodeCell.innerText;
                    const isMatch = text.toUpperCase().includes(filter);
                    row.style.display = isMatch ? '' : 'none';
                    if (isMatch) matchCount++;
                }
            });

            document.getElementById('noResultsRow').style.display = matchCount === 0 ? '' : 'none';
        });
        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>

</html>