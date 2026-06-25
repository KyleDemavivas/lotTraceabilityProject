<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$kepi_lot = $_POST['kepi_lot'] ?? '';
if (!$kepi_lot) { echo json_encode(['accepted' => false, 'attempt_count' => 0]); exit; }

// Get the most recent attempt for this lot
$stmt = $conn->prepare("SELECT TOP 1 lot_result, attempt_number 
                         FROM qa_lot 
                         WHERE kepi_lot = ? 
                         ORDER BY attempt_number DESC");
$stmt->execute([$kepi_lot]);
$latest = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$latest) {
    // No prior attempts at all
    echo json_encode(['accepted' => false, 'attempt_count' => 0]);
    exit;
}

echo json_encode([
    'accepted'       => $latest['lot_result'] === 'ACCEPT',
    'attempt_count'  => $latest['attempt_number'],
    'last_result'    => $latest['lot_result'],
]);
exit;