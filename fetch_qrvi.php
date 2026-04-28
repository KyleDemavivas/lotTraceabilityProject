<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');
error_reporting(0);

if (!isset($_POST['qr_code'])) {
    echo json_encode(['success' => false, 'message' => 'QR not provided']);
    exit;
}

$qr_code = strtoupper(trim($_POST['qr_code']));

try {
    $stmt = $conn->prepare('SELECT assy_code, model_name, kepi_lot, operator_name, qty_input FROM vi_process WHERE TRIM(UPPER(qr_code)) = :qr_code');
    $stmt->execute([':qr_code' => $qr_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'QR Code is not Found in System.', 'title' => 'QR Not Found']);
        exit;
    }

    $stmt2 = $conn->prepare("SELECT COUNT(*) FROM vi_process WHERE qr_code = :qr_code AND (board_status = 'HOLD' OR serial_status = 'NO GOOD')
                            UNION ALL
                            SELECT COUNT(*) FROM ai_process WHERE qr_code = :qr_code2 AND (board_status = 'HOLD' OR serial_status = 'NO GOOD')");
    $stmt2->execute([':qr_code' => $qr_code, ':qr_code2' => $qr_code]);
    $holdCount = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    if ($holdCount[0] > 0 || $holdCount[1] > 0) {
        echo json_encode(['success' => false, 'message' => 'This QR Code is currently on HOLD and cannot be processed.', 'title' => 'QR on Hold']);
        exit;
    }

    $finalQtyStmt = $conn->prepare('SELECT final_qtyinput FROM vi_process WHERE kepi_lot = :kepi_lot AND final_qtyinput IS NOT NULL ORDER BY created_at DESC');
    $finalQtyStmt->execute([':kepi_lot' => $row['kepi_lot']]);
    $final_qtyinput = (int) ($finalQtyStmt->fetchColumn() ?: 0);

    $stmt3 = $conn->prepare('SELECT board_counter FROM ai_process WHERE :qr_code = qr_code');
    $stmt3->execute([':qr_code' => $qr_code]);
    $boardCount = $stmt3->fetchColumn();

    echo json_encode([
        'success' => true,
        'assy_code' => $row['assy_code'],
        'model_name' => $row['model_name'],
        'kepi_lot' => $row['kepi_lot'],
        'operator_name' => $row['operator_name'],
        'qty_input' => $row['qty_input'],
        'final_qtyinput' => $final_qtyinput,
        'boardCount' => $boardCount,
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
