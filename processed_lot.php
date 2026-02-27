<?php
include 'sidebar.php';
include 'db_connect.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if ($search !== '') {
    $query = "SELECT kepi_lot, line, operator_name, MIN(created_at) AS start_time, MAX(created_at) AS end_time, SUM(CAST(qty_input AS INT)) AS total_qty, MAX(process_remarks) AS process_remarks
              FROM mounter_process WHERE kepi_lot LIKE :search GROUP BY kepi_lot, line, operator_name ORDER BY start_time";
    $stmt = $conn->prepare($query);
    $stmt->execute(['search' => "%$search%"]);
} else {
    $query = "SELECT kepi_lot, line, operator_name, MIN(created_at) AS start_time, MAX(created_at) AS end_time, SUM(CAST(qty_input AS INT)) AS total_qty, MAX(process_remarks) AS process_remarks
              FROM mounter_process GROUP BY kepi_lot, line, operator_name ORDER BY start_time DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute();
}

$lots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Processed Lot</title>
    <link rel="stylesheet" href="css/viewlots.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>
</head>

<body>
    <div class="container">
        <h2>Processed Lot</h2>
        <div class="controls">
            <form method="GET" style="display: inline;">
                <input type="text" name="search" placeholder="Search KEPI Lot"
                    value="<?= htmlspecialchars($search) ?>" autocomplete="off">
                <button type="submit">Search</button>
            </form>
            <input type="text" id="start_date" class="datepicker" placeholder="Start Date">
            <input type="text" id="end_date" class="datepicker" placeholder="End Date">
            <button onclick="downloadData()">Download</button>
        </div>

        <div class="table-container">
            <table style="min-width:900px;" id="myTable" class="display">
                <thead>
                    <tr>
                        <th>Line</th>
                        <th>Processed Lot</th>
                        <th>Operator Name</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Total Quantity</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($lots) > 0): ?>
                        <?php foreach ($lots as $lot): ?>
                            <tr>
                                <td><?= htmlspecialchars($lot['line']) ?></td>
                                <td><?= htmlspecialchars($lot['kepi_lot']) ?></td>
                                <td><?= htmlspecialchars($lot['operator_name']) ?></td>
                                <td><?= date("F d, Y h:i A", strtotime($lot['start_time'])) ?></td>
                                <td><?= date("F d, Y h:i A", strtotime($lot['end_time'])) ?></td>
                                <td><?= htmlspecialchars($lot['total_qty']) ?></td>
                                <td>
                                    <span><?= htmlspecialchars($lot['process_remarks']) ?></span><br><br>
                                    <button class="remarks_btn"
                                        onclick="openRemarksModal('<?= htmlspecialchars($lot['kepi_lot']) ?>',
                                                                 '<?= htmlspecialchars($lot['process_remarks']) ?>')">
                                        Add Remarks
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="remarksModal">
        <h3>Add Remarks for <span id="lotLabel"></span></h3>
        <textarea id="remarksInput" rows="5"></textarea>
        <div style="margin-top:15px; text-align:right;">
            <button onclick="saveRemarks()">Save</button>
            <button onclick="closeRemarksModal()">Cancel</button>
        </div>
    </div>
    <div id="overlay"></div>

    <script>
        $('#myTable').DataTable({
            lengthChange: false,
            info: false,
            pageLength: 10,
            searching: false
        });

        let currentLotName = '';

        function openRemarksModal(lot, existingRemarks) {
            currentLotName = lot;
            $('#lotLabel').text(lot);
            $('#remarksInput').val(existingRemarks);
            $('#remarksModal').show();
            $('#overlay').show();
        }

        function closeRemarksModal() {
            $('#remarksModal').hide();
            $('#overlay').hide();
        }

        function saveRemarks() {
            const remarks = $('#remarksInput').val().trim();

            if (remarks === '') {
                alert('Please enter remarks.');
                return;
            }

            $.post('save_remarks.php', {
                kepi_lot: currentLotName,
                remarks: remarks
            }, function(response) {
                alert(response);
                location.reload();
            });
        }

        $(function() {
            $("#start_date, #end_date").datepicker({
                dateFormat: "mm-dd-yy"
            });
        });

        function downloadData() {
            const start = $("#start_date").val();
            const end = $("#end_date").val();

            if (!start || !end) {
                alert("Please select a valid date range.");
                return;
            }
            window.location.href = `download_processed.php?start=${start}&end=${end}`;
        }
    </script>
</body>

</html>