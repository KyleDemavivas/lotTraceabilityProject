<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => '', 'board_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = $_POST['source'] ?? '';
    try {
        if (empty($source)) {
            throw new Exception('Source is NULL.');
        }
        $main_table = $source === 'main' ? 'wi_process' : 'wi_batchlot';
        $process_location =
        $source === 'main' ? 'WI' : 'WI BATCH LOT';

        $qr_code = strtoupper($_POST['qr_code'] ?? '');
        $serial_code = strtoupper($_POST['serial_code'] ?? '');
        $operator_name = $_POST['operator_name'] ?? '';
        $shift = $_POST['shift'] ?? '';
        $asmline = $_POST['asmline'] ?? '';
        $line = $_POST['line'] ?? '';
        $assy_code = strtoupper($_POST['assy_code'] ?? '');
        $model_name = strtoupper($_POST['model_name'] ?? '');
        $kepi_lot = strtoupper($_POST['kepi_lot'] ?? '');
        $qty_input = (int) ($_POST['qty_input'] ?? 0);

        if (
            empty($qr_code) || empty($serial_code) || empty($operator_name)
            || empty($shift) || empty($line) || empty($assy_code)
            || empty($model_name) || empty($kepi_lot)
        ) {
            throw new Exception('Missing required fields.');
        }

        date_default_timezone_set('Asia/Manila');
        $created_at = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("SELECT TOP 1 board_counter FROM $main_table WHERE kepi_lot = :kepi_lot AND line = :line ORDER BY id DESC");
        $stmt->execute([':kepi_lot' => $kepi_lot, ':line' => $line]);
        $board_counter = ((int) $stmt->fetchColumn()) + 1 ?: 1;

        $stmt = $conn->prepare('SELECT wi_process FROM trace_process WHERE serial_code = :serial_code');
        $stmt->execute([':serial_code' => $serial_code]);
        $viProcess = $stmt->fetchColumn();

        if ($viProcess === 'GOOD') {
            throw new Exception('WI Process already has data.');
        }

        if ($viProcess !== false) {
            $stmt = $conn->prepare("UPDATE trace_process SET wi_process = 'GOOD' WHERE serial_code = :serial_code");
            $stmt->execute([':serial_code' => $serial_code]);
        }

        $query = 'SELECT COUNT(process_location) FROM repair_master WHERE serial_code = :serial_code AND process_location = :process_location';
        $stmt = $conn->prepare($query);
        $stmt->execute([':serial_code' => $serial_code, ':process_location' => $process_location]);
        $repaired = (int) $stmt->fetchColumn();

        if ($repaired > 0) {
            $finalQtyStmt = $conn->prepare("SELECT TOP 1 final_qtyinput FROM $main_table WHERE kepi_lot = :kepi_lot ORDER BY created_at DESC");
            $finalQtyStmt->execute([':kepi_lot' => $kepi_lot]);
            $final_qtyinput = (int) ($finalQtyStmt->fetchColumn() ?: 0);
        } else {
            $finalQtyStmt = $conn->prepare("SELECT TOP 1 final_qtyinput FROM $main_table WHERE kepi_lot = :kepi_lot ORDER BY created_at DESC");
            $finalQtyStmt->execute([':kepi_lot' => $kepi_lot]);
            $previous_final_qty = (int) ($finalQtyStmt->fetchColumn() ?: 0);
            $final_qtyinput = $previous_final_qty + (int) $qty_input;
        }

        $stmt = $conn->prepare("INSERT INTO $main_table (qr_code, serial_code, qty_input, final_qtyinput, operator_name, shift, asmline, line, assy_code, model_name, kepi_lot, board_counter, created_at, board_status, serial_status, prev_boardstatus, prev_serialstatus) 
                VALUES (:qr_code, :serial_code, :qty_input, :final_qtyinput, :operator_name, :shift, :asmline, :line, :assy_code, :model_name, :kepi_lot, :board_counter, :created_at,'GOOD','GOOD','GOOD','GOOD')");

        $stmt->execute([
            ':qr_code' => $qr_code,
            ':serial_code' => $serial_code,
            ':qty_input' => $qty_input,
            ':final_qtyinput' => $final_qtyinput,
            ':operator_name' => $operator_name,
            ':shift' => $shift,
            ':asmline' => $asmline,
            ':line' => $line,
            ':assy_code' => $assy_code,
            ':model_name' => $model_name,
            ':kepi_lot' => $kepi_lot,
            ':board_counter' => $board_counter,
            ':created_at' => $created_at,
        ]);

        $stmt = $conn->prepare("SELECT COUNT(*) FROM $main_table WHERE kepi_lot = :kepi_lot AND line = :line");
        $stmt->execute([':kepi_lot' => $kepi_lot, ':line' => $line]);

        $response['status'] = 'success';
        $response['message'] = 'WI Process recorded successfully.';
        $response['board_count'] = (int) $stmt->fetchColumn();
        $response['final_qtyinput'] = $final_qtyinput;
    } catch (Throwable $e) {
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
