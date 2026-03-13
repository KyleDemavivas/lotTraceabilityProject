<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';
assert(isset($conn));
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Something went wrong.', 'data' => 'Server Error'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $serial_code = (string) strtoupper(trim($_POST['serial_code']));

        if (!empty($_POST['action']) && $_POST['action'] === 'CANCEL') {
            $qry = 'DELETE FROM repair_master WHERE serial_code = :serial_code';
            $stmt = $conn->prepare($qry);
            $stmt->execute([
                ':serial_code' => $serial_code,
            ]);
            if ($stmt->rowCount() === 0) {
                $response = ['success' => false, 'message' => 'Database Error, serial code is not found in database.'];
                echo json_encode($response);
                exit;
            }
            $response = ['success' => true, 'message' => 'Scrapping Canceled'];
            echo json_encode($response);
            exit;
        } else {
            $qry = "UPDATE repair_master 
            SET status = 'SCRAP', ll_verified = 'SCRAP', process_lead = 'SCRAP', repairable = 'SCRAP'  
            WHERE serial_code = :serial_code";
        }

        $defect = (string) strtoupper(trim($_POST['defect']));
        $location = (string) strtoupper(trim($_POST['location']));

        $stmt = $conn->prepare($qry);
        $stmt->execute([':serial_code' => $serial_code]);

        if ($stmt->rowCount() === 0) {
            $response = ['success' => false, 'message' => 'Database Error, serial code is not found in database.'];
            echo json_encode($response);
            exit;
        }

        $response = ['success' => true, 'message' => 'Data submitted successfully.'];
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => $e->getMessage()];
    }
} else {
    $response = ['success' => false, 'message' => 'Invalid request method.'];
}

echo json_encode($response);
