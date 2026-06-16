<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$kepi_lot = $_POST['kepi_lot'] ?? '';
if (!$kepi_lot) { echo json_encode(['success' => false]); exit; }

try {
    // Lot header
    $stmt = $conn->prepare("
        SELECT TOP 1 inspection_method, sample_size, lot_result
        FROM qa_process WHERE kepi_lot = ?
    ");
    $stmt->execute([$kepi_lot]);
    $header = $stmt->fetch(PDO::FETCH_ASSOC);

    // Serials
    $stmt = $conn->prepare("
        SELECT id, serial_code, status
        FROM qa_process
        WHERE kepi_lot = ?
        ORDER BY created_at ASC
    ");
    $stmt->execute([$kepi_lot]);
    $serials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Defects per serial (once qa_defects is wired up)
    $defectStmt = $conn->prepare("
        SELECT qa_process_id, defect_code, location, severity
        FROM qa_defects
        WHERE qa_process_id = ?
    ");

    foreach ($serials as &$s) {
        $defectStmt->execute([$s['id']]);
        $s['defects'] = $defectStmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'data'    => array_merge($header, ['serials' => $serials])
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;