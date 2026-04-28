<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];
date_default_timezone_set('Asia/Manila');
$created_at_ll = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->beginTransaction();

        // Extract all POST variables
        $judgement = $_POST['judgement_pl'] ?? '';
        $verifier = $_POST['verified_pl'] ?? '';
        $qr_code = $_POST['qr_code'] ?? '';
        $serial_code = $_POST['serial_code'] ?? '';
        $process_location = $_POST['process_location'] ?? '';
        $defect = $_POST['defect'] ?? '';

        if (!$serial_code) {
            throw new Exception('Serial code is required.');
        }

        if ($judgement === 'GOOD') {
            // Map process_location to their corresponding process table column and table
            $processMap = [
                'VI' => 'vi_process',
                'AI' => 'ai_process',
                'MOD1' => 'mod1_process',
                'MOD2' => 'mod2_process',
            ];

            $batchlotProcesses = ['FVISS', 'PARTSIDE 1', 'PARTSIDE 2', 'MICRO', 'WI'];

            $batchlotTables = ['fviss_process', 'partside_process', 'partside2_process', 'micro_process', 'wi_process'];
            $batchlotTablesBatch = ['fviss_batchlot', 'partside_batchlot', 'partside2_batchlot', 'micro_batchlot', 'wi_batchlot'];

            if (isset($processMap[$process_location])) {
                // Normal single process updates
                $col = $processMap[$process_location];

                $conn->prepare("UPDATE repair_master SET status = 'REPAIRED', process_lead = :process_lead WHERE serial_code = :serial_code AND defect = :defect")
                 ->execute([':process_lead' => $verifier, ':serial_code' => $serial_code, ':defect' => $defect]);

                $stmt = $conn->prepare("SELECT COUNT(*) FROM repair_master WHERE serial_code = :serial_code AND status != 'REPAIRED'");
                $stmt->execute([':serial_code' => $serial_code]);
                $pendingCount = $stmt->fetchColumn();

                $table = strtolower($process_location).'_process';
                if ($pendingCount == 0) {
                    $conn->prepare("UPDATE trace_process SET $col = NULL WHERE qr_code = :qr_code")
                     ->execute([':qr_code' => $qr_code]);

                    $conn->prepare("UPDATE $table SET board_status = 'GOOD', serial_status = 'GOOD' WHERE qr_code = :qr_code")
                         ->execute([':qr_code' => $qr_code]);
                }
            } elseif (in_array($process_location, $batchlotProcesses)) {
                // Batchlot logic
                $conn->prepare("UPDATE repair_master SET status = 'REPAIRED', process_lead = :process_lead WHERE serial_code = :serial_code AND defect = :defect")
                 ->execute([':process_lead' => $verifier, ':serial_code' => $serial_code, ':defect' => $defect]);

                $stmt = $conn->prepare("SELECT COUNT(*) FROM repair_master WHERE serial_code = :serial_code AND status != 'REPAIRED'");
                $stmt->execute([':serial_code' => $serial_code]);
                $pendingCount = $stmt->fetchColumn();

                if ($pendingCount == 0) {
                    $conn->prepare('UPDATE trace_process SET fviss_process = NULL, partside_process = NULL, partside2_process = NULL, micro_process = NULL, wi_process = NULL WHERE serial_code = :serial_code')
                          ->execute([':serial_code' => $serial_code]);

                    // Insert into batchlot_process from fviss_process
                    $conn->prepare("
                    INSERT INTO batchlot_process
                    (qr_code, qty_input, final_qtyinput, operator_name, shift, asmline, line, assy_code, model_name, kepi_lot, serial_code, board_counter, created_at, board_status, serial_status, prev_boardstatus, prev_serialstatus)
                    SELECT qr_code, qty_input, final_qtyinput, operator_name, shift, asmline, line, assy_code, model_name, kepi_lot, serial_code, board_counter, created_at, 'GOOD', 'GOOD', 'HOLD', 'NO GOOD'
                    FROM fviss_process
                    WHERE serial_code = :serial_code
                ")->execute([':serial_code' => $serial_code]);

                    // Delete from all individual process tables
                    foreach ($batchlotTables as $tbl) {
                        $conn->prepare("DELETE FROM $tbl WHERE serial_code = :serial_code")
                             ->execute([':serial_code' => $serial_code]);
                    }
                }
            } elseif (str_contains($process_location, 'BATCH LOT')) {
                // Batch lot deletion
                $conn->prepare("UPDATE repair_master SET status = 'REPAIRED', process_lead = :process_lead WHERE serial_code = :serial_code AND defect = :defect")
                 ->execute([':process_lead' => $verifier, ':serial_code' => $serial_code, ':defect' => $defect]);

                $stmt = $conn->prepare("SELECT COUNT(*) FROM repair_master WHERE serial_code = :serial_code AND status != 'REPAIRED'");
                $stmt->execute([':serial_code' => $serial_code]);
                $pendingCount = $stmt->fetchColumn();

                if ($pendingCount == 0) {
                    $conn->prepare('UPDATE trace_process SET fviss_process = NULL, partside_process = NULL, partside2_process = NULL, micro_process = NULL, wi_process = NULL WHERE serial_code = :serial_code')
                         ->execute([':serial_code' => $serial_code]);

                    foreach ($batchlotTablesBatch as $tbl) {
                        $conn->prepare("DELETE FROM $tbl WHERE serial_code = :serial_code")
                             ->execute([':serial_code' => $serial_code]);
                    }
                }
            } else {
                throw new Exception('Invalid Process Location.');
            }
        } else {
            // NO GOOD -> delete from repair_master
            $conn->prepare('DELETE FROM repair_master WHERE serial_code = :serial_code AND defect = :defect')
                 ->execute([':serial_code' => $serial_code, ':defect' => $defect]);
        }

        $conn->commit();
        $response['status'] = 'success';
        $response['message'] = 'LL Verification submitted successfully.';
    } catch (Exception $e) {
        $conn->rollBack();
        $response['status'] = 'error';
        $response['message'] = $e->getMessage();
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid Request Method.';
}

echo json_encode($response);
exit;
