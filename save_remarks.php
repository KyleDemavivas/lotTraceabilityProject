<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kepi_lot = $_POST['kepi_lot'] ?? '';
    $remarks = $_POST['remarks'] ?? '';

    if ($kepi_lot && $remarks) {
        $query = 'UPDATE mounter_process SET process_remarks = :remarks WHERE kepi_lot = :kepi_lot';
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([
            'remarks' => $remarks,
            'kepi_lot' => $kepi_lot,
        ]);

        echo $result
            ? 'Remarks saved successfully for lot '.htmlspecialchars($kepi_lot)
            : 'Failed to save remarks.';
    } else {
        echo 'Invalid input.';
    }
} else {
    echo 'Invalid request method.';
}
