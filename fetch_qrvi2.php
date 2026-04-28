<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

if (isset($_POST['qr_code'])) {
    $qr_code = $_POST['qr_code'];

    $query = 'SELECT assy_code, model_name, kepi_lot,operator_name, shift, line, qty_input
              FROM mounter_process WHERE qr_code = :qr_code';
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([':qr_code' => $qr_code]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $finalQtyQuery = 'SELECT final_qtyinput FROM vi_process WHERE kepi_lot = :kepi_lot AND final_qtyinput IS NOT NULL ORDER BY created_at DESC';

            $finalQtyStmt = $conn->prepare($finalQtyQuery);
            $finalQtyStmt->execute([':kepi_lot' => $row['kepi_lot']]);
            $finalQtyRow = $finalQtyStmt->fetch(PDO::FETCH_ASSOC);

            $final_qtyinput = $finalQtyRow ? $finalQtyRow['final_qtyinput'] : 'Error on Final Quantity';

            echo json_encode(array_merge(['success' => true, 'final_qtyinput' => $final_qtyinput], $row));
        } else {
            echo json_encode(['success' => false, 'message' => 'QR Code not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
