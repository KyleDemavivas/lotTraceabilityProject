<?php
include 'db_connect.php';
header('Content-Type: application/json');

$response = [
    'valid' => false,
    'message' => 'Invalid request',
    'qr_code' => ''
];

try {
    if (isset($_POST['serial_code'])) {
        $serial = trim($_POST['serial_code']);
        $source = $_POST['source'] ?? '';

        if (in_array($source, ['alert', 'modal', 'manual'])) {

            $stmt = $conn->prepare("SELECT qr_code FROM fviss_process WHERE serial_code = :serial");
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
} catch (PDOException $e) {
    $response['valid'] = false;
    $response['message'] = 'Database error: ' . $e->getMessage();
}

echo json_encode($response);
