<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';
header('Content-Type: application/json');

$kepi_lot = $_POST['kepi_lot'] ?? '';
$line = $_POST['line'] ?? '';

if (!$kepi_lot || !$line) {
    echo json_encode(['success' => false, 'count' => 0]);
    exit;
}

$sql = 'SELECT TOP 1 board_counter FROM partside_process WHERE kepi_lot = :kepi_lot AND line = :line ORDER BY id DESC';

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':kepi_lot' => $kepi_lot,
    ':line' => $line,
]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'count' => $row ? intval($row['board_counter']) : 0,
]);
