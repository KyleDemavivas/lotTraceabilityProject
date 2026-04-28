<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];
date_default_timezone_set('Asia/Manila');
$created_at = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $repair_status = ($_POST['batchlot'] === 'YES') ? 'GOOD' : 'GOOD';

        $sql = 'INSERT INTO ai_repair (qr_code, model_name, assy_code, kepi_lot, serial_code, operator_name, repaired_by, action_rp, lcr_reading, parts_code, parts_lot, unitmeasurement, batchlot, repairable, shift, line, defect, location, board_number, repair_status, judgement_ll, created_at) 
                VALUES (:qr_code, :model_name, :assy_code, :kepi_lot, :serial_code, :operator_name, :repaired_by, :action_rp, :lcr_reading, :parts_code, :parts_lot, :unitmeasurement, :batchlot, :repairable, :shift, :line, :defect, :location, :board_number, :repair_status, :judgement_ll, :created_at)';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':qr_code' => $_POST['qr_code'],
            ':model_name' => $_POST['model_name'],
            ':assy_code' => $_POST['assy_code'],
            ':kepi_lot' => $_POST['kepi_lot'],
            ':serial_code' => $_POST['serial_code'],
            ':operator_name' => $_POST['operator_name'],
            ':repaired_by' => $_POST['repaired_by'],
            ':action_rp' => $_POST['action_rp'],
            ':lcr_reading' => $_POST['lcr_reading'],
            ':parts_code' => $_POST['parts_code'],
            ':parts_lot' => $_POST['parts_lot'],
            ':unitmeasurement' => $_POST['unitmeasurement'],
            ':batchlot' => $_POST['batchlot'],
            ':repairable' => $_POST['repairable'],
            ':shift' => $_POST['shift'],
            ':line' => $_POST['line'],
            ':defect' => $_POST['defect'],
            ':location' => $_POST['location'],
            ':board_number' => $_POST['board_number'],
            ':repair_status' => $repair_status,
            ':judgement_ll' => 'PENDING',
            ':created_at' => $created_at,
        ]);

        //  Update vp.serial_status to ONGOING REPAIR
        $updateSql = "UPDATE ai_process SET serial_status = 'ONGOING REPAIR' WHERE qr_code = :qr_code AND serial_code = :serial_code";

        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->execute([
            ':qr_code' => $_POST['qr_code'],
            ':serial_code' => $_POST['serial_code'],
        ]);

        $response['status'] = 'success';
        $response['message'] = 'Repair Process successfully submitted. Serial set to ONGOING REPAIR.';
    } catch (PDOException $e) {
        $response['message'] = 'Database error: '.$e->getMessage();
    }
}

echo json_encode($response);
exit;
