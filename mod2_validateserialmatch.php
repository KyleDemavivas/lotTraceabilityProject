<?php
include 'db_connect.php';

header('Content-Type: application/json');

$response = [
    'valid' => false,
    'message' => 'Invalid request',
    'qr_code' => ''
];

if (isset($_POST['serial_code'])) {
    $serial = trim($_POST['serial_code']);
    $source = $_POST['source'] ?? '';

    try {
        $stmt = $conn->prepare("SELECT qr_code, serial_status FROM mod2_process WHERE serial_code = :serial");
        $stmt->execute([':serial' => $serial]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo json_encode([
                'valid' => false,
                'message' => 'Serial code not found',
                'qr_code' => ''
            ]);
            exit;
        }

        if ($row['serial_status'] === 'NO GOOD') {
            echo json_encode([
                'valid' => false,
                'message' => 'Serial is already tagged as NO GOOD.',
                'qr_code' => $row['qr_code']
            ]);
            exit;
        }

        $qrCodeFromDB = $row['qr_code'];

        if ($source === 'alert' || $source === 'modal') {
            $inputQr = trim($_POST['qr_code'] ?? '');

            if ($inputQr && $inputQr !== $qrCodeFromDB) {
                echo json_encode([
                    'valid' => false,
                    'message' => 'Serial does not match this QR code',
                    'qr_code' => $qrCodeFromDB
                ]);
                exit;
            }
        }

        echo json_encode([
            'valid' => true,
            'message' => '',
            'qr_code' => $qrCodeFromDB
        ]);
        exit;

    } catch (PDOException $e) {
        echo json_encode([
            'valid' => false,
            'message' => 'Database error',
            'qr_code' => ''
        ]);
        exit;
    }
}
