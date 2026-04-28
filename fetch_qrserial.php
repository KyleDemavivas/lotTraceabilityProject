<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

if (isset($_POST['serial_code'])) {
    $serial_code = $_POST['serial_code'];

    $query = 'SELECT qr_code FROM vi_process WHERE serial_code = :serial_code';
    $stmt = $conn->prepare($query);
    $stmt->execute([':serial_code' => $serial_code]);

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['success' => true, 'qr_code' => $row['qr_code']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Serial code not found']);
    }
}
