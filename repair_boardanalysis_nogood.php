<?php

header('Content-Type: application/json;');
require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

$response = [
    'success' => false,
    'status' => 500,
    'message' => 'Database error.',
    'data' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Serial = $_POST['serialcode'];

    try {
        $query = 'UPDATE repair_boardanalysis SET status = NULL WHERE serialcode = ?';
        $stmt = $conn->prepare($query);
        $stmt->bindValue(1, $Serial);
        $stmt->execute();

        $response = [
            'success' => true,
            'status' => 200,
            'message' => 'Success!',
            'data' => $Serial,
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
