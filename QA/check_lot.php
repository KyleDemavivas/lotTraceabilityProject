<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$kepi_lot = $_POST['kepi_lot'] ?? '';
if (!$kepi_lot) { echo json_encode(['accepted' => false, 'attempt_count' => 0]); exit; }

// Check for an in-progress attempt first
$stmt = $conn->prepare("SELECT TOP 1 attempt_number 
                         FROM qa_lot 
                         WHERE kepi_lot = ? AND status = 'IN_PROGRESS'
                         ORDER BY attempt_number DESC");
$stmt->execute([$kepi_lot]);
$inProgress = $stmt->fetch(PDO::FETCH_ASSOC);

if ($inProgress) {
    echo json_encode([
        'in_progress'     => true,
        'attempt_number'  => $inProgress['attempt_number'],
        'accepted'        => false,
        'attempt_count'   => 0,
    ]);
    exit;
}

// Get the most recent finalized attempt for this lot
$stmt = $conn->prepare("SELECT TOP 1 lot_result, attempt_number 
                         FROM qa_lot 
                         WHERE kepi_lot = ? AND status = 'FINALIZED'
                         ORDER BY attempt_number DESC");
$stmt->execute([$kepi_lot]);
$latest = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$latest) {
    // No prior attempts at all
    echo json_encode(['in_progress' => false, 'accepted' => false, 'attempt_count' => 0]);
    exit;
}

echo json_encode([
    'in_progress'    => false,
    'accepted'       => $latest['lot_result'] === 'ACCEPT',
    'attempt_count'  => $latest['attempt_number'],
    'last_result'    => $latest['lot_result'],
]);
exit;