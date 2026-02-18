<?php
include 'db_connect.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];
date_default_timezone_set('Asia/Manila');
$created_at_ll = date('Y-m-d H:i:s');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $conn->beginTransaction();

        $judgement_vi = ($_POST['judgement_ll'] === 'NO GOOD') ? 'NO GOOD' : 'PENDING';

        $sql = "UPDATE repair_process SET judgement_ll = :judgement_ll, verified_ll = :verified_ll, judgement_vi = :judgement_vi, created_at_ll = :created_at_ll
                WHERE qr_code = :qr_code AND serial_code = :serial_code AND id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':judgement_ll' => $_POST['judgement_ll'],
            ':verified_ll' => $_POST['verified_ll'],
            ':judgement_vi' => $judgement_vi,
            ':created_at_ll' => $created_at_ll,
            ':qr_code' => $_POST['qr_code'],
            ':serial_code' => $_POST['serial_code'],
            ':id' => $_POST['id']
        ]);

        $conn->commit();

        $response['status'] = 'success';
        $response['message'] = 'LL Verification submitted successfully.';
    } catch (PDOException $e) {
        $conn->rollBack();
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
exit();
