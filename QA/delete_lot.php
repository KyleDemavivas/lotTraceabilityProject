<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$kepi_lot = $_POST['kepi_lot'] ?? '';
if (!$kepi_lot) { echo json_encode(['status' => 'error', 'message' => 'No lot provided.']); exit; }

try {
    // delete defects first due to FK constraint, then the main records
    $stmt = $conn->prepare("
        DELETE FROM qa_defects 
        WHERE qa_process_id IN (
            SELECT id FROM qa_process WHERE kepi_lot = ?
        )
    ");
    $stmt->execute([$kepi_lot]);

    $stmt = $conn->prepare("DELETE FROM qa_process WHERE kepi_lot = ?");
    $stmt->execute([$kepi_lot]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
exit;