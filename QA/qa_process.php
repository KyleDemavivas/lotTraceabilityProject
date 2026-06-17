<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];
date_default_timezone_set('Asia/Manila');
$created_at = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = 'INSERT INTO qa_process (kepi_lot, serial_code, inspection_method, line, shift, location, defect_code, severity, operator_id, created_at, sample_size, lot_quantity, model, status, lot_result) 
                VALUES (:kepi_lot, :serial_code, :inspection_method, :line, :shift, :location, :defect_code, :severity, :operator_id, :created_at, :sample_size, :lot_qty, :model, :status, :lot_result)';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':kepi_lot'          => $_POST['kepi_lot'] ?? '',
            ':serial_code'       => $_POST['serial_code'] ?? '',
            ':inspection_method' => $_POST['inspection_method'] ?? '',
            ':line'              => $_POST['line'] ?? '',
            ':shift'             => $_POST['shift'] ?? '',
            ':location'          => $_POST['location'] ?? '',
            ':defect_code'       => !empty($_POST['defect_code']) ? $_POST['defect_code'] : null,
            ':severity'          => !empty($_POST['severity']) ? $_POST['severity'] : null,
            ':operator_id'       => $_POST['operator_id'] ?? '',
            ':created_at'        => $created_at,
            ':sample_size'       => $_POST['sample_size'] ?? '',
            ':lot_qty'           => $_POST['lot_qty'] ?? '',
            ':model'             => $_POST['model'] ?? '',
            ':status'            => $_POST['status'] ?? 'GOOD',
            ':lot_result'        => $_POST['lot_result'] ?? 'ACCEPT',
        ]);

        $response['status'] = 'success';
        $response['message'] = 'QA Process record successfully inserted.';
    } catch (PDOException $e) {
        $response['message'] = 'Database error: '.$e->getMessage();
    }
}

echo json_encode($response);
exit;