<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$inspection_id = $_GET['inspection_id'] ?? 0;
if (!$inspection_id) { echo json_encode(['status'=>'error']); exit; }

$stmt = $conn->prepare("SELECT * FROM qa_lot WHERE id = :id");
$stmt->execute([':id' => $inspection_id]);
$lot = $stmt->fetch(PDO::FETCH_ASSOC);

$serialStmt = $conn->prepare("SELECT * FROM qa_process WHERE inspection_id = :id ORDER BY id ASC");
$serialStmt->execute([':id' => $inspection_id]);
$serials = $serialStmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['status'=>'success', 'lot'=>$lot, 'serials'=>$serials]);