<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$serial_code = $_POST['serial_code'] ?? '';
$kepi_lot = $_POST['kepi_lot'] ?? '';

if (!$kepi_lot || !$serial_code) { echo json_encode(['status' => 'error', 'message' => 'No lot or serial code provided.']); exit; }

try {
    $stmt = $conn->prepare("UPDATE qa_process SET scrap = 1 WHERE kepi_lot = ? AND serial_code = ?");
    $stmt->execute([$kepi_lot, $serial_code]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;