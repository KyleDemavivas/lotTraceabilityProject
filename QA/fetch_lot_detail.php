<?php
include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$kepi_lot = $_POST['kepi_lot'] ?? '';
if (!$kepi_lot) { echo json_encode(['success' => false]); exit; }

try {
    // Lot header
    $stmt = $conn->prepare("
        SELECT TOP 1 inspection_method, sample_size, lot_result
        FROM qa_process WHERE kepi_lot = ?
    ");
    $stmt->execute([$kepi_lot]);
    $header = $stmt->fetch(PDO::FETCH_ASSOC);

    // Serials with defect info already flattened on the row
    $stmt = $conn->prepare("
        SELECT id, serial_code, status, location, defect_code, severity
        FROM qa_process
        WHERE kepi_lot = ?
        ORDER BY created_at ASC
    ");
    $stmt->execute([$kepi_lot]);
    $serials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Reshape flat columns into a defects array per serial,
    // since location/defect_code/severity are comma-separated lists
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

        // Clean up raw flat fields since they're now folded into 'defects'
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