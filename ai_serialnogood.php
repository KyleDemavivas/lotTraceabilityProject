<?php
include 'db_connect.php';

$response = [];

if (
    isset($_POST['qr_code'], $_POST['serial_code'], $_POST['defect'], $_POST['location'], $_POST['board_number'])
) {
    try {
        $qr_code = trim($_POST['qr_code']);
        $serial_code = trim($_POST['serial_code']);
        $defects = $_POST['defect'];
        $locations = $_POST['location'];
        $board_number = trim($_POST['board_number']);
        $source = isset($_POST['source']) ? $_POST['source'] : '';

        date_default_timezone_set('Asia/Manila');
        $created_at = date('Y-m-d H:i:s');

        $checkSerial = $conn->prepare("SELECT COUNT(*) FROM ai_process WHERE serial_code = :serial_code");
        $checkSerial->execute([':serial_code' => $serial_code]);
        $serialExists = $checkSerial->fetchColumn();

        if (!$serialExists) {
            $response['status'] = 'error';
            $response['message'] = 'Serial code does not exist in the system.';
            echo json_encode($response);
            exit;
        }

        if ($source === 'alert' || $source === 'modal') {
            $verifySerialQR = $conn->prepare("SELECT COUNT(*) FROM ai_process WHERE serial_code = :serial_code AND qr_code = :qr_code");
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

        $insertSQL = "INSERT INTO ai_nogood (qr_code, serial_code, defect, location, board_number, created_at)
                      VALUES (:qr_code, :serial_code, :defect, :location, :board_number, :created_at)";
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
                    ':created_at' => $created_at
                ]);
                if ($stmtInsert->rowCount() > 0) {
                    $successfulInserts++;
                }
            }
        }

        if ($successfulInserts > 0) {
            $updateSerialStatusSql = "UPDATE ai_process SET prev_serialstatus = serial_status, serial_status = 'NO GOOD' WHERE serial_code = :serial_code";
            $stmtUpdateStatus = $conn->prepare($updateSerialStatusSql);
            $stmtUpdateStatus->execute([':serial_code' => $serial_code]);

            $checkSql = "SELECT COUNT(*) FROM ai_process WHERE qr_code = :qr_code AND serial_status = 'NO GOOD'";
            $stmtCheck = $conn->prepare($checkSql);
            $stmtCheck->execute([':qr_code' => $qr_code]);
            $noGoodCount = $stmtCheck->fetchColumn();

            if ($noGoodCount > 0) {
                $updateBoardStatusSql = "UPDATE ai_process SET prev_boardstatus = board_status, board_status = 'HOLD' WHERE qr_code = :qr_code AND prev_boardstatus = 'GOOD' AND board_status = 'GOOD'";
                $stmtBoard = $conn->prepare($updateBoardStatusSql);
                $stmtBoard->execute([':qr_code' => $qr_code]);
            }

            $response['status'] = 'success';
            $response['message'] = "Saved $successfulInserts defect(s) and updated status.";
        } else {
            $response['status'] = 'error';
            $response['message'] = 'No valid defects submitted.';
        }
    } catch (PDOException $e) {
        $response['status'] = 'error';
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Missing required fields.';
}

echo json_encode($response);
exit;
