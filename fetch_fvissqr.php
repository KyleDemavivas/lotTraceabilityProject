<?php
include 'db_connect.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['code'])) {
    echo json_encode(['success' => false, 'message' => 'QR or Serial not provided']);
    exit;
}

$code = strtoupper(trim($_POST['code']?? ''));

try {

    if(strlen($code) > 20){
        $query = "SELECT assy_code, model_name, kepi_lot, operator_name, shift, asmline, line, qty_input FROM mod2_process WHERE qr_code = :qr_code";

        $stmt = $conn->prepare($query);
        $stmt->execute([':qr_code' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

    }else{
        $query = "SELECT assy_code, model_name, kepi_lot, operator_name, shift, asmline, line, qty_input FROM mod2_process WHERE serial_code = :serial_code";

        $stmt = $conn->prepare($query);
        $stmt->execute([':serial_code' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }    

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'This QR Code is not found within the system.', 'title'=>'QR Not Found']);
        exit;
    }

    $query2 = "SELECT COUNT(*) FROM mod2_process WHERE TRIM(UPPER(qr_code)) = :qr_code AND (board_status = 'HOLD' OR serial_status = 'NO GOOD')
                            UNION ALL
                             SELECT COUNT(*) FROM fviss_process WHERE TRIM(UPPER(qr_code)) = :qr_code2 AND (board_status = 'HOLD' OR serial_status = 'NO GOOD')";
    $stmt = $conn->prepare($query2);
    $stmt->execute([':qr_code' => $code, ':qr_code2' => $code]);
    $holdCount = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($holdCount[0] > 0 || $holdCount[1] > 0) {
        echo json_encode(['success' => false, 'message' => 'This QR Code has been marked as NO GOOD and cannot be processed.', 'title'=>'QR on Hold']);
        exit;
    }

    $stmtbatchlot = $conn->prepare("SELECT COUNT(*) FROM fviss_batchlot WHERE qr_code = :qr_code");
    $stmtbatchlot->execute([':qr_code' => $code]);
    $batchlotCount = $stmtbatchlot->fetchColumn();

    if ($batchlotCount > 0) {
        echo json_encode(['success' => false, 'title' => 'QR Code is in Batchlot', 'message' => 'This Board has been transferred to the Batchlot and cannot be processed.']);
        exit;
    }

    $finalQtyStmt = $conn->prepare("SELECT final_qtyinput FROM mod2_process WHERE kepi_lot = :kepi_lot ORDER BY created_at DESC");
    $finalQtyStmt->execute([':kepi_lot' => $row['kepi_lot']]);
    $final_qtyinput = $finalQtyStmt->fetchColumn() ?: 0;

    $serialStmt = $conn->prepare("SELECT serial_code1, serial_code2, serial_code3, serial_code4, serial_code5,
               serial_code6, serial_code7, serial_code8, serial_code9, serial_code10,
               serial_code11, serial_code12, serial_code13, serial_code14, serial_code15,
               serial_code16, serial_code17, serial_code18, serial_code19, serial_code20,
               serial_code21, serial_code22, serial_code23, serial_code24
        FROM label_code WHERE TRIM(UPPER(qr_code)) = :qr_code");

    $serialStmt->execute([':qr_code' => $code]);
    $serials = $serialStmt->fetch(PDO::FETCH_ASSOC);

    $serial_qty = 0;
    if ($serials) {
        foreach ($serials as $s) {
            if (!empty($s)) {
                $serial_qty++;
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
        'serial_qty' => $serial_qty
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
