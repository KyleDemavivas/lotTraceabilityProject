<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'data' => null,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['serialcode']) {
    $serialcode = $_POST['serialcode'];

    $query = 'INSERT INTO repair';
}
