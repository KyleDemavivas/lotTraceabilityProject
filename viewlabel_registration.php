<?php
include 'sidebar.php';
include 'db_connect.php';

try {
    $sql = "SELECT TOP 100 * FROM label_code ORDER BY created_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $labels = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
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
                    <?php if (count($labels) > 0): ?>
                        <?php foreach ($labels as $label): ?>
                            <tr>
                                <td><?= htmlspecialchars($label['assy_code']) ?></td>
                                <td><?= htmlspecialchars($label['model_name']) ?></td>
                                <td><?= htmlspecialchars($label['letter_allocation']) ?></td>
                                <td><?= htmlspecialchars($label['serial_qty']) ?></td>
                                <td><?= htmlspecialchars($label['kepi_lot']) ?></td>
                                <td><?= htmlspecialchars($label['qr_code']) ?></td>
                                <td>
                                    <?php
                                    $serials = [];
                                    for ($i = 1; $i <= 18; $i++) {
                                        if (!empty($label["serial_code$i"])) {
                                            $serials[] = $label["serial_code$i"];
                                        }
                                    }
                                    echo implode(", ", $serials);
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($label['created_by']) ?></td>
                                <td><?php if (isset($label['created_date'])) {
                                        echo htmlspecialchars(date('M d, Y h:i A', strtotime($label['created_date'])));
                                    } else {
                                        echo 'N/A';
                                    } ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11">No records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

<script>
    $(document).ready(function() {
        $('.table').DataTable({
            pageLength: 10,
            ordering: true,
            searching: true,
            lengthChange: false,
            info: false
        });
    });
</script>