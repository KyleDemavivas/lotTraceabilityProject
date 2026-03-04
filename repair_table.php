<?php
include 'sidebar.php';
include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

try {
    $sql = 'SELECT DISTINCT * FROM main_repair_view';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $nogood_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt2 = $conn->prepare('SELECT COUNT(*) FROM repair_ll_verify');
    $stmt2->execute();
    $repairCheck = $stmt2->fetchColumn();
} catch (PDOException $e) {
    exit('Error fetching No Good data: '.$e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/repair_process.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>F
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.13.6/sorting/datetime-moment.js"></script>

    <style>
        .form-input-date {
            height: 28px;
            padding: 2px 8px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #clearDate {
            height: 28px;
            padding: 2px 10px;
            font-size: 0.8em;
            cursor: pointer;
            width: 5em;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>REPAIR TABLE</h2>
        <?php /* ?> <div class="table-container">
             <div style="float: left; margin-bottom: 30px;">
                <span style="font-weight: bold;">SERIAL CODE:</span>
                <input class="form-input" type="text" name="qr_code" id="searchSerialCode" required autocomplete="off" autofocus placeholder="SERIAL CODE" style="width: 200px; margin-left: 10px;">

                <span style="font-weight: bold; margin-left: 30px;">QR CODE:</span>
                <input class="form-input" type="text" name="qr_code" id="searchQRCode" required autocomplete="off" autofocus placeholder="QR CODE" style="width: 200px; margin-left: 10px;">
            </div> <php?*/ ?>
        <div style="margin-bottom: 15px; font-size: 0.8em; width:50%; display: flex; align-items: left; justify-content: left;">
            <span style="font-weight: bold;">Date From:</span>
            <input type="date" id="dateFrom" class="form-input-date" style="margin-left: 5px;">
            <span style="font-weight: bold; margin-left: 10px;">Date To:</span>
            <input type="date" id="dateTo" class="form-input-date" style="margin-left: 5px;">
            <button id="clearDate">Clear</button>
        </div>
        <table id="repairTable" class="display">
            <thead>
                <tr>
                    <th>QR Code</th>
                    <th>Serial Code</th>
                    <th>Defect</th>
                    <th>Location</th>
                    <th>Process Location</th>
                    <th>Board Number</th>
                    <th>Operator Name</th>
                    <th>Serial Status</th>
                    <th>Board Status</th>
                    <th>Line</th>
                    <th>Shift</th>
                    <th>Model Name</th>
                    <th>Assy Code</th>
                    <th>KEPI Lot</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody style="font-size: 12px;">
                <?php if (count($nogood_data) > 0) { ?>
                    <?php foreach ($nogood_data as $row) { ?>
                        <tr>
                            <td>
                                <?php
                                if ($row['qr_code']) {
                                    echo htmlspecialchars($row['qr_code']);
                                } else {
                                    $qryNull = htmlspecialchars($row['serial_code']);
                                    $stmt = $conn->prepare('SELECT qr_code FROM trace_process WHERE serial_code = ?');
                                    $stmt->execute([$qryNull]);
                                    $nullQR = $stmt->fetchColumn();
                                    echo htmlspecialchars($nullQR);
                                }
                        ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['serial_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['defect']); ?></td>
                            <td>
                                <?php
                        if ($row['location']) {
                            echo htmlspecialchars($row['location']);
                        } else {
                            echo htmlspecialchars('N/A');
                        }
                        ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['process_location']); ?></td>
                            <td>
                                <?php
                        if ($row['board_number']) {
                            echo htmlspecialchars($row['board_number']);
                        } else {
                            echo htmlspecialchars('N/A');
                        }
                        ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['operator_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['serial_status']); ?></td>
                            <td><?php echo htmlspecialchars($row['board_status']); ?></td>
                            <td><?php echo htmlspecialchars($row['line']); ?></td>
                            <td><?php echo htmlspecialchars($row['shift']); ?></td>
                            <td><?php echo htmlspecialchars($row['model_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['assy_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['kepi_lot']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo date('F d, Y', strtotime($row['created_at_dt'])); ?></td>
                            <td><button onclick='openModal(<?php echo json_encode($row); ?>)'>Repair</button></td>
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
                    <label class="form-label">Process Location</label>
                    <input class="form-input" type="text" name="process_location" id="process_location" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Repaired By</label>
                    <input class="form-input" type="text" name="repaired_by" id="repaired_by" value="<?php echo htmlspecialchars($_SESSION['user_namefl']); ?>" readonly>
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
        var table;

        $(document).ready(function() {
            $.fn.dataTable.moment('MMMM DD, YYYY');
            table = $('#repairTable').DataTable({
                pageLength: 10,
                order: [
                    [15, 'desc']
                ], // Date column (index 15)
                columnDefs: [{
                        orderable: false,
                        targets: 16
                    } // Action column (index 16)
                ],
                lengthChange: false,
                info: false
            });
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var dateFrom = $('#dateFrom').val();
            var dateTo = $('#dateTo').val();
            var rowDate = data[15].split('.')[0]; // removes .000 → "2026-02-23 20:33:56"
            var date = new Date(rowDate);

            if (!dateFrom && !dateTo) return true;

            var date = new Date(rowDate);
            var from = dateFrom ? new Date(dateFrom) : null;
            var to = dateTo ? new Date(dateTo) : null;

            if (from) from.setHours(0, 0, 0, 0);
            if (to) to.setHours(23, 59, 59, 999);

            if (from && to) return date >= from && date <= to;
            if (from) return date >= from;
            if (to) return date <= to;

            return true;
        });

        $('#dateFrom, #dateTo').on('change', function() {
            table.draw();
        });

        $('#clearDate').on('click', function() {
            $('#dateFrom').val('');
            $('#dateTo').val('');
            table.draw();
        });

        function resetTable() {
            document.getElementById('repairForm').reset();
        }

        function openModal(data) {
            document.getElementById('qr_code').value = data.qr_code;
            document.getElementById('model_name').value = data.model_name;
            document.getElementById('assy_code').value = data.assy_code;
            document.getElementById('kepi_lot').value = data.kepi_lot;
            document.getElementById('serial_code').value = data.serial_code;
            document.getElementById('defect').value = data.defect;
            document.getElementById('operator_name').value = data.operator_name;
            document.getElementById('location').value = data.location;
            document.getElementById('process_location').value = data.process_location;
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
                            timer: 3000,
                            timerProgressBar: true
                        });
                        form.reset();
                        closeModal();
                        resetTable();
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

        /*document.getElementById('searchSerialCode').addEventListener('input', function() {
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

        document.getElementById('searchQRCode').addEventListener('input', function() {
            const filter = this.value.toUpperCase();
            const rows = document.querySelectorAll('table tbody tr');
            let matchCount = 0;

            rows.forEach(row => {
                if (row.id === 'noResultsRow') return;

                const qrCodeCell = row.cells[0];
                if (qrCodeCell) {
                    const text = qrCodeCell.textContent || qrCodeCell.innerText;
                    const isMatch = text.toUpperCase().includes(filter);
                    row.style.display = isMatch ? '' : 'none';
                    if (isMatch) matchCount++;
                }
            });

            document.getElementById('noResultsRow').style.display = matchCount === 0 ? '' : 'none';
        });*/

        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    </script>
</body>

</html>