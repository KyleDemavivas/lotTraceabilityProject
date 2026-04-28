<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="Processed_Lots.csv"');

$start = isset($_GET['start']) ? $_GET['start'].' 00:00:00' : null;
$end = isset($_GET['end']) ? $_GET['end'].' 23:59:59' : null;

if (!$start || !$end) {
    echo 'Invalid date range.';
    exit;
}

$query = 'SELECT kepi_lot, line, operator_name, MIN(created_at) AS start_time, MAX(created_at) AS end_time, SUM(CAST(qty_input AS INT)) AS total_qty
          FROM mounter_process WHERE created_at BETWEEN :start AND :end GROUP BY kepi_lot, line, operator_name ORDER BY start_time DESC';

$stmt = $conn->prepare($query);
$stmt->bindParam(':start', $start);
$stmt->bindParam(':end', $end);
$stmt->execute();
$lots = $stmt->fetchAll(PDO::FETCH_ASSOC);

$output = fopen('php://output', 'w');

fputcsv($output, ['Line', 'Processed Lot', 'Operator Name', 'Total Quantity', 'Start Time', 'End Time']);

foreach ($lots as $lot) {
    fputcsv($output, [
        $lot['line'],
        $lot['kepi_lot'],
        $lot['operator_name'],
        $lot['total_qty'],
        date('F d, Y h:i A', strtotime($lot['start_time'])),
        date('F d, Y h:i A', strtotime($lot['end_time'])),
    ]);
}

fclose($output);
exit;
