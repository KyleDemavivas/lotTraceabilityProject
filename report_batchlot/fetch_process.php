<?php
include '../db_connect.php';

$serial_code = $_GET['serial_code'] ?? '';
$response = ["data" => []];

if ($serial_code != '') {
    // Map process to table
    $process_map = [
        "LABELLING"             => "label_code",
        "SPA"                   => "spa_process",
        "MOUNTER"               => "mounter_process",
        "SMT VI"                => "vi_process",
        "AUTO INSERTION"        => "ai_process",
        "MANUAL INSERTION"      => "manual_insertion",
        "UNLOADER"              => "unloader",
        "LEAD LENGTH CHECK"     => "lead_length_check",
        "MODIFICATION"          => "modification",
        "WISS"                  => "wiss",
        "ICT"                   => "ict",
        "FVI"                   => "fvi",
        "MICROSCOPE INSPECTION" => "microscope_inspection",
        "QA ONLINE"             => "qa_online",
        "SUB ASSY"              => "sub_assy",
        "BARCODING"             => "barcoding",
        "WI"                    => "wi",
        "FT"                    => "ft",
        "PACKING"               => "packing",
        "QA ONLINE"             => "qa_online2" // alias for second QA ONLINE
    ];

    foreach ($process_map as $process => $table) {
        $stmt = $conn->prepare("SELECT TOP 1 line, shift, judgement, operator, created_at 
                                FROM $table 
                                WHERE serial_code = ? 
                                ORDER BY created_at DESC");
        $stmt->execute([$serial_code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $response["data"][] = [
            "process" => $process,
            "line" => $row['line'] ?? '',
            "shift" => $row['shift'] ?? '',
            "judgement" => $row['judgement'] ?? '',
            "operator" => $row['operator'] ?? '',
            "date_process" => isset($row['created_at']) ? date("d-M", strtotime($row['created_at'])) : '',
            "time_end_process" => isset($row['created_at']) ? date("g:i a", strtotime($row['created_at'])) : ''
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
