<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => '', 'board_count' => 0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sourcePage = $_POST['source_page'] ?? '';

        if (!in_array($sourcePage, ['main', 'batchlot'])) {
            throw new Exception('Unknown source page.');
        }

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

        // Determine which table to use
        $main_table = $sourcePage === 'main' ? 'partside2_process' : 'partside2_batchlot';
        $main_table2 = $sourcePage === 'main' ? 'partside_process' : 'partside_batchlot';
        $counter_table = $main_table; // both use same table for board_counter

        // Check if NO GOOD
        $stmtNG = $conn->prepare("SELECT serial_status FROM $main_table2 WHERE serial_code = :serial_code");
        $stmtNG->execute([':serial_code' => $serial_code]);
        $fvissStatus = $stmtNG->fetchColumn();

        if ($fvissStatus === 'NO GOOD') {
            throw new Exception('This serial is already tagged as NO GOOD and cannot be processed.');
        }

        // Board counter
        $stmt = $conn->prepare("SELECT TOP 1 board_counter FROM $main_table WHERE kepi_lot = :kepi_lot AND line = :line ORDER BY id DESC");
        $stmt->execute([':kepi_lot' => $kepi_lot, ':line' => $line]);
        $board_counter = ((int) $stmt->fetchColumn()) + 1 ?: 1;

        // Check trace_process
        $stmt = $conn->prepare('SELECT partside2_process FROM trace_process WHERE serial_code = :serial_code');
        $stmt->execute([':serial_code' => $serial_code]);
        $viProcess = $stmt->fetchColumn();

        if ($viProcess === 'GOOD') {
            throw new Exception('Partside Process already has data.');
        }

        if ($viProcess !== false) {
            $stmt = $conn->prepare("UPDATE trace_process SET partside2_process = 'GOOD' WHERE serial_code = :serial_code");
            $stmt->execute([':serial_code' => $serial_code]);
        }

        // Final qtyinput
        $stmt = $conn->prepare("SELECT COALESCE(SUM(CAST(qty_input AS INT)),0) FROM $main_table2 WHERE kepi_lot = :kepi_lot AND line = :line");
        $stmt->execute([':kepi_lot' => $kepi_lot, ':line' => $line]);
        $final_qtyinput = (int) $stmt->fetchColumn() + $qty_input;

        // Insert into correct table
        $stmt = $conn->prepare("INSERT INTO $main_table 
            (qr_code, serial_code, qty_input, final_qtyinput, operator_name, shift, asmline, line, assy_code, model_name, kepi_lot, board_counter, created_at, board_status, serial_status, prev_boardstatus, prev_serialstatus) 
            VALUES 
            (:qr_code, :serial_code, :qty_input, :final_qtyinput, :operator_name, :shift, :asmline, :line, :assy_code, :model_name, :kepi_lot, :board_counter, :created_at,'GOOD','GOOD','GOOD','GOOD')");

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
        $response['message'] = 'Partside Process recorded successfully.';
        $response['board_count'] = (int) $stmt->fetchColumn();
    } catch (Throwable $e) {
        $response['message'] = $e->getMessage();
    }
}

// Always echoes, no matter what happens
echo json_encode($response);
