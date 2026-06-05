<?php

require_once $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

header('Content-Type: application/json');

if(!isset($_POST['kepi_lot'])) {
    echo json_encode(['success' => false, 'message' => 'Kepi Lot Missing.']);
    exit;
}

$Kepi_lot = trim($_POST['kepi_lot']);

try{
    $tmt = $conn->prepare('SELECT model_name FROM trace_process WHERE kepi_lot = :kepi_lot');
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching data: ' . $e->getMessage()]);
    exit;
}