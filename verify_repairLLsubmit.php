<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';
header('Content-Type: application/json');

date_default_timezone_set('Asia/Manila');
$created_at_ll = date('Y-m-d H:i:s');
$judgement = isset($_POST['judgement_ll']) ? $_POST['judgement_ll'] : '';
$serial_code = isset($_POST['serial_code']) ? $_POST['serial_code'] : '';
$status = 'VERIFIED';
$verifier = isset($_POST['verified_ll']) ? $_POST['verified_ll'] : '';
$defect = isset($_POST['defect']) ? $_POST['defect'] : '';

if ($judgement === 'GOOD') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!$serial_code) {
            $response = ['success' => false, 'message' => 'Serial code is required.'];
        } else {
            try {
                $stmt = $conn->prepare('UPDATE repair_master SET status = :status, ll_verified = :ll_verified WHERE serial_code = :serial_code AND defect = :defect');
                $stmt->bindParam(':serial_code', $serial_code);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':ll_verified', $verifier);
                $stmt->bindParam(':defect', $defect);
                $stmt->execute();
                $response = ['success' => true, 'message' => 'Repair request verified.'];
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => $e->getMessage()];
            }
        }
    } else {
        $response = ['success' => false, 'message' => 'Invalid Request Method.'];
    }
} else {
    try {
        $stmt = $conn->prepare('DELETE FROM repair_master WHERE serial_code = :serial_code');
        $stmt->bindParam(':serial_code', $serial_code);
        $stmt->execute();
        $response = ['success' => true, 'message' => 'Repair request rejected and deleted.'];
    } catch (PDOException $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }
}

echo json_encode($response);
