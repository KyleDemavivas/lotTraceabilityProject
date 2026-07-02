<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$inspection_id = $_POST['inspection_id'] ?? 0;
$serial_code   = trim($_POST['serial_code'] ?? '');
$status        = $_POST['status'] ?? 'GOOD';      // GOOD | NO GOOD
$location      = $_POST['location'] ?? null;
$defect_code   = $_POST['defect_code'] ?? null;
$severity      = $_POST['severity'] ?? null;
$parts_spec    = $_POST['parts_specification'] ?? null;
$major_count   = (int)($_POST['major_count'] ?? 0);
$minor_count   = (int)($_POST['minor_count'] ?? 0);

if (!$inspection_id || !$serial_code) {
    echo json_encode(['status'=>'error','message'=>'Missing inspection_id or serial_code.']);
    exit;
}

try {
    $conn->beginTransaction();

    // Confirm the session is still open — reject scans against an already-finalized lot
    $chk = $conn->prepare("SELECT status FROM qa_lot WHERE id = :id");
    $chk->execute([':id' => $inspection_id]);
    $lotStatus = $chk->fetchColumn();
    if ($lotStatus !== 'IN_PROGRESS') {
        $conn->rollBack();
        echo json_encode(['status'=>'error', 'message'=>'This lot has already been finalized.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO qa_process
        (serial_code, status, location, defect_code, severity, lot_out, scrap, inspection_id, created_at, parts_specification)
        VALUES (:serial_code, :status, :location, :defect_code, :severity, 0, 0, :inspection_id, :created_at, :parts_spec)");
    $stmt->execute([
        ':serial_code'   => $serial_code,
        ':status'        => $status,
        ':location'      => $location,
        ':defect_code'   => $defect_code,
        ':severity'      => $severity,
        ':inspection_id' => $inspection_id,
        ':created_at'    => date('Y-m-d H:i:s'),
        ':parts_spec'    => $parts_spec,
    ]);

    if ($major_count > 0 || $minor_count > 0) {
        $upd = $conn->prepare("UPDATE qa_lot SET defects_015 = defects_015 + :maj, defects_10 = defects_10 + :min WHERE id = :id");
        $upd->execute([':maj' => $major_count, ':min' => $minor_count, ':id' => $inspection_id]);
    }

    $conn->commit();
    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    $conn->rollBack();
    // 2627/2601 = SQL Server unique constraint violation codes
    if ($e->errorInfo[1] == 2627 || $e->errorInfo[1] == 2601) {
        echo json_encode(['status'=>'duplicate', 'message'=>'This serial was already scanned (possibly by another operator).']);
    } else {
        echo json_encode(['status'=>'error', 'message'=>'Database error: ' . $e->getMessage()]);
    }
}