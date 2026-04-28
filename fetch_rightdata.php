<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
header('Content-Type: application/json');

$response = ['success' => false];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['stencil_no'])) {
            $stencil_no = trim($_POST['stencil_no']);

            $stmt = $conn->prepare('SELECT total_stroke, current_stroke FROM stencil_master WHERE stencil_no = :stencil_no AND deleted_at IS NULL');
            $stmt->execute([':stencil_no' => $stencil_no]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $response['success'] = true;
                $response['total_stroke'] = $row['total_stroke'];
                $response['current_stroke'] = $row['current_stroke'];
            } else {
                $response['stencil_message'] = 'No stencil data found';
            }

            $stmt = $conn->prepare('SELECT current_stroke FROM spa_process WHERE stencil_no = :stencil_no ORDER BY id DESC LIMIT 1');
            $stmt->execute([':stencil_no' => $stencil_no]);
            $last = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($last) {
                $response['last_stencil_stroke_spa'] = $last['current_stroke'];
            }
        }

        if (!empty($_POST['squeegee_no'])) {
            $squeegee_no = trim($_POST['squeegee_no']);

            $stmt = $conn->prepare('SELECT squeegeetotal_stroke, squeegeecurrent_stroke FROM squeegee_master WHERE squeegee_no = :squeegee_no AND deleted_at IS NULL');
            $stmt->execute([':squeegee_no' => $squeegee_no]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $response['success'] = true;
                $response['squeegeetotal_stroke'] = $row['squeegeetotal_stroke'];
                $response['squeegeecurrent_stroke'] = $row['squeegeecurrent_stroke'];
            } else {
                $response['squeegee_message'] = 'No squeegee data found';
            }

            $stmt = $conn->prepare('SELECT squeegeecurrent_stroke FROM spa_process WHERE squeegee_no = :squeegee_no ORDER BY id DESC LIMIT 1');
            $stmt->execute([':squeegee_no' => $squeegee_no]);
            $last = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($last) {
                $response['last_squeegee_stroke_spa'] = $last['squeegeecurrent_stroke'];
            }
        }

        if (!empty($_POST['serial_paste'])) {
            $serial_paste = trim($_POST['serial_paste']);
            $stmt = $conn->prepare('SELECT solder_paste, part_lot, time_pulledout, time_use FROM solderpaste_master WHERE serial_paste = :serial_paste AND deleted_at IS NULL');
            $stmt->execute([':serial_paste' => $serial_paste]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $response['success'] = true;
                $response['solder_paste'] = $row['solder_paste'];
                $response['part_lot'] = $row['part_lot'];
                $response['time_pulledout'] = $row['time_pulledout'];
                $response['time_use'] = $row['time_use'];
            } else {
                $response['solder_message'] = 'No solder paste data found';
            }
        }

        if (!empty($_POST['serial_bonding'])) {
            $serial_bonding = trim($_POST['serial_bonding']);
            $stmt = $conn->prepare('SELECT bonding, part_lot, time_pulledout, time_use FROM bonding_master WHERE serial_bonding = :serial_bonding AND deleted_at IS NULL');
            $stmt->execute([':serial_bonding' => $serial_bonding]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $response['success'] = true;
                $response['bonding'] = $row['bonding'];
                $response['part_lot'] = $row['part_lot'];
                $response['time_pulledout'] = $row['time_pulledout'];
                $response['time_use'] = $row['time_use'];
            } else {
                $response['bonding_message'] = 'No bonding data found';
            }
        }
    } else {
        $response['message'] = 'Invalid request method.';
    }
} catch (PDOException $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
