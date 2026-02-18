<?php
include '../db_connect.php';
include '../sidebar.php';
$serial_code = $_GET['serial_code'] ?? '';

$model_name = '';
$assy_code = '';
$batchlot = 'NO';
$process_data = [];

if ($serial_code != '') {
    // Get model & assy_code from ai_process (or vi_process as backup)
    $stmt = $conn->prepare("SELECT TOP 1 model_name, assy_code, qr_code FROM ai_process WHERE serial_code = ? ORDER BY created_at DESC");
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $model_name = $row['model_name'];
        $assy_code  = $row['assy_code'];
        $qr_code    = $row['qr_code'];
    } else {
        // Try from vi_process if not found
        $stmt = $conn->prepare("SELECT TOP 1 model_name, assy_code, qr_code FROM vi_process WHERE serial_code = ? ORDER BY created_at DESC");
        $stmt->execute([$serial_code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $model_name = $row['model_name'];
            $assy_code  = $row['assy_code'];
            $qr_code    = $row['qr_code'];
        } else {
            $qr_code = '';
        }
    }

    // Batchlot check in repair_process
    $stmt2 = $conn->prepare("SELECT TOP 1 batchlot FROM repair_process WHERE serial_code = ? ORDER BY created_at DESC");
    $stmt2->execute([$serial_code]);
    if ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $batchlot = (!empty($row2['batchlot']) && $row2['batchlot'] !== "NO") ? $row2['batchlot'] : "NO";
    }

    // SPA
    $spa_row = null;
    if ($qr_code != '') {
        $spa_sql = "SELECT TOP 1 line, shift, spa_status AS judgement, operator_name AS operator, created_at FROM spa_process WHERE qr_code = ? ORDER BY created_at DESC";
        $stmt = $conn->prepare($spa_sql);
        $stmt->execute([$qr_code]);
        $spa_row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Labelling
    $label_row = null;
    if ($qr_code != '') {
        $label_sql = "SELECT TOP 1 created_by AS operator, created_date AS created_at FROM label_code WHERE qr_code = ? ORDER BY created_date DESC";
        $stmt = $conn->prepare($label_sql);
        $stmt->execute([$qr_code]);
        $label_row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    $process_data[] = [
        "process" => "LABELLING",
        "line" => $spa_row['line'] ?? '',
        "shift" => $spa_row['shift'] ?? '',
        "judgement" => $spa_row['judgement'] ?? '',
        "operator" => $label_row['operator'] ?? '',
        "date_process" => isset($label_row['created_at']) ? date("d-M", strtotime($label_row['created_at'])) : '',
        "time_end_process" => isset($label_row['created_at']) ? date("g:i a", strtotime($label_row['created_at'])) : ''
    ];
    // SPA
    if ($spa_row) {
        $process_data[] = [
            "process" => "SPA",
            "line" => $spa_row['line'],
            "shift" => $spa_row['shift'],
            "judgement" => $spa_row['judgement'],
            "operator" => $spa_row['operator'],
            "date_process" => date("d-M", strtotime($spa_row['created_at'])),
            "time_end_process" => date("g:i a", strtotime($spa_row['created_at']))
        ];
    }
    // Mounter
    if ($qr_code != '') {
        $sql = "SELECT TOP 1 line, shift, mounter_status AS judgement, operator_name AS operator, created_at FROM mounter_process WHERE qr_code = ? ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$qr_code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $process_data[] = [
                "process" => "MOUNTER",
                "line" => $row['line'],
                "shift" => $row['shift'],
                "judgement" => $row['judgement'],
                "operator" => $row['operator'],
                "date_process" => date("d-M", strtotime($row['created_at'])),
                "time_end_process" => date("g:i a", strtotime($row['created_at']))
            ];
        }
    }
    // SMT VI
    $sql = "SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM vi_process WHERE serial_code = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            "process" => "SMT VI",
            "line" => $row['line'],
            "shift" => $row['shift'],
            "judgement" => $row['judgement'],
            "operator" => $row['operator'],
            "date_process" => date("d-M", strtotime($row['created_at'])),
            "time_end_process" => date("g:i a", strtotime($row['created_at']))
        ];
    }
    // AUTO INSERTION
    $sql = "SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM ai_process WHERE serial_code = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            "process" => "AUTO INSERTION",
            "line" => $row['line'],
            "shift" => $row['shift'],
            "judgement" => $row['judgement'],
            "operator" => $row['operator'],
            "date_process" => date("d-M", strtotime($row['created_at'])),
            "time_end_process" => date("g:i a", strtotime($row['created_at']))
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Board History</title>
    <link rel="stylesheet" href="../css/board_history.css">
</head>

<body>

    <div class="header">BOARD HISTORY</div>
    <div class="board-section">
        <form method="get">
            <label><b>LOT / SERIAL NO.:</b></label>
            <input type="text" name="serial_code" value="<?= htmlspecialchars($serial_code) ?>" required autocomplete="off" minlength="13" maxlength="13">
            <button type="submit">Search</button>
        </form>
        <p><b>MODEL:</b> <?= htmlspecialchars($model_name) ?></p>
        <p><b>ASSYCODE:</b> <?= htmlspecialchars($assy_code) ?></p>
        <p><b>BATCHLOT:</b> <span class="<?= $batchlot == 'NO' ? 'highlight' : '' ?>"><?= htmlspecialchars($batchlot) ?></span></p>
    </div>
    <table>
        <tr>
            <th>PROCESS</th>
            <th>LINE</th>
            <th>SHIFT</th>
            <th>JUDGEMENT</th>
            <th>OPERATOR</th>
            <th>DATE PROCESS</th>
            <th>TIME END PROCESS</th>
        </tr>
        <?php foreach ($process_data as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['process']) ?></td>
                <td><?= htmlspecialchars($row['line']) ?></td>
                <td><?= htmlspecialchars($row['shift']) ?></td>
                <td><?= htmlspecialchars($row['judgement']) ?></td>
                <td><?= htmlspecialchars($row['operator']) ?></td>
                <td><?= htmlspecialchars($row['date_process']) ?></td>
                <td><?= htmlspecialchars($row['time_end_process']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

</body>

</html>