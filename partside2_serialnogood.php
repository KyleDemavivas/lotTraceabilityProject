<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

$response['success'] = false;

if (
    isset($_POST['qr_code'], $_POST['serial_code'], $_POST['defect'], $_POST['location'], $_POST['board_number'], $_POST['scrap_partside'], $_POST['repairable'])
) {
    try {
        $origin = $_POST['origin'] ?? '';
        if (empty($origin)) {
            throw new Exception('Origin is NULL.');
            exit;
        }
        $main_table = $origin === 'main' ? 'partside2_process' : 'partside2_batchlot';

        $qr_code = trim($_POST['qr_code']);
        $serial_code = trim($_POST['serial_code']);
        $defects = $_POST['defect'];
        $locations = $_POST['location'];
        $board_number = trim($_POST['board_number']);
        $scrap_partside = $_POST['scrap_partside'];
        $repairable = $_POST['repairable'];
        $source = isset($_POST['source']) ? $_POST['source'] : '';

        date_default_timezone_set('Asia/Manila');
        $created_at = date('Y-m-d H:i:s');

        $checkSerial = $conn->prepare("SELECT COUNT(*) FROM $main_table WHERE serial_code = :serial_code");
        $checkSerial->execute([':serial_code' => $serial_code]);
        $serialExists = $checkSerial->fetchColumn();

        if (!$serialExists) {
            $response['status'] = 'error';
            $response['message'] = 'Serial code does not exist in the system.';
            echo json_encode($response);
            exit;
        }

        $stmt = $conn->prepare("SELECT serial_status FROM $main_table WHERE serial_code = :serial_code");
        $stmt->execute([':serial_code' => $serial_code]);
        $currentStatus = $stmt->fetchColumn();

        if ($currentStatus === 'NO GOOD') {
            $response['status'] = 'error';
            $response['message'] = 'This serial is already recorded as NO GOOD.';
            echo json_encode($response);
            exit;
        }

        if ($source === 'alert' || $source === 'modal') {
            $verifySerialQR = $conn->prepare("SELECT COUNT(*) FROM $main_table WHERE serial_code = :serial_code AND qr_code = :qr_code");
            $verifySerialQR->execute([':serial_code' => $serial_code, ':qr_code' => $qr_code]);
            $matchCount = $verifySerialQR->fetchColumn();

            if ($matchCount == 0) {
                $response['status'] = 'error';
                $response['field'] = 'serial_code';
                $response['message'] = 'This serial code does not belong to the entered QR code.';
                echo json_encode($response);
                exit;
            }
        }

        $insertSQL = 'INSERT INTO partside2_nogood (qr_code, serial_code, defect, location, board_number, scrap_partside, repairable, created_at)
                      VALUES (:qr_code, :serial_code, :defect, :location, :board_number, :scrap_partside, :repairable, :created_at)';
        $stmtInsert = $conn->prepare($insertSQL);

        $successfulInserts = 0;

        foreach ($defects as $index => $defect) {
            $defect = trim($defect);
            $location_array = isset($locations[$index]) ? $locations[$index] : [];
            $location_string = implode(', ', $location_array);

            if ($defect && $location_string) {
                $stmtInsert->execute([
                    ':qr_code' => $qr_code,
                    ':serial_code' => $serial_code,
                    ':defect' => $defect,
                    ':location' => $location_string,
                    ':board_number' => $board_number,
                    ':scrap_partside' => $scrap_partside,
                    ':repairable' => $repairable,
                    ':created_at' => $created_at,
                ]);
                if ($stmtInsert->rowCount() > 0) {
                    ++$successfulInserts;
                }
            }
        }

        if ($successfulInserts > 0) {
            $updateSerialStatusSql = "UPDATE $main_table SET prev_serialstatus = serial_status, serial_status = 'NO GOOD' WHERE serial_code = :serial_code";
            $stmtUpdateStatus = $conn->prepare($updateSerialStatusSql);
            $stmtUpdateStatus->execute([':serial_code' => $serial_code]);

            $checkSql = "SELECT COUNT(*) FROM $main_table WHERE qr_code = :qr_code AND serial_status = 'NO GOOD'";
            $stmtCheck = $conn->prepare($checkSql);
            $stmtCheck->execute([':qr_code' => $qr_code]);
            $noGoodCount = $stmtCheck->fetchColumn();

            if ($noGoodCount > 0) {
                $updateBoardStatusSql = "UPDATE $main_table SET prev_boardstatus = board_status, board_status = 'HOLD' WHERE qr_code = :qr_code AND prev_boardstatus = 'GOOD' AND board_status = 'GOOD'";
                $stmtBoard = $conn->prepare($updateBoardStatusSql);
                $stmtBoard->execute([':qr_code' => $qr_code]);
            }

            $response['status'] = 'success';
            $response['message'] = "Saved $successfulInserts defect(s) and updated status.";
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No valid defects submitted.';
        }
    } catch (Throwable $e) {
        $response['status'] = 'error';
        $response['message'] = 'Database error: '.$e->getMessage();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Missing required fields.';
}

echo json_encode($response);
exit;
