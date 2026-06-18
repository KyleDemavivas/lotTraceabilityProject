<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT 
            kepi_lot, serial_code, inspection_method, line, shift,
            location, defect_code, severity, operator_id,
            created_at, model, lot_result
        FROM qa_process
        WHERE status = 'NO GOOD'
        ORDER BY created_at DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;