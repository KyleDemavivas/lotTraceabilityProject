<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$kepi_lot = $_POST['kepi_lot'] ?? '';
if (!$kepi_lot) { echo json_encode(['success' => false]); exit; }

try {
    $stmt = $conn->prepare("
        SELECT 
            ql.id AS inspection_id,
            ql.kepi_lot, ql.model, ql.lot_quantity, ql.sample_size,
            ql.inspection_method, ql.code_letter, ql.line, ql.shift, ql.operator_id,
            ql.attempt_number, ql.lot_result, ql.created_at, ql.status,
            ql.defects_015, ql.defects_10,
            ISNULL(ng.ng_count, 0) AS ng_count
        FROM qa_lot ql
        LEFT JOIN (
            SELECT inspection_id, COUNT(*) AS ng_count
            FROM qa_process
            WHERE status = 'NO GOOD'
            GROUP BY inspection_id
        ) ng ON ng.inspection_id = ql.id
        WHERE ql.kepi_lot LIKE ?
        ORDER BY ql.kepi_lot, ql.attempt_number DESC
    ");
    $stmt->execute(['%' . $kepi_lot . '%']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;