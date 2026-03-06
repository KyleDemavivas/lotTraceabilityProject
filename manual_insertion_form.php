<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $qr_code = strtoupper($_POST['qr_code'] ?? '');
        $operator_name = $_POST['operator_name'] ?? '';
        $shift = $_POST['shift'] ?? '';
        $asmline = $_POST['asmline'] ?? '';
        $line = $_POST['line'] ?? '';
        $assy_code = strtoupper($_POST['assy_code'] ?? '');
        $model_name = strtoupper($_POST['model_name'] ?? '');
        $kepi_lot = strtoupper($_POST['kepi_lot'] ?? '');
        $assy_code_mi = strtoupper($_POST['assy_code_mi'] ?? '');
        $model_name_mi = strtoupper($_POST['model_name_mi'] ?? '');
        $kepi_lot_mi = strtoupper($_POST['kepi_lot_mi'] ?? '');
        $serial_qty = (int) ($_POST['serial_qty'] ?? 0);
        $qr_count = (int) ($_POST['qr_count'] ?? 0);
        $qty_input = (int) ($_POST['qty_input'] ?? 0);
        $mi_reason = strtoupper($_POST['mi_reason'] ?? '');
        $mi_remarks = strtoupper($_POST['mi_remarks'] ?? '');
        $asmline = $_POST['asmline'] ?? '';

        if (empty($qr_code) || empty($operator_name) || empty($shift) || empty($line) || empty($assy_code) || empty($model_name) || empty($kepi_lot)) {
            $response['message'] = 'Missing required fields.';
            echo json_encode($response);
            exit;
        }

        if ($assy_code !== $assy_code_mi || $kepi_lot !== $kepi_lot_mi) {
            $response['message'] = 'ASSY Code or LOT No does not match QR Code data.';
            echo json_encode($response);
            exit;
        }

        date_default_timezone_set('Asia/Manila');
        $created_at = date('Y-m-d H:i:s');

        $checkQuery = 'SELECT ai_process, vi_process, mi_process FROM trace_process WHERE qr_code = :qr_code';
        // $checkQuery = "SELECT ai_process, mi_process FROM trace_process WHERE qr_code = :qr_code";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([':qr_code' => $qr_code]);
        $row = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            if ($row['ai_process'] === 'GOOD' || $row['vi_process'] === 'GOOD') {
                // if ($row['ai_process'] === 'GOOD')
                if (strtoupper($row['mi_process']) === 'GOOD') {
                    $response['message'] = 'MI Process already has data.';
                    echo json_encode($response);
                    exit;
                } else {
                    $updateMounter = "UPDATE trace_process SET mi_process = 'GOOD' WHERE qr_code = :qr_code";
                    $updateStmt = $conn->prepare($updateMounter);
                    $updateStmt->execute([':qr_code' => $qr_code]);
                }
            } else {
                $response['message'] = 'VI/AI PROCESS NOT FOUND';
                echo json_encode($response);
                exit;
            }
        } else {
            $response['message'] = 'VI/AI PROCESS NOT FOUND';
            echo json_encode($response);
            exit;
        }

        $finalQtyStmt = $conn->prepare('SELECT final_qtyinput FROM mi_process WHERE kepi_lot = :kepi_lot AND final_qtyinput IS NOT NULL ORDER BY created_at DESC');
        $finalQtyStmt->execute([':kepi_lot' => $kepi_lot]);
        $previous_final_qty = (int) $finalQtyStmt->fetchColumn() ?: 0;

        (int) $final_qtyinput = $previous_final_qty + $qty_input;

        $serialQuery = 'SELECT serial_code1, serial_code2, serial_code3, serial_code4, serial_code5, serial_code6, 
                        serial_code7, serial_code8, serial_code9, serial_code10, serial_code11, serial_code12, 
                        serial_code13, serial_code14, serial_code15, serial_code16, serial_code17, serial_code18, 
                        serial_code19, serial_code20, serial_code21, serial_code22, serial_code23, serial_code24
                        FROM label_code WHERE qr_code = :qr_code';
        $serialStmt = $conn->prepare($serialQuery);
        $serialStmt->execute([':qr_code' => $qr_code]);
        $serials = $serialStmt->fetch(PDO::FETCH_ASSOC);

        if ($serials) {
            $insertSerial = 'INSERT INTO mi_process (qr_code, qty_input, final_qtyinput, operator_name, shift,  asmline, assy_code, model_name, line, kepi_lot, serial_code, mi_reason, mi_remarks, created_at) 
                VALUES (:qr_code, :qty_input, :final_qtyinput, :operator_name, :shift, :asmline, :assy_code, :model_name, :line, :kepi_lot, :serial_code, :mi_reason, :mi_remarks, :created_at)';
            $insertStmt = $conn->prepare($insertSerial);

            foreach ($serials as $serial_code) {
                if (!empty($serial_code)) {
                    $insertStmt->execute([
                        ':qr_code' => $qr_code,
                        ':qty_input' => $qty_input,
                        ':final_qtyinput' => $final_qtyinput,
                        ':operator_name' => $operator_name,
                        ':shift' => $shift,
                        ':line' => $line,
                        ':asmline' => $asmline,
                        ':assy_code' => $assy_code,
                        ':model_name' => $model_name,
                        ':kepi_lot' => $kepi_lot,
                        ':serial_code' => $serial_code,
                        ':mi_reason' => $mi_reason,
                        ':mi_remarks' => $mi_remarks,
                        ':created_at' => $created_at,
                    ]);
                }
            }
        }

        $response['status'] = 'success';
        $response['message'] = 'MI Process recorded successfully.';
        $response['final_qtyinput'] = $final_qtyinput;
    } catch (PDOException $e) {
        $response['message'] = 'Error submitting form: '.$e->getMessage();
    }
}

echo json_encode($response);
