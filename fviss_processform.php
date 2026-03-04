<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => '', 'board_count' => 0];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $source = $_POST['source'] ?? '';
    $main_table = $source === 'main' ? 'fviss_process' : 'fviss_batchlot';

    try {
        $qr_code = strtoupper($_POST['qr_code'] ?? '');
        $operator_name = $_POST['operator_name'] ?? '';
        $shift = $_POST['shift'] ?? '';
        $asmline = $_POST['asmline'] ?? '';
        $line = $_POST['line'] ?? '';
        $assy_code = strtoupper($_POST['assy_code'] ?? '');
        $model_name = strtoupper($_POST['model_name'] ?? '');
        $kepi_lot = strtoupper($_POST['kepi_lot'] ?? '');
        $serial_qty = $_POST['serial_qty'] ?? 0;
        $qr_count = $_POST['qr_count'] ?? 0;
        $qty_input = $_POST['qty_input'] ?? 0;

        if (empty($qr_code) || empty($operator_name) || empty($shift) || empty($asmline) || empty($line) || empty($assy_code) || empty($model_name) || empty($kepi_lot)) {
            $response['message'] = 'Missing required fields.';
            echo json_encode($response);
            exit;
        }

        date_default_timezone_set('Asia/Manila');
        $created_at = date('Y-m-d H:i:s');

        $counterQuery = "SELECT board_counter FROM $main_table WHERE kepi_lot = :kepi_lot AND line = :line ORDER BY id DESC";
        $counterStmt = $conn->prepare($counterQuery);
        $counterStmt->execute([':kepi_lot' => $kepi_lot, ':line' => $line]);
        $lastCounter = $counterStmt->fetchColumn();
        $board_counter = ($lastCounter) ? ($lastCounter + 1) : 1;

        $checkQuery = 'SELECT mod2_process, fviss_process FROM trace_process WHERE qr_code = :qr_code';
        // $checkQuery = "SELECT mod2_process, fviss_process FROM trace_process WHERE qr_code = :qr_code";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([':qr_code' => $qr_code]);
        $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if ($row['mod2_process'] === 'GOOD') {
                if ($row['fviss_process'] === 'GOOD') {
                    $response['message'] = 'FVISS Process already has data.';
                    echo json_encode($response);
                    exit;
                } else {
                    $updateMounter = "UPDATE trace_process SET fviss_process = 'GOOD' WHERE qr_code = :qr_code";
                    $updateStmt = $conn->prepare($updateMounter);
                    $updateStmt->execute([':qr_code' => $qr_code]);
                }
            } else {
                $response['message'] = 'MOD 2 PROCESS NOT FOUND';
                echo json_encode($response);
                exit;
            }
        }

        $finalQtyQuery = "SELECT COALESCE(SUM(CAST(qty_input AS INT)),0) AS final_qty 
                          FROM $main_table WHERE kepi_lot = :kepi_lot AND line = :line";
        $finalQtyStmt = $conn->prepare($finalQtyQuery);
        $finalQtyStmt->execute([':kepi_lot' => $kepi_lot, ':line' => $line]);
        $previous_final_qty = $finalQtyStmt->fetchColumn();
        $final_qtyinput = $previous_final_qty + (int) $qty_input;

        $serialQuery = 'SELECT serial_code1, serial_code2, serial_code3, serial_code4, serial_code5, serial_code6, 
                        serial_code7, serial_code8, serial_code9, serial_code10, serial_code11, serial_code12, 
                        serial_code13, serial_code14, serial_code15, serial_code16, serial_code17, serial_code18, 
                        serial_code19, serial_code20, serial_code21, serial_code22, serial_code23, serial_code24
                        FROM label_code WHERE qr_code = :qr_code';

        $serialStmt = $conn->prepare($serialQuery);
        $serialStmt->execute([':qr_code' => $qr_code]);
        $serials = $serialStmt->fetch(PDO::FETCH_ASSOC);

        if ($serials) {
            $insertSerial = "INSERT INTO $main_table 
            (qr_code, qty_input, final_qtyinput, operator_name, shift, asmline, line, assy_code, model_name, kepi_lot, serial_code, board_counter, created_at, board_status, serial_status, prev_boardstatus, prev_serialstatus) 
            VALUES (:qr_code, :qty_input, :final_qtyinput, :operator_name, :shift, :asmline, :line, :assy_code, :model_name, :kepi_lot, :serial_code, :board_counter, :created_at, :board_status, :serial_status, :prev_boardstatus, :prev_serialstatus)";

            $insertStmt = $conn->prepare($insertSerial);

            foreach ($serials as $serial_code) {
                if (!empty($serial_code)) {
                    $insertStmt->execute([
                        ':qr_code' => $qr_code,
                        ':qty_input' => $qty_input,
                        ':final_qtyinput' => $final_qtyinput,
                        ':operator_name' => $operator_name,
                        ':shift' => $shift,
                        ':asmline' => $asmline,
                        ':line' => $line,
                        ':assy_code' => $assy_code,
                        ':model_name' => $model_name,
                        ':kepi_lot' => $kepi_lot,
                        ':serial_code' => $serial_code,
                        ':board_counter' => $board_counter,
                        ':created_at' => $created_at,
                        ':board_status' => 'GOOD',
                        ':serial_status' => 'GOOD',
                        ':prev_boardstatus' => 'GOOD',
                        ':prev_serialstatus' => 'GOOD',
                    ]);
                }
            }
        }

        $boardCountQuery = "SELECT COUNT(*) FROM $main_table WHERE kepi_lot = :kepi_lot AND line = :line";
        $boardCountStmt = $conn->prepare($boardCountQuery);
        $boardCountStmt->execute([':kepi_lot' => $kepi_lot, ':line' => $line]);
        $realTimeBoardCount = (int) $boardCountStmt->fetchColumn();

        $response['status'] = 'success';
        $response['message'] = 'FVI Solderside Process recorded successfully.';
        $response['board_count'] = $realTimeBoardCount;
    } catch (PDOException $e) {
        $response['message'] = 'Error submitting form: '.$e->getMessage();
    }
}

echo json_encode($response);
