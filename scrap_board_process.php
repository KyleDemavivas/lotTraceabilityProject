<?php

session_start();
header('Content-Type: application/json');
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

try {
    if (!isset($_SESSION['user_namefl'])) {
        throw new Exception('Unauthorized access. Please log in again.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method.');
    }

    date_default_timezone_set('Asia/Manila');
    $created_at = date('Y-m-d H:i:s');

    $qr_code = strtoupper(trim($_POST['qr_code'] ?? ''));
    $model_name = strtoupper(trim($_POST['model_name'] ?? ''));
    $assy_code = strtoupper(trim($_POST['assy_code'] ?? ''));
    $kepi_lot = strtoupper(trim($_POST['kepi_lot'] ?? ''));
    $serial_code = strtoupper(trim($_POST['serial_code'] ?? ''));
    $qty = intval($_POST['qty'] ?? 0);
    $process = strtoupper(trim($_POST['process'] ?? ''));
    $operator_name = strtoupper(trim($_POST['operator_name'] ?? ''));
    $line = strtoupper(trim($_POST['line'] ?? ''));
    $status = strtoupper(trim($_POST['status'] ?? ''));
    $shift = strtoupper(trim($_POST['shift'] ?? ''));
    $analysis = strtoupper(trim($_POST['analysis'] ?? ''));

    $defects = $_POST['defect'] ?? [];
    $locations = $_POST['location'] ?? [];

    if (!is_array($defects)) {
        $defects = [$defects];
    }
    if (!is_array($locations)) {
        $locations = [$locations];
    }

    if (empty($qr_code) || empty($serial_code) || empty($operator_name)) {
        throw new Exception('QR Code, Serial Code, and Operator Name are required.');
    }

    if (empty($defects)) {
        throw new Exception('At least one defect is required.');
    }

    $defects_str = strtoupper(implode(', ', array_filter(array_map('trim', $defects))));
    $locations_str = strtoupper(implode(', ', array_filter(array_map('trim', $locations))));

    $conn->beginTransaction();

    $stmt = $conn->prepare('INSERT INTO scrap_board (qr_code, model_name, assy_code, kepi_lot, serial_code, qty, process, operator_name, line, status, shift, defects, locations, analysis, created_at)
            VALUES (:qr_code, :model_name, :assy_code, :kepi_lot, :serial_code, :qty, :process, :operator_name, :line, :status, :shift, :defects, :locations, :analysis,  :created_at)');

    $stmt->execute([
        ':qr_code' => $qr_code,
        ':model_name' => $model_name,
        ':assy_code' => $assy_code,
        ':kepi_lot' => $kepi_lot,
        ':serial_code' => $serial_code,
        ':qty' => $qty,
        ':process' => $process,
        ':operator_name' => $operator_name,
        ':line' => $line,
        ':status' => $status,
        ':shift' => $shift,
        ':defects' => $defects_str,
        ':locations' => $locations_str,
        ':analysis' => $analysis,
        ':created_at' => $created_at,
    ]);

    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Scrap board data saved successfully.',
    ]);
} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
    ]);
}
