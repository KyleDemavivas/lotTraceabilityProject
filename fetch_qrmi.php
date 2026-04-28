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
    $query = 'SELECT assy_code, model_name, kepi_lot, operator_name, shift, asmline, line, qty_input FROM mi_process WHERE qr_code = :qr_code ';

    $stmt = $conn->prepare($query);
    $stmt->execute([':qr_code' => $qr_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'This QR Code is Not Found in the System.', 'title' => 'QR Not Found']);
        exit;
    }

    $query2 = "SELECT COUNT(*) FROM repair_master WHERE TRIM(UPPER(qr_code)) = :qr_code AND status = 'SCRAP'";
    $stmt2 = $conn->prepare($query2);
    $stmt2->execute([':qr_code' => $qr_code]);
    $scrapCount = $stmt2->fetchColumn();
    if ($scrapCount > 0) {
        echo json_encode(['success' => false, 'message' => 'This QR Code is already marked as SCRAP and cannot be processed.', 'title' => 'QR Marked as SCRAP']);
        exit;
    }

    $finalQtyStmt = $conn->prepare('SELECT final_qtyinput FROM mi_process WHERE kepi_lot = :kepi_lot ORDER BY created_at DESC');
    $finalQtyStmt->execute([':kepi_lot' => $row['kepi_lot']]);
    $final_qtyinput = $finalQtyStmt->fetchColumn() ?: 0;

    $serialStmt = $conn->prepare('SELECT serial_code1, serial_code2, serial_code3, serial_code4, serial_code5,
               serial_code6, serial_code7, serial_code8, serial_code9, serial_code10,
               serial_code11, serial_code12, serial_code13, serial_code14, serial_code15,
               serial_code16, serial_code17, serial_code18, serial_code19, serial_code20,
               serial_code21, serial_code22, serial_code23, serial_code24
        FROM label_code WHERE TRIM(UPPER(qr_code)) = :qr_code');

    $serialStmt->execute([':qr_code' => $qr_code]);
    $serials = $serialStmt->fetch(PDO::FETCH_ASSOC);

    $serial_qty = 0;
    if ($serials) {
        foreach ($serials as $s) {
            if (!empty($s)) {
                ++$serial_qty;
            }
        }
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
        'qty_input' => $row['qty_input'],
        'final_qtyinput' => $final_qtyinput,
        'serial_qty' => $serial_qty,
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
