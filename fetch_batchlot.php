<?php
include 'db_connect.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['code'])) {
    echo json_encode(['success' => false, 'message' => 'Serial Code not provided']);
    exit;
}

$serial_code = strtoupper(trim($_POST['code']));

try {
    $query = "SELECT * FROM fviss_batchlot WHERE serial_code = :serial_code";

    $stmt = $conn->prepare($query);
    $stmt->execute([':serial_code' => $serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'This Serial Code is not found in the system.', 'title' => 'Serial Not Found']);
        exit;
    }

    $query2 = "SELECT COUNT(*) FROM fviss_batchlot WHERE TRIM(UPPER(serial_code)) = :serial_code AND (board_status = 'HOLD' OR serial_status = 'NO GOOD')";
    $stmt = $conn->prepare($query2);
    $stmt->execute([':serial_code' => $serial_code]);
    $holdCount = $stmt->fetchColumn();

    if ($holdCount > 0) {
        echo json_encode(['success' => false, 'title' => 'Serial Code on Hold', 'message' => 'This Serial Code has been marked as NO GOOD and cannot be processed.']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'assy_code' => $row['assy_code'],
        'model_name' => $row['model_name'],
        'kepi_lot' => $row['kepi_lot'],
        'operator_name' => $row['operator_name'],
        'shift' => $row['shift'],
        'asmline' => $row['asmline'],
        'line' => $row['line'],
        'qty_input' => $row['qty_input']
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
