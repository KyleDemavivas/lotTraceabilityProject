<?php
include 'sidebar.php';
include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

try {
    $sql = 'SELECT TOP 100 * FROM label_code ORDER BY created_date DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $labels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    exit('Error fetching data: '.$e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/viewlabel_registration.css">
    <script src="https://code.jquery.com/jquery-4.0.0.min.js" integrity="sha256-OaVG6prZf4v69dPg6PhVattBXkcOWQB62pdZ3ORyrao=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.css" />
    <script src="https://cdn.datatables.net/2.3.7/js/dataTables.js"></script>

</head>

<body>
    <div class="container">
        <h2>Label Registration Data</h2>
        <div class="table-container">
              <div style="margin-bottom: 15px; font-size: 0.8em; width:50%; display: flex; align-items: left; justify-content: left;">
            <span style="font-weight: bold;">Date From:</span>
            <input type="date" id="dateFrom" class="form-input-date" style="margin-left: 5px;">
            <span style="font-weight: bold; margin-left: 10px;">Date To:</span>
            <input type="date" id="dateTo" class="form-input-date" style="margin-left: 5px;">
            <button id="clearDate">Clear</button>
        </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Assy Code</th>
                        <th>Model Name</th>
                        <th>Letter Allocation</th>
                        <th>Serial Qty</th>
                        <th>KEPI Lot</th>
                        <th>QR Code</th>
                        <th>Serial Codes</th>
                        <th>Created By</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($labels) > 0) { ?>
                        <?php foreach ($labels as $label) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($label['assy_code']); ?></td>
                                <td><?php echo htmlspecialchars($label['model_name']); ?></td>
                                <td><?php echo htmlspecialchars($label['letter_allocation']); ?></td>
                                <td><?php echo htmlspecialchars($label['serial_qty']); ?></td>
                                <td><?php echo htmlspecialchars($label['kepi_lot']); ?></td>
                                <td><?php echo htmlspecialchars($label['qr_code']); ?></td>
                                <td>
                                    <?php
                                    $serials = [];
                            for ($i = 1; $i <= 18; ++$i) {
                                if (!empty($label["serial_code$i"])) {
                                    $serials[] = $label["serial_code$i"];
                                }
                            }
                            echo implode(', ', $serials);
                            ?>
                                </td>
                                <td><?php echo htmlspecialchars($label['created_by']); ?></td>
                                <td><?php if (isset($label['created_date'])) {
                                    echo htmlspecialchars(date('M d, Y h:i A', strtotime($label['created_date'])));
                                } else {
                                    echo 'N/A';
                                } ?>
                                </td>

                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="11">No records found.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<script>
    var table;
    $(document).ready(function() {
        table = $('.table').DataTable({
            pageLength: 10,
            ordering: true,
            searching: true,
            lengthChange: false,
            info: false
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var dateFrom = $('#dateFrom').val();
            var dateTo = $('#dateTo').val();
            var rowDate = data[8].split('.')[0]; // ← Created Date is index 8 // removes .000 → "2026-02-23 20:33:56"
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
    });
</script>