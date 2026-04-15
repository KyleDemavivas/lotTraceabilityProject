<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json;');

$response = [
    'success' => false,
    'status' => 500,
    'message' => 'Database Error.',
    'data' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['process'] === 'repair' ? 'll' : 'mod';
    $message = $status === 'll' ? 'Line Leader' : 'Modificator';

    try {
        $serialcode = $_POST['serialcode'];

        $query = "UPDATE repair_boardanalysis SET status = '".$status."' WHERE serialcode = :serialcode";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':serialcode', $serialcode);
        $stmt->execute();
        $response = [
            'success' => true,
            'status' => 200,
            'message' => 'Board successfully sent for '.$message.' Verification.',
            'data' => null,
        ];
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'status' => 400,
            'message' => $e->getMessage(),
            'data' => null,
        ];
    }
}

echo json_encode($response);
