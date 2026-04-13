<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'data' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['serialcode']) {
    $response = [
        'success' => true,
        'message' => 'Data submitted successfully.',
        'data' => $_POST['serialcode'],
    ];
} else {
    $response = [
        'success' => false,
        'message' => 'Invalid request. Serial code is required.',
        'data' => null,
    ];
}

echo json_encode($response);
