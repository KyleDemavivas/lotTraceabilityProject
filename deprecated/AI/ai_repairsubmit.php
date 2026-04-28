<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];

date_default_timezone_set('Asia/Manila');
$created_at = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $sql = 'UPDATE ai_process SET judgement_vi = :judgement_vi, verified_vi = :verified_vi,created_at = :created_at WHERE qr_code = :qr_code AND serial_code = :serial_code AND id = :id';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':judgement_vi' => $_POST['judgement_vi'],
            ':verified_vi' => $_POST['verified_vi'],
            ':created_at' => $created_at,
            ':qr_code' => $_POST['qr_code'],
            ':serial_code' => $_POST['serial_code'],
            ':id' => $_POST['id'],
        ]);

        if ($_POST['judgement_vi'] === 'GOOD') {
            $checkAllGood = "SELECT COUNT(*) FROM ai_process WHERE qr_code = :qr_code AND serial_code = :serial_code AND judgement_vi != 'GOOD'";
            $stmtCheck = $conn->prepare($checkAllGood);
            $stmtCheck->execute([
                ':qr_code' => $_POST['qr_code'],
                ':serial_code' => $_POST['serial_code'],
            ]);
            $notGoodCount = $stmtCheck->fetchColumn();

            if ($notGoodCount == 0) {
                $fetchStatusSql = 'SELECT serial_status, board_status FROM ai_process WHERE qr_code = :qr_code AND serial_code = :serial_code';
                $stmtFetch = $conn->prepare($fetchStatusSql);
                $stmtFetch->execute([
                    ':qr_code' => $_POST['qr_code'],
                    ':serial_code' => $_POST['serial_code'],
                ]);
                $currentStatus = $stmtFetch->fetch(PDO::FETCH_ASSOC);

                if ($currentStatus) {
                    $updateStatusSql = "UPDATE ai_process SET prev_serialstatus = :prev_serial, prev_boardstatus = :prev_board, serial_status = 'GOOD', board_status = 'GOOD'
                                        WHERE qr_code = :qr_code AND serial_code = :serial_code";
                    $stmtUpdate = $conn->prepare($updateStatusSql);
                    $stmtUpdate->execute([
                        ':prev_serial' => $currentStatus['serial_status'],
                        ':prev_board' => $currentStatus['board_status'],
                        ':qr_code' => $_POST['qr_code'],
                        ':serial_code' => $_POST['serial_code'],
                    ]);
                }

                $checkAllSerialsGood = "SELECT COUNT(*) FROM ai_repair WHERE qr_code = :qr_code AND judgement_vi != 'GOOD'";
                $stmtCheckAll = $conn->prepare($checkAllSerialsGood);
                $stmtCheckAll->execute([':qr_code' => $_POST['qr_code']]);
                $remainingDefects = $stmtCheckAll->fetchColumn();

                if ($remainingDefects == 0) {
                    $fetchAllSerials = 'SELECT serial_code, serial_status, board_status FROM ai_process WHERE qr_code = :qr_code';
                    $stmtFetchAll = $conn->prepare($fetchAllSerials);
                    $stmtFetchAll->execute([':qr_code' => $_POST['qr_code']]);
                    $allSerials = $stmtFetchAll->fetchAll(PDO::FETCH_ASSOC);

                    $updateOthersSql = "UPDATE ai_process SET prev_boardstatus = :prev_board, prev_serialstatus = :prev_serial, board_status = 'GOOD', serial_status = 'GOOD'
                                        WHERE qr_code = :qr_code AND serial_code = :serial_code";
                    $stmtUpdateAll = $conn->prepare($updateOthersSql);

                    foreach ($allSerials as $row) {
                        $stmtUpdateAll->execute([
                            ':prev_board' => $row['board_status'],
                            ':prev_serial' => $row['serial_status'],
                            ':qr_code' => $_POST['qr_code'],
                            ':serial_code' => $row['serial_code'],
                        ]);
                    }
                }
            }
        }

        $response['status'] = 'success';
        $response['message'] = 'Verify Repair successfully.';
    } catch (PDOException $e) {
        $response['message'] = 'Database error: '.$e->getMessage();
    }
}

echo json_encode($response);
exit;
