<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');
error_reporting(0);

if (!isset($_POST['serial_code'])) {
    echo json_encode(['success' => false, 'message' => 'No serial provided']);
    exit;
}

$serial_code = strtoupper(trim($_POST['serial_code']));

try {
    $stmt = $conn->prepare('SELECT qr_code, assy_code, model_name, kepi_lot, operator_name, shift, asmline, line, qty_input FROM fviss_batchlot WHERE TRIM(UPPER(serial_code)) = :serial_code');
    $stmt->execute([':serial_code' => $serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'The Serial Code is not found in the system.', 'title' => 'Serial Code Not Found']);
        exit;
    }

    $stmt2 = $conn->prepare('SELECT serial_status FROM fviss_batchlot WHERE serial_code = :serial_code');
    $stmt2->execute([':serial_code' => $serial_code]);
    $serialStatus = $stmt2->fetchColumn();

    if ($serialStatus === 'NO GOOD') {
        echo json_encode(['success' => false, 'message' => 'This serial is already tagged as NO GOOD and cannot be processed.', 'title' => 'Serial Code No Good']);
        exit;
    }

    $finalQtyStmt = $conn->prepare('SELECT final_qtyinput FROM fviss_process WHERE kepi_lot = :kepi_lot ORDER BY id DESC');
    $finalQtyStmt->execute([':kepi_lot' => $row['kepi_lot']]);
    $final_qtyinput = (int) ($finalQtyStmt->fetchColumn() ?: 0);

    echo json_encode([
        'success' => true,
        'qr_code' => $row['qr_code'],
        'assy_code' => $row['assy_code'],
        'model_name' => $row['model_name'],
        'kepi_lot' => $row['kepi_lot'],
        'operator_name' => $row['operator_name'],
        'shift' => $row['shift'],
        'asmline' => $row['asmline'],
        'line' => $row['line'],
        'qty_input' => $row['qty_input'],
        'final_qtyinput' => $final_qtyinput,
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
