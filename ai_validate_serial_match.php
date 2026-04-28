<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');

$response = [
    'valid' => false,
    'message' => 'Invalid request',
    'qr_code' => '',
];

if (isset($_POST['serial_code'])) {
    $serial = trim($_POST['serial_code']);
    $source = $_POST['source'] ?? '';

    try {
        $stmt = $conn->prepare('SELECT qr_code FROM ai_process WHERE serial_code = :serial');
        $stmt->execute([':serial' => $serial]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $qrCodeFromDB = $row['qr_code'];

            if ($source === 'alert' || $source === 'modal') {
                $inputQr = trim($_POST['qr_code'] ?? '');
                if ($inputQr && $inputQr !== $qrCodeFromDB) {
                    $response = [
                        'valid' => false,
                        'message' => 'Serial does not match this QR code',
                        'qr_code' => $qrCodeFromDB,
                    ];
                } else {
                    $response = [
                        'valid' => true,
                        'message' => '',
                        'qr_code' => $qrCodeFromDB,
                    ];
                }
            } else {
                $response = [
                    'valid' => true,
                    'message' => '',
                    'qr_code' => $qrCodeFromDB,
                ];
            }
        } else {
            $response = [
                'valid' => false,
                'message' => 'Serial code not found',
                'qr_code' => '',
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'valid' => false,
            'message' => 'Database error: '.$e->getMessage(),
            'qr_code' => '',
        ];
    }
}

echo json_encode($response);
