<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');
error_reporting(0);

if (!isset($_POST['serial_code'])) {
    echo json_encode(['success' => false, 'message' => 'No serial provided']);
    exit;
}

$serial_code = strtoupper(trim($_POST['serial_code']));
$source = $_POST['source'] ?? '';
if (!in_array($source, ['main', 'batchlot'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid source']);
    exit;
}
$main_table = $source === 'main' ? 'fviss_process' : 'fviss_batchlot';
$main_table2 = $source === 'main' ? 'partside_process' : 'partside_batchlot';

try {
    $stmt = $conn->prepare("SELECT qr_code, assy_code, model_name, kepi_lot, operator_name, shift, asmline, line, qty_input FROM $main_table WHERE TRIM(UPPER(serial_code)) = :code");
    $stmt->execute([':code' => $serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'This Serial Code is not found in the system.', 'title' => 'Serial Not Found']);
        exit;
    }

    $stmt2 = $conn->prepare("SELECT serial_status FROM $main_table WHERE serial_code = :serial_code
                            UNION ALL
                            SELECT serial_status FROM $main_table2 WHERE serial_code = :serial_code2");
    $stmt2->execute([':serial_code' => $serial_code, ':serial_code2' => $serial_code]);
    $serialStatus = $stmt2->fetchAll(PDO::FETCH_COLUMN);

    if (in_array('NO GOOD', $serialStatus, true)) {
        echo json_encode(['success' => false, 'message' => 'This serial is already tagged as NO GOOD and cannot be processed.', 'title' => 'Serial Code No Good']);
        exit;
    }

    $query2 = "SELECT COUNT(*) FROM repair_master WHERE TRIM(UPPER(serial_code)) = :code AND status = 'SCRAP'";
    $stmt2 = $conn->prepare($query2);
    $stmt2->execute([':code' => $serial_code]);
    $scrapCount = $stmt2->fetchColumn();
    if ($scrapCount > 0) {
        echo json_encode(['success' => false, 'message' => 'This serial is already tagged as SCRAP and cannot be processed.', 'title' => 'Serial Code Scrapped']);
        exit;
    }

    $finalQtyStmt = $conn->prepare("SELECT final_qtyinput FROM $main_table WHERE kepi_lot = :kepi_lot ORDER BY id DESC");
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
