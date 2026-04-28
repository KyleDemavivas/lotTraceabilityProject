<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$response = [
    'valid' => false,
    'message' => 'Invalid request',
    'qr_code' => '',
];

try {
    $origin = $_POST['origin'] ?? '';
    if (empty($origin)) {
        throw new Exception('Origin is NULL.');
    }
    $main_table = $origin === 'main' ? 'fviss_process' : 'fviss_batchlot';

    if (isset($_POST['serial_code'])) {
        $serial = trim($_POST['serial_code']);
        $source = $_POST['source'] ?? '';

        if (in_array($source, ['alert', 'modal', 'manual'])) {
            $stmt = $conn->prepare("SELECT qr_code FROM $main_table WHERE serial_code = :serial");
            $stmt->execute([':serial' => $serial]);
            $qr = $stmt->fetchColumn();

            if ($qr) {
                $response['valid'] = true;
                $response['message'] = '';
                $response['qr_code'] = $qr;
            } else {
                $response['valid'] = false;
                $response['message'] = 'Serial does not match any record.';
            }
        } else {
            $response['valid'] = true;
            $response['message'] = '';
            $response['qr_code'] = '';
        }
    }
} catch (Throwable $e) {
    $response['valid'] = false;
    $response['message'] = 'Database error: '.$e->getMessage();
}

echo json_encode($response);
