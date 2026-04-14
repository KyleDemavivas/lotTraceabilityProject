<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json;');

$response = [
    'success' => false,
    'message' => 'Database Error.',
    'data' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $serialcode = $_POST['serialcode'];

        $query = "UPDATE repair_boardanalysis SET status = 'll' WHERE serialcode = :serialcode";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':serialcode', $serialcode);
        $stmt->execute();
        $response = [
            'success' => true,
            'message' => 'Board successfully sent for Line Leader Verification.',
            'data' => null,
        ];
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => $e->getMessage(),
            'data' => null,
        ];
    }
}

echo json_encode($response);
