<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type:application/json;');

$response = [
    'success' => false,
    'message' => 'Database Error.',
    'status' => 500,
    'data' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $serialcode = $_POST['serialcode'];

        $query = "UPDATE repair_boardanalysis SET status = 'mod' WHERE serialcode = :serialcode";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':serialcode', $serialcode);
        $stmt->execute();

        $response = [
            'success' => true,
            'message' => 'Board sent for verification.',
            'status' => 200,
            'data' => null,
        ];
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => $e->getMessage(),
            'status' => 400,
            'data' => null,
        ];
    }
}

echo json_encode($response);
