<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$kepi_lot = $_POST['kepi_lot'] ?? '';
if (!$kepi_lot) { echo json_encode(['accepted' => false]); exit; }

$stmt = $conn->prepare("SELECT COUNT(*) FROM qa_process WHERE kepi_lot = ? AND lot_result = 'ACCEPT'");
$stmt->execute([$kepi_lot]);
$count = $stmt->fetchColumn();

echo json_encode(['accepted' => $count > 0]);
exit;