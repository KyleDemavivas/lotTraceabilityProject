<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';
include $_SERVER['DOCUMENT_ROOT'].'/traceability/sidebar.php';
assert(isset($conn));

$serial_code = isset($_GET['serial_code']) ? strtoupper($_GET['serial_code']) : '';

$qry = "SELECT * FROM repair_master WHERE status = 'SCRAP' ORDER BY created_at DESC";
$stmt = $conn->prepare($qry);
$stmt->execute();
$scrapRecord = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Document</title> -->
     <link rel="stylesheet" href="css/scrap_history.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
</head>
<body>
    <div class="container">
    <div class="board-section">
        <div class="header"><h2>Scrap History</h2></div>
    <table id="scrapHistoryTable">
        <thead>
            <tr>
                <th>QR Code</th>
                <th>KEPI Lot</th>
                <th>Serial Code</th>
                <th>Assembly Code</th>
                <th>Model Name</th>
                <th>Process</th>
                <th>Location</th>
                <th>Defect</th>
                <th>Operator</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($scrapRecord as $rows) { ?>
            <tr>
                <td><?php echo $rows['qr_code']; ?></td>
                <td><?php echo $rows['kepi_lot']; ?></td>
                <td><?php echo $rows['serial_code']; ?></td>
                <td><?php echo $rows['assy_code']; ?></td>
                <td><?php echo $rows['model_name']; ?></td>
                <td><?php echo $rows['process_location']; ?></td>
                <td><?php echo $rows['location']; ?></td>
                <td><?php echo $rows['defect']; ?></td>
                <td><?php echo $rows['operator_name']; ?></td>
                <td><?php echo date('M d, Y', strtotime($rows['created_at'])); ?></td>
                <td><?php echo date('H:i', strtotime($rows['created_at'])); ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>

<script>
    $(document).ready(function() {
        $('#scrapHistoryTable').DataTable({
            pageLength: 25,
            lengthChange: false,
            info: false,
            autoWidth: true,
            scrollX: true,
        });
    })
</script>