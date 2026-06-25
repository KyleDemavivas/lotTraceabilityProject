<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

try {
    $stmt = $conn->query("
        SELECT 
            qp.id, qp.serial_code, qp.location, qp.defect_code, qp.severity,
            qp.created_at, qp.inspection_id,
            ql.kepi_lot, ql.inspection_method, ql.line, ql.shift,
            ql.operator_id, ql.model, ql.lot_result, ql.attempt_number
        FROM qa_process qp
        JOIN qa_lot ql ON ql.id = qp.inspection_id
        WHERE qp.status = 'NO GOOD' AND qp.scrap = 0
        ORDER BY qp.created_at DESC
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;