<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$kepi_lot = trim($_POST['kepi_lot'] ?? '');
if (!$kepi_lot) { echo json_encode(['status'=>'error','message'=>'Missing lot number.']); exit; }

try {
    $conn->beginTransaction();

    // Serialize start/join per kepi_lot so two simultaneous "new lot" starts can't race
    $lockStmt = $conn->prepare("DECLARE @res INT; EXEC @res = sp_getapplock @Resource = :res, @LockMode = 'Exclusive', @LockOwner='Transaction', @LockTimeout = 5000; SELECT @res AS result");
    $lockStmt->execute([':res' => 'qalot_' . $kepi_lot]);
    $lockResult = $lockStmt->fetch(PDO::FETCH_ASSOC);
    if ($lockResult['result'] < 0) {
        throw new Exception('Could not acquire lock for this lot.');
    }

    // Is there already an IN_PROGRESS session for this lot?
    $stmt = $conn->prepare("SELECT * FROM qa_lot WHERE kepi_lot = :lot AND status = 'IN_PROGRESS'");
    $stmt->execute([':lot' => $kepi_lot]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        // JOIN existing session — pull locked params + serials scanned so far
        $serialStmt = $conn->prepare("SELECT * FROM qa_process WHERE inspection_id = :id ORDER BY id ASC");
        $serialStmt->execute([':id' => $existing['id']]);
        $serials = $serialStmt->fetchAll(PDO::FETCH_ASSOC);

        $conn->commit();
        echo json_encode([
            'status'   => 'success',
            'joined'   => true,
            'lot'      => $existing,
            'serials'  => $serials,
        ]);
        exit;
    }

    // No in-progress session — create one, locking in AQL params now
    $stmt = $conn->prepare('SELECT ISNULL(MAX(attempt_number), 0) + 1 AS next_attempt FROM qa_lot WHERE kepi_lot = :lot');
    $stmt->execute([':lot' => $kepi_lot]);
    $nextAttempt = $stmt->fetch(PDO::FETCH_ASSOC)['next_attempt'];

    $sql = "INSERT INTO qa_lot (
                kepi_lot, attempt_number, model, inspection_method, code_letter,
                sample_size, lot_quantity, line, shift, operator_id,
                defects_015, defects_10, created_at, status,
                customer, assy_no, inspection_level
            ) VALUES (
                :kepi_lot, :attempt_number, :model, :inspection_method, :code_letter,
                :sample_size, :lot_quantity, :line, :shift, :operator_id,
                0, 0, :created_at, 'IN_PROGRESS',
                :customer, :assy_no, :inspection_level
            )";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':kepi_lot'          => $kepi_lot,
        ':attempt_number'    => $nextAttempt,
        ':model'             => $_POST['model'] ?? '',
        ':inspection_method' => $_POST['inspection_method'] ?? '',
        ':code_letter'       => $_POST['code_letter'] ?? '',
        ':sample_size'       => $_POST['sample_size'] ?? 0,
        ':lot_quantity'      => $_POST['lot_qty'] ?? 0,
        ':line'              => $_POST['line'] ?? '',
        ':shift'             => $_POST['shift'] ?? '',
        ':operator_id'       => $_POST['operator_id'] ?? '',
        ':created_at'        => date('Y-m-d H:i:s'),
        ':customer'          => $_POST['customer'] ?? '',
        ':assy_no'           => $_POST['assy_no'] ?? '',
        ':inspection_level'  => $_POST['inspection_level'] ?? '',
    ]);
    $newId = $conn->lastInsertId();

    $conn->commit();
    echo json_encode(['status'=>'success', 'joined'=>false, 'lot_id'=>$newId, 'attempt_number'=>$nextAttempt, 'serials'=>[]]);

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo json_encode(['status'=>'error', 'message'=>$e->getMessage()]);
}