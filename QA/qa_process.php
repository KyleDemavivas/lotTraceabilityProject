<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Something went wrong.'];
date_default_timezone_set('Asia/Manila');
$created_at = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $kepi_lot           = $_POST['kepi_lot'] ?? '';
    $model              = $_POST['model'] ?? '';
    $inspection_method  = $_POST['inspection_method'] ?? '';
    $code_letter        = $_POST['code_letter'] ?? '';
    $sample_size        = $_POST['sample_size'] ?? 0;
    $lot_quantity       = $_POST['lot_qty'] ?? 0;
    $line               = $_POST['line'] ?? '';
    $shift              = $_POST['shift'] ?? '';
    $operator_id        = $_POST['operator_id'] ?? '';
    $defects_015        = $_POST['defects_015'] ?? 0;
    $defects_10         = $_POST['defects_10'] ?? 0;
    $lot_result         = $_POST['lot_result'] ?? 'ACCEPT';
    $customer         = $_POST['customer'] ?? '';
    $assy_no          = $_POST['assy_no'] ?? '';
    $reference_no     = $_POST['reference_no'] ?? '';
    $inspection_level = $_POST['inspection_level'] ?? '';

    //FINALIZATION DATA
    $judgement              = $_POST['judgement'] ?? '';
    $parts_appearance       = $_POST['parts_appearance'] ?? null;
    $pcb_appearance         = $_POST['pcb_appearance'] ?? null;
    $solder_condition       = $_POST['solder_condition'] ?? null;
    $labels_markings        = $_POST['labels_markings'] ?? null;
    $subassembly_condition  = $_POST['subassembly_condition'] ?? null;
    $package_condition      = $_POST['package_condition'] ?? null;

    // serials is a JSON string from the frontend: array of {serial_code, status, location, defect_code, severity, lot_out, scrap}
    $serialsJson = $_POST['serials'] ?? '[]';
    $serials     = json_decode($serialsJson, true);

    if (empty($kepi_lot) || !is_array($serials) || empty($serials)) {
        $response['message'] = 'Missing lot number or serial data.';
        echo json_encode($response);
        exit;
    }

    try {
        $conn->beginTransaction();

        // 1. Calculate next attempt_number for this lot
        $stmt = $conn->prepare('SELECT ISNULL(MAX(attempt_number), 0) + 1 AS next_attempt FROM qa_lot WHERE kepi_lot = :kepi_lot');
        $stmt->execute([':kepi_lot' => $kepi_lot]);
        $nextAttempt = $stmt->fetch(PDO::FETCH_ASSOC)['next_attempt'];

        // 2. Insert the qa_lot header row
       $sql = 'INSERT INTO qa_lot (
            kepi_lot, attempt_number, model, inspection_method, code_letter,
            sample_size, lot_quantity, line, shift, operator_id,
            defects_015, defects_10, lot_result, created_at,
            customer, assy_no, reference_no, inspection_level,
            judgement, parts_appearance, pcb_appearance, solder_condition,
            labels_markings, subassembly_condition, package_condition
        )
        VALUES (
            :kepi_lot, :attempt_number, :model, :inspection_method, :code_letter,
            :sample_size, :lot_quantity, :line, :shift, :operator_id,
            :defects_015, :defects_10, :lot_result, :created_at,
            :customer, :assy_no, :reference_no, :inspection_level,
            :judgement, :parts_appearance, :pcb_appearance, :solder_condition,
            :labels_markings, :subassembly_condition, :package_condition
        )';

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':kepi_lot'          => $kepi_lot,
            ':attempt_number'    => $nextAttempt,
            ':model'             => $model,
            ':inspection_method' => $inspection_method,
            ':code_letter'       => $code_letter,
            ':sample_size'       => $sample_size,
            ':lot_quantity'      => $lot_quantity,
            ':line'              => $line,
            ':shift'             => $shift,
            ':operator_id'       => $operator_id,
            ':defects_015'       => $defects_015,
            ':defects_10'        => $defects_10,
            ':lot_result'        => $lot_result,
            ':created_at'        => $created_at,
            ':customer'          => $customer,
            ':assy_no'           => $assy_no,
            ':reference_no'      => $reference_no,
            ':inspection_level'  => $inspection_level,
            ':judgement'              => $judgement,
            ':parts_appearance'       => $parts_appearance,
            ':pcb_appearance'         => $pcb_appearance,
            ':solder_condition'       => $solder_condition,
            ':labels_markings'        => $labels_markings,
            ':subassembly_condition'  => $subassembly_condition,
            ':package_condition'      => $package_condition,
        ]);

        $inspectionId = $conn->lastInsertId();

        // 3. Insert each serial row, tagged with the new inspection_id
        $serialSql = 'INSERT INTO qa_process (serial_code, status, location, defect_code, severity, lot_out, scrap, inspection_id, created_at, parts_specification)
              VALUES (:serial_code, :status, :location, :defect_code, :severity, :lot_out, :scrap, :inspection_id, :created_at, :parts_specification)';
        $serialStmt = $conn->prepare($serialSql);

        foreach ($serials as $s) {
            $serialStmt->execute([
                ':serial_code'   => $s['serial_code'] ?? '',
                ':status'        => $s['status'] ?? 'GOOD',
                ':location'      => $s['location'] ?? null,
                ':defect_code'   => $s['defect_code'] ?? null,
                ':severity'      => $s['severity'] ?? null,
                ':lot_out'       => $s['lot_out'] ?? 0,
                ':scrap'         => $s['scrap'] ?? 0,
                ':inspection_id' => $inspectionId,
                ':created_at'    => $created_at,
                ':parts_specification' => $s['parts_specification'] ?? null,
            ]);
        }

        $conn->commit();

        $response['status']        = 'success';
        $response['message']       = 'QA inspection recorded successfully.';
        $response['inspection_id'] = $inspectionId;
        $response['attempt_number'] = $nextAttempt;

    } catch (PDOException $e) {
        $conn->rollBack();
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
}

echo json_encode($response);
exit;