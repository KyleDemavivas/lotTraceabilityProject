<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $qr_code = strtoupper($_POST['qr_code']);
        $operator_name = $_POST['operator_name'];
        $shift = $_POST['shift'];
        $line = $_POST['line'];
        $assy_code = strtoupper($_POST['assy_code']);
        $model_name = strtoupper($_POST['model_name']);
        $kepi_lot = strtoupper($_POST['kepi_lot']);
        $serial_qty = $_POST['serial_qty'];
        $qr_count = $_POST['qr_count'];
        $qty_input = $_POST['qty_input'];

        date_default_timezone_set('Asia/Manila');
        $created_at = date('Y-m-d H:i:s');

        $checkQuery = 'SELECT spa_process, mounter_process FROM trace_process WHERE qr_code = :qr_code';
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([':qr_code' => $qr_code]);
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            $response['message'] = 'QR Code not found..';
            echo json_encode($response);
            exit;
        }

        if ($result['spa_process'] !== 'GOOD') {
            $response['message'] = 'QR Code rejected. SPA process is not GOOD.';
            echo json_encode($response);
            exit;
        }

        if ($result['mounter_process'] === 'GOOD') {
            $response['message'] = 'QR Code already exists..';
            echo json_encode($response);
            exit;
        }

        $updateMounter = "UPDATE trace_process SET mounter_process = 'GOOD' WHERE qr_code = :qr_code";
        $updateStmt = $conn->prepare($updateMounter);
        $updateStmt->execute([':qr_code' => $qr_code]);

        $finalQtyQuery = 'SELECT TOP 1 final_qtyinput FROM mounter_process WHERE kepi_lot = :kepi_lot ORDER BY created_at DESC';
        $finalQtyStmt = $conn->prepare($finalQtyQuery);
        $finalQtyStmt->execute([
            ':kepi_lot' => $kepi_lot]);
        $previous_final_qty = (int) ($finalQtyStmt->fetchColumn() ?: 0);
        $final_qtyinput = $previous_final_qty + (int) $qty_input;

        $query = 'INSERT INTO mounter_process (qr_code, qty_input, final_qtyinput, operator_name, shift, line, assy_code, model_name, kepi_lot, created_at, mounter_status) 
                  VALUES (:qr_code, :qty_input, :final_qtyinput, :operator_name, :shift, :line, :assy_code, :model_name, :kepi_lot, :created_at, :mounter_status)';
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':qr_code' => $qr_code,
            ':qty_input' => $qty_input,
            ':final_qtyinput' => $final_qtyinput,
            ':operator_name' => $operator_name,
            ':shift' => $shift,
            ':line' => $line,
            ':assy_code' => $assy_code,
            ':model_name' => $model_name,
            ':kepi_lot' => $kepi_lot,
            ':created_at' => $created_at,
            ':mounter_status' => 'GOOD',
        ]);

        $response['status'] = 'success';
        $response['message'] = 'Mounter Process recorded and Trace Process updated successfully.';
        $response['final_qtyinput'] = $final_qtyinput;
        // $response['final_qtyinput'] = $final_qtyinput;
    } catch (PDOException $e) {
        $response['status'] = false;
        $response['data'] = 'Database Error';
        $response['message'] = 'A database error has occured.';
    }
}

echo json_encode($response);
