<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assy_code = strtoupper(trim($_POST['assy_code']));
    $model_name = strtoupper(trim($_POST['model_name']));
    $letter_allocation = strtoupper(trim($_POST['letter_allocation']));
    $serial_qty = min(max((int) $_POST['serial_qty'], 1), 24);
    $kepi_lot = strtoupper(trim($_POST['kepi_lot']));
    $qr_code = strtoupper(trim($_POST['qr_code']));

    date_default_timezone_set('Asia/Manila');
    $created_date = date('Y-m-d H:i:s');
    $created_by = isset($_SESSION['user_namefl']) ? $_SESSION['user_namefl'] : 'Unknown';

    $serial_codes = [];
    $errors = [];

    for ($i = 1; $i <= $serial_qty; ++$i) {
        $serial_code = strtoupper(trim($_POST["serial_code$i"]));

        if (strlen($serial_code) !== 13) {
            $errors["serial_code$i"] = "Serial Code $i must be exactly 13 characters long.";
        }

        if (in_array($serial_code, $serial_codes)) {
            $errors["serial_code$i"] = 'Duplicate serial code found.';
        }

        $serial_codes[] = $serial_code;
    }

    if (!empty($errors)) {
        echo json_encode(['status' => 'error', 'errors' => $errors]);
        exit;
    }

    try {
        $qrStmt = $conn->prepare('SELECT qr_code FROM label_code');
        $qrStmt->execute();
        $existing_qrs = $qrStmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($existing_qrs as $existing_qr) {
            if (strtoupper(trim($existing_qr)) === $qr_code) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'QR Code already exists in the database.',
                ]);
                exit;
            }
        }

        $existing_serials = [];
        $serialPlaceholders = implode(',', array_fill(0, count($serial_codes), '?'));
        $conditions = [];

        for ($i = 1; $i <= 24; ++$i) {
            $conditions[] = "serial_code$i IN ($serialPlaceholders)";
        }

        $sql = 'SELECT * FROM label_code WHERE '.implode(' OR ', $conditions);
        $stmt = $conn->prepare($sql);

        $bindValues = [];
        for ($i = 0; $i < 24; ++$i) {
            $bindValues = array_merge($bindValues, $serial_codes);
        }

        $stmt->execute($bindValues);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            for ($i = 1; $i <= 24; ++$i) {
                $value = $row["serial_code$i"];
                if ($value && in_array($value, $serial_codes) && !in_array($value, $existing_serials)) {
                    $existing_serials[] = $value;
                }
            }
        }

        if (!empty($existing_serials)) {
            $duplicate_serials = implode(', ', $existing_serials);
            echo json_encode([
                'status' => 'error',
                'message' => "The following serial codes already exist in the database: $duplicate_serials",
            ]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error checking existing data: '.$e->getMessage(),
        ]);
        exit;
    }
    $columns = 'assy_code, model_name, letter_allocation, serial_qty, kepi_lot, qr_code, created_by, created_date';
    $placeholders = ':assy_code, :model_name, :letter_allocation, :serial_qty, :kepi_lot, :qr_code, :created_by, :created_date';
    $params = [
        ':assy_code' => $assy_code,
        ':model_name' => $model_name,
        ':letter_allocation' => $letter_allocation,
        ':serial_qty' => $serial_qty,
        ':kepi_lot' => $kepi_lot,
        ':qr_code' => $qr_code,
        ':created_by' => $created_by,
        ':created_date' => $created_date,
    ];

    for ($i = 1; $i <= $serial_qty; ++$i) {
        $columns .= ", serial_code$i";
        $placeholders .= ", :serial_code$i";
        $params[":serial_code$i"] = $serial_codes[$i - 1];
    }

    try {
        $sql = "INSERT INTO label_code ($columns) VALUES ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error submitting form: '.$e->getMessage(),
        ]);
    }

    $conn = null;
}
