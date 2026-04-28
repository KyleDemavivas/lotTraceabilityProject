<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
assert(isset($conn));
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Something went wrong.', 'data' => 'Server Error'];
date_default_timezone_set('Asia/Manila');
$created_at = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $qr_code = $_POST['qr_code'] ?? '';
        $model_name = $_POST['model_name'] ?? '';
        $assy_code = $_POST['assy_code'] ?? '';
        $kepi_lot = $_POST['kepi_lot'] ?? '';
        $shift = $_POST['shift'] ?? '';
        $line = $_POST['line'] ?? '';
        $board_number = $_POST['board_number'] ?? '';
        $serial_code = $_POST['serial_code'] ?? '';
        $defect = $_POST['defect'] ?? 'SCRAP';
        $operator_name = $_POST['operator_name'] ?? '';
        $location = $_POST['location'] ?? 'N/A';
        $process_location = $_POST['process_location'] ?? 'MOD 1';
        $repaired_by = $_POST['repaired_by'] ?? 'N/A';
        $action_rp = $_POST['action_rp'] ?? 'N/A';
        $lcr_reading = $_POST['lcr_reading'] ?? 'N/A';
        $parts_code = $_POST['parts_code'] ?? 'N/A';
        $parts_lot = $_POST['parts_lot'] ?? 'N/A';
        $unitmeasurement = $_POST['unitmeasurement'] ?? 'N/A';
        $batchlot = $_POST['batchlot'] ?? 'N/A';
        $repairable = 'SCRAP';

        $query = "INSERT INTO repair_master
        (qr_code, model_name, assy_code, kepi_lot, shift, line, board_number, 
        serial_code, defect, operator_name, location, process_location, 
        repaired_by, action_rp, lcr_reading, parts_code, parts_lot, 
        unitmeasurement, batchlot, repairable, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'PENDING', ?)";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            $qr_code,
            $model_name,
            $assy_code,
            $kepi_lot,
            $shift,
            $line,
            $board_number,
            $serial_code,
            $defect,
            $operator_name,
            $location,
            $process_location,
            $repaired_by,
            $action_rp,
            $lcr_reading,
            $parts_code,
            $parts_lot,
            $unitmeasurement,
            $batchlot,
            $repairable,
            $created_at,
        ]);

        $response = ['success' => true, 'message' => 'Data submitted successfully.'];
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request method.'];
}

echo json_encode($response);
