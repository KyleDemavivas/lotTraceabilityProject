<?php
include 'db_connect.php';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];
date_default_timezone_set('Asia/Manila');
$created_at_ll = date('Y-m-d H:i:s');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {

        $conn->beginTransaction();
        $judgement = $_POST['judgement_pl'];

        if ($judgement === 'GOOD') {
            switch ($_POST['process_location']) {
                case 'VI':
                    $stmt1 = $conn->prepare("UPDATE trace_process SET vi_process = NULL WHERE qr_code = :qr_code");
                    $stmt1->execute([':qr_code' => $_POST['qr_code']]);

                    $stmt2 = $conn->prepare("UPDATE vi_process SET board_status = 'GOOD', serial_status = 'GOOD' WHERE qr_code = :qr_code");
                    $stmt2->execute([':qr_code' => $_POST['qr_code']]);

                    $stmt3 = $conn->prepare("INSERT INTO repair_record (qr_code, serial_code, process_location, created_at, action_rp, repaired_by, shift, defect, board_number, lcr_reading, assy_code, verified_pl, verified_ll)
                        VALUES (:qr_code, :serial_code, :process_location, :created_at, :action_rp, :repaired_by, :shift, :defect, :board_number, :lcr_reading, :assy_code, :verified_pl, :verified_ll)");
                    $stmt3->execute([
                        ':qr_code' => $_POST['qr_code'],
                        ':serial_code' => $_POST['serial_code'],
                        ':process_location' => $_POST['process_location'],
                        ':created_at' => $created_at_ll, // Or $_POST['created_at'] if you want to use the posted value
                        ':action_rp' => $_POST['action_rp'],
                        ':repaired_by' => $_POST['repaired_by'],
                        ':shift' => $_POST['shift'],
                        ':defect' => $_POST['defect'],
                        ':board_number' => $_POST['board_number'],
                        ':lcr_reading' => $_POST['lcr_reading'],
                        ':assy_code' => $_POST['assy_code'],
                        ':verified_pl' => $_POST['verified_pl'],
                        ':verified_ll' => $_POST['verified_ll']
                    ]);

                    $conn->prepare("INSERT INTO ");

                    $stmt = $conn->prepare("DELETE FROM repair_process_verify WHERE qr_code = :qr_code");
                    $stmt->execute([':qr_code' => $_POST['qr_code']]);

                    $stmtDel = $conn->prepare("DELETE FROM vi_nogood WHERE qr_code = :qr_code");
                    $stmtDel->execute([':qr_code' => $_POST['qr_code']]);

                    break;

                case 'AI':
                    $stmt1 = $conn->prepare("UPDATE trace_process SET ai_process = NULL WHERE qr_code = :qr_code");
                    $stmt1->execute([':qr_code' => $_POST['qr_code']]);

                    $stmt2 = $conn->prepare("UPDATE ai_process SET board_status = 'GOOD', serial_status = 'GOOD' WHERE qr_code = :qr_code");
                    $stmt2->execute([':qr_code' => $_POST['qr_code']]);

                    $stmt3 = $conn->prepare("INSERT INTO repair_record (qr_code, serial_code, process_location, created_at, action_rp, repaired_by, shift, defect, board_number, lcr_reading, assy_code, verified_pl, verified_ll)
                        VALUES (:qr_code, :serial_code, :process_location, :created_at, :action_rp, :repaired_by, :shift, :defect, :board_number, :lcr_reading, :assy_code, :verified_pl, :verified_ll)");
                    $stmt3->execute([
                        ':qr_code' => $_POST['qr_code'],
                        ':serial_code' => $_POST['serial_code'],
                        ':process_location' => $_POST['process_location'],
                        ':created_at' => $created_at_ll, // Or $_POST['created_at'] if you want to use the posted value
                        ':action_rp' => $_POST['action_rp'],
                        ':repaired_by' => $_POST['repaired_by'],
                        ':shift' => $_POST['shift'],
                        ':defect' => $_POST['defect'],
                        ':board_number' => $_POST['board_number'],
                        ':lcr_reading' => $_POST['lcr_reading'],
                        ':assy_code' => $_POST['assy_code'],
                        ':verified_pl' => $_POST['verified_pl'],
                        ':verified_ll' => $_POST['verified_ll']
                    ]);

                    $stmt = $conn->prepare("DELETE FROM repair_process_verify WHERE qr_code = :qr_code");
                    $stmt->execute([':qr_code' => $_POST['qr_code']]);

                    $stmtDel = $conn->prepare("DELETE FROM ai_nogood WHERE qr_code = :qr_code");
                    $stmtDel->execute([':qr_code' => $_POST['qr_code']]);


                    break;

                case 'MOD1':

                    $response['testMessage'] = 'this case is selected!';
                    $stmt1 = $conn->prepare("UPDATE trace_process SET mod1_process = NULL WHERE qr_code = :qr_code");
                    $stmt1->execute([':qr_code' => $_POST['qr_code']]);

                    $stmt2 = $conn->prepare("UPDATE mod1_process SET board_status = 'GOOD', serial_status = 'GOOD' WHERE qr_code = :qr_code");
                    $stmt2->execute([':qr_code' => $_POST['qr_code']]);

                    $stmt3 = $conn->prepare("INSERT INTO repair_record (qr_code, serial_code, process_location, created_at, action_rp, repaired_by, shift, defect, board_number, lcr_reading, assy_code, verified_pl, verified_ll)
                        VALUES (:qr_code, :serial_code, :process_location, :created_at, :action_rp, :repaired_by, :shift, :defect, :board_number, :lcr_reading, :assy_code, :verified_pl, :verified_ll)");
                    $stmt3->execute([
                        ':qr_code' => $_POST['qr_code'],
                        ':serial_code' => $_POST['serial_code'],
                        ':process_location' => $_POST['process_location'],
                        ':created_at' => $created_at_ll, // Or $_POST['created_at'] if you want to use the posted value
                        ':action_rp' => $_POST['action_rp'],
                        ':repaired_by' => $_POST['repaired_by'],
                        ':shift' => $_POST['shift'],
                        ':defect' => $_POST['defect'],
                        ':board_number' => $_POST['board_number'],
                        ':lcr_reading' => $_POST['lcr_reading'],
                        ':assy_code' => $_POST['assy_code'],
                        ':verified_pl' => $_POST['verified_pl'],
                        ':verified_ll' => $_POST['verified_ll']
                    ]);
                    $stmt = $conn->prepare("DELETE FROM repair_process_verify WHERE qr_code = :qr_code");
                    $stmt->execute([':qr_code' => $_POST['qr_code']]);

                    $stmtDel = $conn->prepare("DELETE FROM mod1_nogood WHERE qr_code = :qr_code");
                    $stmtDel->execute([':qr_code' => $_POST['qr_code']]);


                    break;

                case 'MOD2':
                    $stmt1 = $conn->prepare("UPDATE trace_process SET mod2_process = NULL WHERE qr_code = :qr_code");
                    $stmt1->execute([':qr_code' => $_POST['qr_code']]);

                    $stmt2 = $conn->prepare("UPDATE mod2_process SET board_status = 'GOOD', serial_status = 'GOOD' WHERE qr_code = :qr_code");
                    $stmt2->execute([':qr_code' => $_POST['qr_code']]);

                    $stmt3 = $conn->prepare("INSERT INTO repair_record (qr_code, serial_code, process_location, created_at, action_rp, repaired_by, shift, defect, board_number, lcr_reading, assy_code, verified_pl, verified_ll)
                        VALUES (:qr_code, :serial_code, :process_location, :created_at, :action_rp, :repaired_by, :shift, :defect, :board_number, :lcr_reading, :assy_code, :verified_pl, :verified_ll)");
                    $stmt3->execute([
                        ':qr_code' => $_POST['qr_code'],
                        ':serial_code' => $_POST['serial_code'],
                        ':process_location' => $_POST['process_location'],
                        ':created_at' => $created_at_ll, // Or $_POST['created_at'] if you want to use the posted value
                        ':action_rp' => $_POST['action_rp'],
                        ':repaired_by' => $_POST['repaired_by'],
                        ':shift' => $_POST['shift'],
                        ':defect' => $_POST['defect'],
                        ':board_number' => $_POST['board_number'],
                        ':lcr_reading' => $_POST['lcr_reading'],
                        ':assy_code' => $_POST['assy_code'],
                        ':verified_pl' => $_POST['verified_pl'],
                        ':verified_ll' => $_POST['verified_ll']
                    ]);

                    $stmt = $conn->prepare("DELETE FROM repair_process_verify WHERE qr_code = :qr_code");
                    $stmt->execute([':qr_code' => $_POST['qr_code']]);

                    $stmtDel = $conn->prepare("DELETE FROM mod2_nogood WHERE qr_code = :qr_code");
                    $stmtDel->execute([':qr_code' => $_POST['qr_code']]);


                    break;

                //FOR BATCHLOT QUERY LOGIC

                case 'FVISS':
                case 'PARTSIDE 1':
                case 'PARTSIDE 2':
                case 'MICRO':
                case 'WI':
                    $stmt1 = $conn->prepare("UPDATE trace_process SET fviss_process = NULL, partside_process = NULL, partside2_process = NULL, micro_process = NULL, wi_process = NULL WHERE serial_code = :serial_code");
                    $stmt1->execute([':serial_code' => $_POST['serial_code']]);

                    $stmt3 = $conn->prepare("INSERT INTO repair_record (qr_code, serial_code, process_location, created_at, action_rp, repaired_by, shift, defect, board_number, lcr_reading, assy_code, verified_pl, verified_ll)
                        VALUES (:qr_code, :serial_code, :process_location, :created_at, :action_rp, :repaired_by, :shift, :defect, :board_number, :lcr_reading, :assy_code, :verified_pl, :verified_ll)");
                    $stmt3->execute([
                        ':qr_code' => $_POST['qr_code'],
                        ':serial_code' => $_POST['serial_code'],
                        ':process_location' => $_POST['process_location'],
                        ':created_at' => $created_at_ll, // Or $_POST['created_at'] if you want to use the posted value
                        ':action_rp' => $_POST['action_rp'],
                        ':repaired_by' => $_POST['repaired_by'],
                        ':shift' => $_POST['shift'],
                        ':defect' => $_POST['defect'],
                        ':board_number' => $_POST['board_number'],
                        ':lcr_reading' => $_POST['lcr_reading'],
                        ':assy_code' => $_POST['assy_code'],
                        ':verified_pl' => $_POST['verified_pl'],
                        ':verified_ll' => $_POST['verified_ll']
                    ]);
                    /*$stmt4 = $conn->prepare("INSERT INTO fviss_batchlot (qr_code, model_name, assy_code, kepi_lot, serial_code, defect, operator_name, location, shift, line, board_number, repaired_by, action_rp, lcr_reading, unitmeasurement, batchlot, process_location, created_at)
                                            VALUES (:qr_code, :model_name, :assy_code, :kepi_lot, :serial_code, :defect, :operator_name, :location, :shift, :line, :board_number, :repaired_by, :action_rp, :lcr_reading, :unitmeasurement, :batchlot, :process_location, GETDATE())");
                    $stmt4->execute([
                        ':qr_code' => $_POST['qr_code'],
                        ':model_name' => $_POST['model_name'],
                        ':assy_code' => $_POST['assy_code'],
                        ':kepi_lot' => $_POST['kepi_lot'],
                        ':serial_code' => $_POST['serial_code'],
                        ':defect' => $_POST['defect'],
                        ':operator_name' => $_POST['operator_name'],
                        ':location' => $_POST['location'],
                        ':shift' => $_POST['shift'],
                        ':line' => $_POST['line'],
                        ':board_number' => $_POST['board_number'],
                        ':repaired_by' => $_POST['repaired_by'],
                        ':action_rp' => $_POST['action_rp'],
                        ':lcr_reading' => $_POST['lcr_reading'],
                        ':unitmeasurement' => $_POST['unitmeasurement'],
                        ':batchlot' => $_POST['batchlot'],
                        ':process_location' => $_POST['process_location']
                    ]);*/

                    $copystmt = $conn->prepare("SELECT qr_code, qty_input, final_qtyinput, operator_name, shift, asmline, line, assy_code, model_name, kepi_lot, serial_code, board_counter, created_at, board_status, serial_status, prev_boardstatus, prev_serialstatus FROM fviss_process WHERE serial_code = :serial_code");
                    $copystmt->execute([':serial_code' => $_POST['serial_code']]);
                    $row = $copystmt->fetch(PDO::FETCH_ASSOC);

                    $insertSerial = "INSERT INTO batchlot_process 
            (qr_code, qty_input, final_qtyinput, operator_name, shift, asmline, line, assy_code, model_name, kepi_lot, serial_code, board_counter, created_at, board_status, serial_status, prev_boardstatus, prev_serialstatus)
            SELECT qr_code, qty_input, final_qtyinput, operator_name, shift, asmline, line, assy_code, model_name, kepi_lot, serial_code, board_counter, created_at, board_status, serial_status, prev_boardstatus, prev_serialstatus FROM fviss_process
            WHERE serial_code = :serial_code
            ";

                    $stmt4 = $conn->prepare($insertSerial);
                    $stmt4->execute([':serial_code' => $_POST['serial_code']]);

                    $conn->prepare("DELETE FROM fviss_process WHERE qr_code = :qr_code");

                    $conn->prepare("DELETE FROM partside_process WHERE serial_code = :serial_code")
                        ->execute([':serial_code' => $_POST['serial_code']]);

                    $conn->prepare("DELETE FROM partside2_process WHERE serial_code = :serial_code")
                        ->execute([':serial_code' => $_POST['serial_code']]);

                    $conn->prepare("DELETE FROM micro_process WHERE serial_code = :serial_code")
                        ->execute([':serial_code' => $_POST['serial_code']]);

                    $conn->prepare("DELETE FROM wi_process WHERE serial_code = :serial_code")
                        ->execute([':serial_code' => $_POST['serial_code']]);


                    $stmt = $conn->prepare("DELETE FROM repair_process_verify WHERE qr_code = :qr_code OR serial_code = :serial_code");
                    $stmt->execute([':qr_code' => $_POST['qr_code'], ':serial_code' => $_POST['serial_code']]);

                    break;

                default:
                    echo "No matching process location.";
            }
        } else {
            $stmt = $conn->prepare("DELETE FROM repair_ll_verify WHERE serial_code = :serial_code");
            $stmt->execute([':serial_code' => $_POST['serial_code']]);
            $stmt = $conn->prepare("DELETE FROM repair_process_verify WHERE serial_code = :serial_code");
            $stmt->execute([':serial_code' => $_POST['serial_code']]);
        }
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
