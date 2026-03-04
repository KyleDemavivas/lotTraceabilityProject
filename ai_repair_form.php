<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_code']) && isset($_POST['serial_code']) && isset($_POST['defect']) && isset($_POST['location']) && isset($_POST['process_origin']) && isset($_POST['board_number']) && isset($_POST['operator_name']) && isset($_POST['serial_status']) && isset($_POST['board_status']) && isset($_POST['line']) && isset($_POST['shift']) && isset($_POST['model_name']) && isset($_POST['assy_code']) && isset($_POST['kepi_lot'])) {
    $sql = 'INSERT INTO repair_data (qr_code, serial_code, defect, location, process_origin, board_number, operator_name, serial_status, board_status, line, shift, model_name, assy_code, kepi_lot, created_at) 
    VALUES (:qr_code, :serial_code, :defect, :location, :process_origin, :board_number, :operator_name, :serial_status, :board_status, :line, :shift, :model_name, :assy_code, :kepi_lot, :created_at)';

    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute([
            ':qr_code' => $_POST['qr_code'],
            ':serial_code' => $_POST['serial_code'],
            ':defect' => $_POST['defect'],
            ':location' => $_POST['location'],
            ':process_origin' => $_POST['process_origin'],
            ':board_number' => $_POST['board_number'],
            ':operator_name' => $_POST['operator_name'],
            ':serial_status' => $_POST['serial_status'],
            ':board_status' => $_POST['board_status'],
            ':line' => $_POST['line'],
            ':shift' => $_POST['shift'],
            ':model_name' => $_POST['model_name'],
            ':assy_code' => $_POST['assy_code'],
            ':kepi_lot' => $_POST['kepi_lot'],
            ':created_at' => date('Y-m-d H:i:s'),
        ]);

        echo json_encode(['success' => true, 'message' => 'Insert successful']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Insert failed: '.$e->getMessage()]);
    }
}
