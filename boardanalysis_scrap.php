<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json;');

$response = [
    'success' => false,
    'status' => 500,
    'message' => 'Database Error.',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serialcode = $_POST['serialcode'];

    $query = "UPDATE repair_boardanalysis SET status = 'scrap' WHERE serialcode = :serialcode";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':serialcode', $serialcode);
    $stmt->execute();

    $response = [
        'success' => true,
        'status' => 200,
        'message' => 'Board marked as scrap.',
    ];
}
echo json_encode($response);
