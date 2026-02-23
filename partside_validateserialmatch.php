<?php
include 'db_connect.php';
header('Content-Type: application/json');

$response = [
    'valid'   => false,
    'message' => '',
    'qr_code' => ''
];

try {

    $origin = $_POST['origin'] ?? '';

    if(empty($origin)) {
       throw new Exception('Origin is NULL.');
    }

    $main_table = $origin === 'main' ? 'partside_process' : 'partside_batchlot';

    if (empty($_POST['serial_code'])) {
        $response['message'] = 'Serial code is required.';
        echo json_encode($response);
        exit;
    }

    $serial = strtoupper(trim($_POST['serial_code']));
    $source = $_POST['source'] ?? '';
    $qrFromClient = $_POST['qr_code'] ?? '';

    $stmt = $conn->prepare("SELECT qr_code, serial_status FROM $main_table WHERE serial_code = :serial");
    $stmt->execute([':serial' => $serial]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $response['valid'] = false;
        $response['message'] = 'Serial code does not exist in the system.';
        echo json_encode($response);
        exit;
    }

    if ($row['serial_status'] === 'NO GOOD') {
        $response['valid'] = false;
        $response['message'] = 'Serial is already tagged as NO GOOD.';
        echo json_encode($response);
        exit;
    }

    $qrFromDb = $row['qr_code'];

    if (in_array($source, ['manual', 'modal'], true)) {
        $response['valid'] = true;
        $response['qr_code'] = $qrFromDb;
        echo json_encode($response);
        exit;
    }

    if ($source === 'alert') {
        if ($qrFromClient && $qrFromClient !== $qrFromDb) {
            $response['valid'] = false;
            $response['message'] = 'Serial does not belong to this QR code.';
        } else {
            $response['valid'] = true;
            $response['qr_code'] = $qrFromDb;
        }
        echo json_encode($response);
        exit;
    }

    $response['valid'] = true;
    $response['qr_code'] = $qrFromDb;
} catch (Throwable $e) {
    $response['valid'] = false;
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
exit;
