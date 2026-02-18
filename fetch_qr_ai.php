<?php
include 'db_connect.php';

header('Content-Type: application/json');
error_reporting(0);

if (!isset($_POST['qr_code'])) {
    echo json_encode(['valid' => true, 'message' => 'QR not provided']);
    exit;
}

$qr_code = strtoupper(trim($_POST['qr_code']));

try {

    $stmt2 = $conn->prepare("SELECT COUNT(*) FROM ai_process WHERE qr_code = :qr_code AND (board_status = 'HOLD' AND serial_status = 'NO GOOD')");
    $stmt2->execute([':qr_code' => $qr_code]);
    $holdCount = $stmt2->fetchColumn();

    if ($holdCount > 0) {
        echo json_encode(['success' => false, 'message' => 'This QR Code is currently on HOLD and cannot be processed.']);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'QR Code is valid',
        'status' => 'GOOD'
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'valid' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
