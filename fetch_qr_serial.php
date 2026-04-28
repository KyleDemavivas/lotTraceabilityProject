<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

$response = ['success' => false, 'qr_code' => '', 'message' => ''];

if (isset($_POST['serial_code'])) {
    $serial_code = trim($_POST['serial_code']);

    try {
        $stmt = $conn->prepare('SELECT qr_code FROM mounter_process WHERE serial_code = :serial_code');
        $stmt->execute([':serial_code' => $serial_code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $response['success'] = true;
            $response['qr_code'] = $row['qr_code'];
        } else {
            $response['message'] = 'Serial code not found';
        }
    } catch (PDOException $e) {
        $response['message'] = 'Database error: '.$e->getMessage();
    }
}

echo json_encode($response);
