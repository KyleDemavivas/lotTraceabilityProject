<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/sidebar.php';

$query = 'SELECT * FROM repair_boardanalysis';
$stmt = $conn->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/repair_boardanalysis.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-4.0.0.slim.min.js" integrity="sha256-8DGpv13HIm+5iDNWw1XqxgFB4mj+yOKFNb+tHBZOowc=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>
<body>
    <div class="form-container">
        <table id="table_main" name="table_main" class="display">
        <thead>
            <tr>
                <th>Board Serial</th>
                <th>Defect</th>
                <th>Process</th>
                <th>ICT Jig No.</th>
                <th>WI Jig No.</th>
                <th>FT Jig No.</th>
                <th>ICT Component</th>
                <th>ICT Reference</th>
                <th>ICT Reading</th>
                <th>FT Step</th>
                <th>FT Reference</th>
                <th>FT Result</th>
                <th>Analysis</th>
                <th>Action</th>
                <th>Result</th>
                <th>Operator Name</th>
                <th>Date</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($results as $row) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['serialcode']); ?></td>
                    <td><?php echo htmlspecialchars($row['defect']); ?></td>
                    <td><?php echo htmlspecialchars($row['process']); ?></td>
                    <td><?php echo htmlspecialchars($row['ict_jig']); ?></td>
                    <td><?php echo htmlspecialchars($row['wi_jig']); ?></td>
                    <td><?php echo htmlspecialchars($row['ft_jig']); ?></td>
                    <td><?php echo htmlspecialchars($row['ict_component']); ?></td>
                    <td><?php echo htmlspecialchars($row['ict_ref']); ?></td>
                    <td><?php echo htmlspecialchars($row['ict_reading']); ?></td>
                    <td><?php echo htmlspecialchars($row['ft_step']); ?></td>
                    <td><?php echo htmlspecialchars($row['ft_ref']); ?></td>
                    <td><?php echo htmlspecialchars($row['ft_result']); ?></td>
                    <td><?php echo htmlspecialchars($row['analysis']); ?></td>
                    <td><?php echo htmlspecialchars($row['action']); ?></td>
                    <td><?php echo htmlspecialchars($row['result']); ?></td>
                    <td><?php echo htmlspecialchars($row['operator']); ?></td>
                    <td><?php echo htmlspecialchars(date('Y-m-d', strtotime($row['DateTime']))); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    </div>
    <script>
        let table;

        table = $('#table_main').DataTable({
                "paging": true,
                "searching": true,
                deferRender: true,
                "ordering": true,
                "order": [[16, "desc"]],
                "info": false,
                "lengthChange": false,
                "pageLength": 10,
                "columnDefs": [
                    {"searchable": false, "targets": 3},
                    {"orderable": false, "targets": [0,1,2,4,5,6,7,8,9,10,11,12,13,14,15]}
                ]
            });
    </script>
</body>
</html>