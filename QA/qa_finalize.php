<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');
date_default_timezone_set('Asia/Manila');

$inspection_id = $_POST['inspection_id'] ?? 0;
$lot_result     = $_POST['lot_result'] ?? 'ACCEPT';
$judgement      = $_POST['judgement'] ?? '';
// ...other finalize fields same as before

try {
    $stmt = $conn->prepare("UPDATE qa_lot
        SET status = 'FINALIZED', lot_result = :lot_result, judgement = :judgement,
            parts_appearance = :pa, pcb_appearance = :pcba, solder_condition = :sc,
            labels_markings = :lm, subassembly_condition = :sac, package_condition = :pc,
            finalized_at = :fin_at
        WHERE id = :id AND status = 'IN_PROGRESS'");
    $stmt->execute([
        ':lot_result' => $lot_result,
        ':judgement'  => $judgement,
        ':pa'  => $_POST['parts_appearance'] ?? null,
        ':pcba'=> $_POST['pcb_appearance'] ?? null,
        ':sc'  => $_POST['solder_condition'] ?? null,
        ':lm'  => $_POST['labels_markings'] ?? null,
        ':sac' => $_POST['subassembly_condition'] ?? null,
        ':pc'  => $_POST['package_condition'] ?? null,
        ':fin_at' => date('Y-m-d H:i:s'),
        ':id' => $inspection_id,
    ]);

    if ($stmt->rowCount() === 0) {
        echo json_encode(['status'=>'error', 'message'=>'This lot was already finalized by another operator.']);
    } else {
        echo json_encode(['status'=>'success']);
    }
} catch (PDOException $e) {
    echo json_encode(['status'=>'error', 'message'=>'Database error: ' . $e->getMessage()]);
}