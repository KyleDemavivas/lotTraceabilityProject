<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $qr_code = strtoupper($_POST['qr_code'] ?? '');
        $operator_name = $_POST['operator_name'] ?? '';
        $shift = $_POST['shift'] ?? '';
        $line = $_POST['line'] ?? '';
        $angle = $_POST['angle'] ?? '';
        $assy_code = strtoupper($_POST['assy_code'] ?? '');
        $model_name = strtoupper($_POST['model_name'] ?? '');
        $kepi_lot = strtoupper($_POST['kepi_lot'] ?? '');
        $serial_qty = $_POST['serial_qty'] ?? 0;
        $qr_count = $_POST['qr_count'] ?? 0;
        $qty_input = $_POST['qty_input'] ?? 0;

        if (empty($qr_code) || empty($operator_name) || empty($shift) || empty($line) || empty($assy_code) || empty($model_name) || empty($kepi_lot)) {
            $response['message'] = 'Missing required fields.';
            echo json_encode($response);
            exit;
        }

        date_default_timezone_set('Asia/Manila');
        $created_at = date('Y-m-d H:i:s');

        $counterQuery = 'SELECT board_counter FROM ai_process WHERE kepi_lot = :kepi_lot AND line = :line ORDER BY id DESC';
        $counterStmt = $conn->prepare($counterQuery);
        $counterStmt->execute([':kepi_lot' => $kepi_lot, ':line' => $line]);
        $lastCounter = $counterStmt->fetchColumn();
        $board_counter = ($lastCounter) ? ($lastCounter + 1) : 1;

        $checkQuery = 'SELECT ai_process FROM trace_process WHERE qr_code = :qr_code';
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([':qr_code' => $qr_code]);
        $aiProcess = $checkStmt->fetchColumn();

        if ($aiProcess !== false) {
            if ($aiProcess === 'GOOD') {
                $response['message'] = 'AI Process already has data.';
                echo json_encode($response);
                exit;
            } else {
                $updateMounter = "UPDATE trace_process SET ai_process = 'GOOD' WHERE qr_code = :qr_code";
                $updateStmt = $conn->prepare($updateMounter);
                $updateStmt->execute([':qr_code' => $qr_code]);
            }
        }

        $query = "SELECT COUNT(process_location) FROM repair_master WHERE qr_code = :qr_code AND process_location = 'AI'";
        $stmt = $conn->prepare($query);
        $stmt->execute([':qr_code' => $qr_code]);
        $repaired = (int) $stmt->fetchColumn();

        if ($repaired > 0) {
            $finalQtyStmt = $conn->prepare('SELECT TOP 1 final_qtyinput FROM ai_process WHERE kepi_lot = :kepi_lot ORDER BY created_at DESC');
            $finalQtyStmt->execute([':kepi_lot' => $kepi_lot]);
            $final_qtyinput = (int) ($finalQtyStmt->fetchColumn() ?: 0);
        } else {
            $finalQtyStmt = $conn->prepare('SELECT TOP 1 final_qtyinput FROM ai_process WHERE kepi_lot = :kepi_lot ORDER BY created_at DESC');
            $finalQtyStmt->execute([':kepi_lot' => $kepi_lot]);
            $previous_final_qty = (int) ($finalQtyStmt->fetchColumn() ?: 0);
            $final_qtyinput = $previous_final_qty + (int) $qty_input;
        }

        $serialQuery = 'SELECT serial_code1, serial_code2, serial_code3, serial_code4, serial_code5, serial_code6, 
                        serial_code7, serial_code8, serial_code9, serial_code10, serial_code11, serial_code12, 
                        serial_code13, serial_code14, serial_code15, serial_code16, serial_code17, serial_code18, 
                        serial_code19, serial_code20, serial_code21, serial_code22, serial_code23, serial_code24
                        FROM label_code WHERE qr_code = :qr_code';
        $serialStmt = $conn->prepare($serialQuery);
        $serialStmt->execute([':qr_code' => $qr_code]);
        $serials = $serialStmt->fetch(PDO::FETCH_ASSOC);

        if ($serials) {
            $insertSerial = 'INSERT INTO ai_process (qr_code, qty_input, final_qtyinput, operator_name, shift, line, angle, assy_code, model_name, kepi_lot, serial_code, created_at, board_counter, board_status, serial_status, prev_boardstatus, prev_serialstatus) 
            VALUES (:qr_code, :qty_input, :final_qtyinput, :operator_name, :shift, :line, :angle, :assy_code, :model_name, :kepi_lot, :serial_code, :created_at, :board_counter, :board_status, :serial_status, :prev_boardstatus, :prev_serialstatus)';
            $insertStmt = $conn->prepare($insertSerial);

            foreach ($serials as $code) {
                if (!empty($code)) {
                    $insertStmt->execute([
                        ':qr_code' => $qr_code,
                        ':qty_input' => $qty_input,
                        ':final_qtyinput' => $final_qtyinput,
                        ':operator_name' => $operator_name,
                        ':shift' => $shift,
                        ':line' => $line,
                        ':angle' => $angle,
                        ':assy_code' => $assy_code,
                        ':model_name' => $model_name,
                        ':kepi_lot' => $kepi_lot,
                        ':serial_code' => $code,
                        ':created_at' => $created_at,
                        ':board_counter' => $board_counter,
                        ':board_status' => 'GOOD',
                        ':serial_status' => 'GOOD',
                        ':prev_boardstatus' => 'GOOD',
                        ':prev_serialstatus' => 'GOOD',
                    ]);
                }
            }
        }

        $response['status'] = 'success';
        $response['message'] = 'AI Process recorded successfully.';
        $response['boardCount'] = $board_counter;
        $response['lastBoardCount'] = $lastCounter + 1;
        $response['final_qtyinput'] = $final_qtyinput;
    } catch (PDOException $e) {
        $response['success'] = false;
        $response['data'] = 'Insert Unsuccessful.';
        $response['message'] = 'Insert of AI Data is unsuccussful.';
    }
}

echo json_encode($response);
