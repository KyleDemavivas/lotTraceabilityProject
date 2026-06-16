<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$kepi_lot = $_POST['kepi_lot'] ?? '';
if (!$kepi_lot) { echo json_encode(['success' => false]); exit; }

try {
    $stmt = $conn->prepare("
        SELECT 
            kepi_lot, model, lot_quantity, sample_size,
            inspection_method, line, shift, operator_id,
            MIN(created_at) as created_at,
            lot_result,
            SUM(CASE WHEN status = 'NO GOOD' THEN 1 ELSE 0 END) as ng_count
        FROM qa_process
        WHERE kepi_lot LIKE ?
        GROUP BY kepi_lot, model, lot_quantity, sample_size,
                 inspection_method, line, shift, operator_id, lot_result
        ORDER BY MIN(created_at) DESC
    ");
    $stmt->execute(['%' . $kepi_lot . '%']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'data' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;