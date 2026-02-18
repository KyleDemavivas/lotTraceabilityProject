<?php
include 'db_connect.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];
date_default_timezone_set('Asia/Manila');
$created_at_ll = date('Y-m-d H:i:s');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if($_POST['judgement_ll'] == 'GOOD'){
    try {
        // Capture form data
        $qr_code = $_POST['qr_code'];
        $model_name = $_POST['model_name'];
        $assy_code = $_POST['assy_code'];
        $kepi_lot = $_POST['kepi_lot'];
        $shift = $_POST['shift'];
        $line = $_POST['line'];
        $board_number = $_POST['board_number'];
        $serial_code = $_POST['serial_code'];
        $defect = $_POST['defect'];
        $operator_name = $_POST['operator_name'];
        $location = $_POST['location'];
        $process_location = $_POST['process_location'];
        $repaired_by = $_POST['repaired_by'];
        $action_rp = $_POST['action_rp'];
        $lcr_reading = $_POST['lcr_reading'];
        $parts_code = $_POST['parts_code'];
        $parts_lot = $_POST['parts_lot'];
        $unitmeasurement = $_POST['unitmeasurement'];
        $batchlot = $_POST['batchlot'];
        $repairable = $_POST['repairable'];
        $verify_ll = $_POST['verified_ll'];

        // SQL query to insert data
        $query = "INSERT INTO repair_process_verify
            (qr_code, model_name, assy_code, kepi_lot, shift, line, board_number, 
            serial_code, defect, operator_name, location, process_location, 
            repaired_by, action_rp, lcr_reading, parts_code, parts_lot, 
            unitmeasurement, batchlot, repairable, verified_ll, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'FOR VERIFICATION', GETDATE())";

        // Prepare the statement
        $stmt = $conn->prepare($query);

        // Execute the query with the provided form data
        $stmt->execute([
            $qr_code,
            $model_name,
            $assy_code,
            $kepi_lot,
            $shift,
            $line,
            $board_number,
            $serial_code,
            $defect,
            $operator_name,
            $location,
            $process_location,
            $repaired_by,
            $action_rp,
            $lcr_reading,
            $parts_code,
            $parts_lot,
            $unitmeasurement,
            $batchlot,
            $repairable,
            $verify_ll
        ]);

        $stmt2 = $conn->prepare("DELETE FROM repair_ll_verify WHERE qr_code = ?");
        $stmt2->execute([$qr_code]);    

        // Return success response
        $response['status'] = 'success';
        $response['message'] = 'Repair record submitted successfully.';
        echo json_encode($response);
    } catch (PDOException $e) {
        $response['status'] = 'error';
        $response['message'] = 'Database error: ' . $e->getMessage();
            echo json_encode($response);
    }
    } else {
        $stmt2 = $conn->prepare("DELETE FROM repair_ll_verify WHERE qr_code = ?");
        $stmt2->execute([$qr_code]);
        $response['status'] = 'success';
        echo json_encode($response);
    }
}
