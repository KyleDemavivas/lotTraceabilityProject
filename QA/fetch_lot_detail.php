<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$inspection_id = $_POST['inspection_id'] ?? '';
if (!$inspection_id) { 
    echo json_encode(['success' => false, 'message' => 'Missing inspection_id.']); 
    exit; 
}

try {
    // 1. Combined Header Query — Gets all original and new lot-level columns at once
    $stmt = $conn->prepare("
        SELECT id, kepi_lot, attempt_number, model, inspection_method, code_letter,
               sample_size, lot_quantity, line, shift, operator_id,
               defects_015, defects_10, lot_result, created_at,
               customer, assy_no, reference_no, inspection_level,
               judgement, parts_appearance, pcb_appearance, solder_condition,
               labels_markings, subassembly_condition, package_condition
        FROM qa_lot 
        WHERE id = ?
    ");
    $stmt->execute([$inspection_id]);
    $header = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$header) {
        echo json_encode(['success' => false, 'message' => 'Inspection not found.']);
        exit;
    }

    // 2. Serial query — add parts_specification
    $stmt = $conn->prepare("
        SELECT id, serial_code, status, location, defect_code, severity,
               lot_out, scrap, parts_specification
        FROM qa_process
        WHERE inspection_id = ?
        ORDER BY created_at ASC
    ");
    $stmt->execute([$inspection_id]);
    $serials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Reshape flat columns into a defects array per serial
    foreach ($serials as &$s) {
        if (!empty($s['defect_code'])) {
            $defectCodes = array_map('trim', explode(',', $s['defect_code']));
            $locations   = !empty($s['location']) ? array_map('trim', explode(',', $s['location'])) : [];
            $severities  = !empty($s['severity']) ? array_map('trim', explode(',', $s['severity'])) : [];

            $defects = [];
            foreach ($defectCodes as $i => $code) {
                $defects[] = [
                    'defect_code' => $code,
                    'location'    => $locations[$i] ?? '',
                    'severity'    => $severities[$i] ?? '',
                ];
            }
            $s['defects'] = $defects;
        } else {
            $s['defects'] = [];
        }

        unset($s['defect_code'], $s['location'], $s['severity']);
    }

    echo json_encode([
        'success' => true,
        'data'    => array_merge($header, ['serials' => $serials])
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
exit;