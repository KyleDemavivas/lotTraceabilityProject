<?php
include 'db_connect.php';

if (isset($_POST['qr_code'])) {
    $qr_code = $_POST['qr_code'];

    $query = "SELECT assy_code, model_name, kepi_lot, serial_qty, 
                     serial_code1, serial_code2, serial_code3, serial_code4, serial_code5, 
                     serial_code6, serial_code7, serial_code8, serial_code9, serial_code10, 
                     serial_code11, serial_code12, serial_code13, serial_code14, serial_code15, 
                     serial_code16, serial_code17, serial_code18, serial_code19, serial_code20, 
                     serial_code21, serial_code22, serial_code23, serial_code24
              FROM label_code WHERE qr_code = :qr_code";
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute([':qr_code' => $qr_code]);
        //fetch the final_qtyinput displayed in spa_process.php final qty input field
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $finalQtyQuery = "SELECT final_qtyinput FROM spa_process WHERE kepi_lot = :kepi_lot ORDER BY created_at DESC";

            $finalQtyStmt = $conn->prepare($finalQtyQuery);
            $finalQtyStmt->execute([':kepi_lot' => $row['kepi_lot']]);
            $finalQtyRow = $finalQtyStmt->fetch(PDO::FETCH_ASSOC);

            $final_qtyinput = $finalQtyRow ? $finalQtyRow['final_qtyinput'] : 0;

            echo json_encode(array_merge(['success' => true, 'final_qtyinput' => $final_qtyinput], $row));
        } else {
            echo json_encode(['success' => false, 'message' => 'QR Code not found']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
