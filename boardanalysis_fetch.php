<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$query = 'SELECT BoardSerial, ModelName, Process, DateTime FROM board_analysis';
$stmt = $conn->prepare($query);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($results);
