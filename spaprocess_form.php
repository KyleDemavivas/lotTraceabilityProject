<?php
include 'db_connect.php';

$response = [
    'status' => 'error',
    'message' => '',
    'updated_stencil_stroke' => null,
    'updated_squeegee_stroke' => null
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        date_default_timezone_set('Asia/Manila');
        $created_at = date('Y-m-d H:i:s');

        $qr_code = strtoupper($_POST['qr_code']);
        $operator_name = $_POST['operator_name'];
        $shift = $_POST['shift'];
        $line = $_POST['line'];
        $assy_code = strtoupper($_POST['assy_code']);
        $model_name = strtoupper($_POST['model_name']);
        $kepi_lot = strtoupper($_POST['kepi_lot']);
        $serial_qty = $_POST['serial_qty'];
        $qr_count = $_POST['qr_count'];
        $qty_input = $_POST['qty_input'];
        $serial_paste = $_POST['serial_paste'];
        $serial_bonding = $_POST['serial_bonding'];
        $stencil_no = $_POST['stencil_no'];
        $squeegee_no = $_POST['squeegee_no'];
        $adhesive = $_POST['adhesive'];

        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM trace_process WHERE qr_code = :qr_code");
        $checkStmt->execute([':qr_code' => $qr_code]);
        if ($checkStmt->fetchColumn() > 0) {
            $response['message'] = 'QR Code already exists in Database.';
            echo json_encode($response);
            exit;
        }

        // Also check if QR already exists in spa_process
        $checkSpaStmt = $conn->prepare("SELECT COUNT(*) FROM spa_process WHERE qr_code = :qr_code");
        $checkSpaStmt->execute([':qr_code' => $qr_code]);
        if ($checkSpaStmt->fetchColumn() > 0) {
            $response['message'] = 'QR Code already exists in SPA Process.';
            echo json_encode($response);
            exit;
        }

        $stencilStrokeStmt = $conn->prepare("SELECT current_stroke FROM spa_process WHERE stencil_no = :stencil_no ORDER BY id DESC");
        $stencilStrokeStmt->execute([':stencil_no' => $stencil_no]);
        $current_stroke = $stencilStrokeStmt->fetchColumn();
        $current_stroke = $current_stroke ? $current_stroke + 1 : 1;

        $squeegeeStrokeStmt = $conn->prepare("SELECT squeegeecurrent_stroke FROM spa_process WHERE squeegee_no = :squeegee_no ORDER BY id DESC");
        $squeegeeStrokeStmt->execute([':squeegee_no' => $squeegee_no]);
        $squeegeecurrent_stroke = $squeegeeStrokeStmt->fetchColumn();
        $squeegeecurrent_stroke = $squeegeecurrent_stroke ? $squeegeecurrent_stroke + 1 : 1;

        $stencil = $conn->prepare("SELECT total_stroke FROM stencil_master WHERE stencil_no = :stencil_no AND deleted_at IS NULL");
        $stencil->execute([':stencil_no' => $stencil_no]);
        $stencil_total = $stencil->fetchColumn();
        if ($stencil_total !== false && $current_stroke > $stencil_total) {
            $response['message'] = "Error: Stencil stroke limit exceeded (max: $stencil_total)";
            echo json_encode($response);
            exit;
        }

        $squeegee = $conn->prepare("SELECT squeegeetotal_stroke FROM squeegee_master WHERE squeegee_no = :squeegee_no AND deleted_at IS NULL");
        $squeegee->execute([':squeegee_no' => $squeegee_no]);
        $squeegee_total = $squeegee->fetchColumn();
        if ($squeegee_total !== false && $squeegeecurrent_stroke > $squeegee_total) {
            $response['message'] = "Error: Squeegee stroke limit exceeded (max: $squeegee_total)";
            echo json_encode($response);
            exit;
        }

        $finalQtyStmt = $conn->prepare("SELECT final_qtyinput FROM spa_process WHERE kepi_lot = :kepi_lot ORDER BY id DESC");
        $finalQtyStmt->execute([':kepi_lot' => $kepi_lot]);
        $previous_final_qty = $finalQtyStmt->fetchColumn();
        $previous_final_qty = $previous_final_qty ? $previous_final_qty : 0;

        $final_qtyinput = $previous_final_qty + $qty_input;

        $insertSPA = $conn->prepare("INSERT INTO spa_process (
            qr_code, qty_input, final_qtyinput, operator_name, shift, line, assy_code, model_name,
            kepi_lot, serial_paste, serial_bonding, stencil_no, squeegee_no, adhesive,
            current_stroke, squeegeecurrent_stroke, created_at, spa_status
        ) VALUES (
            :qr_code, :qty_input, :final_qtyinput, :operator_name, :shift, :line, :assy_code, :model_name,
            :kepi_lot, :serial_paste, :serial_bonding, :stencil_no, :squeegee_no, :adhesive,
            :current_stroke, :squeegeecurrent_stroke, :created_at, 'GOOD'
        )");

        $insertSPA->execute([
            ':qr_code' => $qr_code,
            ':qty_input' => $qty_input,
            ':final_qtyinput' => $final_qtyinput,
            ':operator_name' => $operator_name,
            ':shift' => $shift,
            ':line' => $line,
            ':assy_code' => $assy_code,
            ':model_name' => $model_name,
            ':kepi_lot' => $kepi_lot,
            ':serial_paste' => $serial_paste,
            ':serial_bonding' => $serial_bonding,
            ':stencil_no' => $stencil_no,
            ':squeegee_no' => $squeegee_no,
            ':adhesive' => $adhesive,
            ':current_stroke' => $current_stroke,
            ':squeegeecurrent_stroke' => $squeegeecurrent_stroke,
            ':created_at' => $created_at
        ]);

        $conn->prepare("UPDATE stencil_master SET current_stroke = current_stroke + 1 WHERE stencil_no = :stencil_no AND deleted_at IS NULL")
            ->execute([':stencil_no' => $stencil_no]);

        $conn->prepare("UPDATE squeegee_master SET squeegeecurrent_stroke = squeegeecurrent_stroke + 1 WHERE squeegee_no = :squeegee_no AND deleted_at IS NULL")
            ->execute([':squeegee_no' => $squeegee_no]);

        $response['updated_stencil_stroke'] = $current_stroke;
        $response['updated_squeegee_stroke'] = $squeegeecurrent_stroke;

        $response['final_qtyinput'] = $final_qtyinput;

        $serialQuery = "SELECT serial_code1, serial_code2, serial_code3, serial_code4, serial_code5, serial_code6, 
        serial_code7, serial_code8, serial_code9, serial_code10, serial_code11, serial_code12, 
        serial_code13, serial_code14, serial_code15, serial_code16, serial_code17, serial_code18, 
        serial_code19, serial_code20, serial_code21, serial_code22, serial_code23, serial_code24
        FROM label_code WHERE qr_code = :qr_code";

        $serialStmt = $conn->prepare($serialQuery);
        $serialStmt->execute([':qr_code' => $qr_code]);
        $serials = $serialStmt->fetch(PDO::FETCH_ASSOC);

        if ($serials) {
            $insertTrace = $conn->prepare("INSERT INTO trace_process (qr_code, assy_code, model_name, kepi_lot, serial_code, created_at, spa_process) 
                                           VALUES (:qr_code, :assy_code, :model_name, :kepi_lot, :serial_code, :created_at, 'GOOD')");
            foreach ($serials as $serial_code) {
                if (!empty($serial_code)) {
                    $insertTrace->execute([
                        ':qr_code' => $qr_code,
                        ':assy_code' => $assy_code,
                        ':model_name' => $model_name,
                        ':kepi_lot' => $kepi_lot,
                        ':serial_code' => $serial_code,
                        ':created_at' => $created_at
                    ]);
                }
            }
        }

        $response['status'] = 'success';
        $response['message'] = 'Form submitted successfully!';
    } catch (PDOException $e) {
        $response['message'] = 'Error submitting form: ' . $e->getMessage();
    }
}

echo json_encode($response);
