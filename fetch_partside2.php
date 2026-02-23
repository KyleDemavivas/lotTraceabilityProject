<?php
include 'db_connect.php';

header('Content-Type: application/json');
error_reporting(0);

if (!isset($_POST['serial_code'])) {
    echo json_encode(['success' => false, 'message' => 'No serial provided']);
    exit;
}

$serial_code = strtoupper(trim($_POST['serial_code'] ?? ''));
$source = $_POST['source'] ?? '';
$main_table = $source === 'main' ? 'partside_process' : 'partside_batchlot';
$main_table2 = $source === 'main' ? 'partside2_process' : 'partside2_batchlot';

$allowed = ['partside_process', 'partside_batchlot', 'partside2_process', 'partside2_batchlot'];

if(!in_array($main_table, $allowed)){
    echo json_encode(['success' => false, 'message' => 'Invalid source provided']);
    exit;
}

try {

    $stmt = $conn->prepare("SELECT qr_code, assy_code, model_name, kepi_lot, operator_name, shift, asmline, line, qty_input FROM $main_table WHERE TRIM(UPPER(serial_code)) = :serial_code");
    $stmt->execute([':serial_code' => $serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Serial not found']);
        exit;
    }

    $stmt = $conn->prepare("SELECT serial_status FROM $main_table WHERE TRIM(UPPER(serial_code)) = :serial_code AND serial_status = 'NO GOOD'
                            UNION ALL
                            SELECT serial_status FROM $main_table2 WHERE TRIM(UPPER(serial_code)) = :serial_code2 AND serial_status = 'NO GOOD'");
    $stmt->execute([':serial_code' => $serial_code, ':serial_code2' => $serial_code]);
    $holdCount = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if(in_array('NO GOOD', $holdCount, true)){
        echo json_encode(['success' => false, 'errorCode' => 'serialhold', 'message' => 'This Serial Code is currently on HOLD and cannot be processed.']);
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
        'final_qtyinput' => $final_qtyinput
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
