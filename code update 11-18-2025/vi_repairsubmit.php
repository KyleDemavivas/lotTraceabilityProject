<?php
include 'db_connect.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];
date_default_timezone_set('Asia/Manila');
$created_at_vi = date('Y-m-d H:i:s');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $conn->beginTransaction();

        $sql = "UPDATE repair_process SET judgement_vi = :judgement_vi, verified_vi = :verified_vi, created_at_vi = :created_at_vi
                WHERE qr_code = :qr_code AND serial_code = :serial_code AND id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':judgement_vi' => $_POST['judgement_vi'],
            ':verified_vi' => $_POST['verified_vi'],
            ':created_at_vi' => $created_at_vi,
            ':qr_code' => $_POST['qr_code'],
            ':serial_code' => $_POST['serial_code'],
            ':id' => $_POST['id']
        ]);

        if ($_POST['judgement_vi'] === 'GOOD') {
            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM repair_process WHERE serial_code = :serial_code AND qr_code = :qr_code AND judgement_vi != 'GOOD'");
            $stmtCheck->execute([
                ':serial_code' => $_POST['serial_code'],
                ':qr_code' => $_POST['qr_code']
            ]);
            $remainingNotGood = $stmtCheck->fetchColumn();

            if ($remainingNotGood == 0) {
                // Fetch current status
                $stmtFetch = $conn->prepare("SELECT serial_status, board_status FROM vi_process 
                    WHERE qr_code = :qr_code AND serial_code = :serial_code");
                $stmtFetch->execute([
                    ':qr_code' => $_POST['qr_code'],
                    ':serial_code' => $_POST['serial_code']
                ]);
                $currentStatus = $stmtFetch->fetch(PDO::FETCH_ASSOC);

                if ($currentStatus) {
                    $stmtUpdate = $conn->prepare("UPDATE vi_process SET prev_serialstatus = :prev_serial, prev_boardstatus = :prev_board, serial_status = 'GOOD', board_status = 'GOOD'
                        WHERE qr_code = :qr_code AND serial_code = :serial_code");
                    $stmtUpdate->execute([
                        ':prev_serial' => $currentStatus['serial_status'],
                        ':prev_board' => $currentStatus['board_status'],
                        ':qr_code' => $_POST['qr_code'],
                        ':serial_code' => $_POST['serial_code']
                    ]);
                }
            }
        }

        $conn->commit();

        $response['status'] = 'success';
        $response['message'] = ($_POST['judgement_vi'] === 'NO GOOD')
            ? 'Unit returned to Repairer (NO GOOD).'
            : 'VI Verification submitted successfully.';
    } catch (PDOException $e) {
        $conn->rollBack();
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
exit();
