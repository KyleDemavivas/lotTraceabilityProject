<?php
include '../db_connect.php';

$serial_code = $_GET['serial_code'] ?? '';

$response = [
    "model_name" => "",
    "assy_code"  => "",
    "batchlot"   => "NO"
];

if ($serial_code != '') {
    // Get basic info from trace_process
    $stmt = $conn->prepare("SELECT TOP 1 model_name, assy_code 
                            FROM trace_process 
                            WHERE serial_code = ? 
                            ORDER BY created_at DESC");
    $stmt->execute([$serial_code]);
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response["model_name"] = $row['model_name'];
        $response["assy_code"]  = $row['assy_code'];
    }

    // Check if batchlot exists in repair_process
    $stmt2 = $conn->prepare("SELECT TOP 1 batchlot 
                             FROM repair_process 
                             WHERE serial_code = ? 
                             ORDER BY created_at DESC");
    $stmt2->execute([$serial_code]);
    if ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $response["batchlot"] = (!empty($row2['batchlot']) && $row2['batchlot'] !== "NO")
            ? $row2['batchlot']
            : "NO";
    }
}

header('Content-Type: application/json');
echo json_encode($response);
