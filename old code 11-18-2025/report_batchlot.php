<?php
include 'db_connect.php';
include 'sidebar.php';

$serial_code = $_GET['serial_code'] ?? '';

$model_name = '';
$assy_code  = '';
$batchlot   = '';
$process_data = [];

if ($serial_code != '') {

    $stmt = $conn->prepare("SELECT TOP 1 model_name, assy_code, qr_code FROM ai_process WHERE serial_code = ? ORDER BY created_at DESC");
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $model_name = $row['model_name'];
        $assy_code  = $row['assy_code'];
        $qr_code    = $row['qr_code'];
    } else {
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
        $batchlot = (!empty($row2['batchlot']) && $row2['batchlot'] !== "NO")
            ? $row2['batchlot']
            : "NO";
    } else {

        $batchlot = "NO";
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

    // Labelling data
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

    // AUTO INSPECTION
    $sql = "SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM ai_process WHERE serial_code = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            "process" => "AUTOMATIC INSERTION",
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
    <link rel="stylesheet" href="css/board_history.css">
</head>

<body>
    <div class="container">
        <div class="header">BOARD HISTORY</div>
        <div class="board-section">
            <form method="get">
                <label><b>LOT / SERIAL NO.:</b></label>
                <input type="text" name="serial_code" value="<?= htmlspecialchars($serial_code) ?>" required autocomplete="off" minlength="13" maxlength="13">
                <button type="submit">Search</button>
            </form>
            <p><b>MODEL:</b> <?= htmlspecialchars($model_name) ?></p>
            <p><b>ASSYCODE:</b> <?= htmlspecialchars($assy_code) ?></p>
            <p><b>BATCHLOT:</b>
                <span class="<?= $batchlot === 'NO' ? 'highlight' : '' ?>">
                    <?= ($serial_code == '' ? '' : htmlspecialchars($batchlot)) ?>
                </span>
            </p>
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

        <div class="repair-section">
            <div class="header">REPAIR HISTORY</div>

            <?php
            // SMT Repair
            $sql = "SELECT TOP 1 defect, location, board_number, operator_name, action_rp, repaired_by, verified_ll, verified_vi, created_at
                FROM repair_process WHERE serial_code = ? ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$serial_code]);
            $smt_repair = $stmt->fetch(PDO::FETCH_ASSOC);

            // AI Repair
            $sql = "SELECT TOP 1 defect, location, board_number, operator_name, action_rp, repaired_by, verified_ll, verified_vi, created_at
                FROM ai_repair WHERE serial_code = ? ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$serial_code]);
            $ai_repair = $stmt->fetch(PDO::FETCH_ASSOC);

            // HANDWORK Repair
            $sql = "SELECT TOP 1 defect, location, board_number, operator_name, action_rp, repaired_by, verified_ll, verified_vi, created_at
                FROM handwork_repair WHERE serial_code = ? ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$serial_code]);
            $hand_repair = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if ALL repairs are empty
            if (!$smt_repair && !$ai_repair && !$hand_repair): ?>
                <div style="text-align:center; color:red; font-weight:bold; margin:20px;">
                    NO REPAIR FOUND
                </div>
            <?php else: ?>

                <div class="table-container">
                    <h3>SMT REPAIR</h3>
                    <table>
                        <tr>
                            <th>DEFECT</th>
                            <th>LOCATION</th>
                            <th>BOARD NO.</th>
                            <th>ACTION</th>
                            <th>OPERATOR NAME</th>
                            <th>REPAIRER</th>
                            <th>LL VERIFIED BY</th>
                            <th>VI VERIFIED BY</th>
                            <th>DATE</th>
                        </tr>
                        <?php if ($smt_repair): ?>
                            <tr>
                                <td><?= htmlspecialchars($smt_repair['defect']) ?></td>
                                <td><?= htmlspecialchars($smt_repair['location']) ?></td>
                                <td><?= htmlspecialchars($smt_repair['board_number']) ?></td>
                                <td><?= htmlspecialchars($smt_repair['action_rp']) ?></td>
                                <td><?= htmlspecialchars($smt_repair['operator_name']) ?></td>
                                <td><?= htmlspecialchars($smt_repair['repaired_by']) ?></td>
                                <td><?= htmlspecialchars($smt_repair['verified_ll']) ?></td>
                                <td><?= htmlspecialchars($smt_repair['verified_vi']) ?></td>
                                <td><?= date("d-M g:i a", strtotime($smt_repair['created_at'])) ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align:center; color:red; font-weight:bold;">
                                    NO REPAIR FOUND
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div class="table-container">
                    <h3>AI REPAIR</h3>
                    <table>
                        <tr>
                            <th>DEFECT</th>
                            <th>LOCATION</th>
                            <th>BOARD NO.</th>
                            <th>ACTION</th>
                            <th>OPERATOR NAME</th>
                            <th>REPAIRER</th>
                            <th>LL VERIFIED BY</th>
                            <th>VI VERIFIED BY</th>
                            <th>DATE</th>
                        </tr>
                        <?php if ($ai_repair): ?>
                            <tr>
                                <td><?= htmlspecialchars($ai_repair['defect']) ?></td>
                                <td><?= htmlspecialchars($ai_repair['location']) ?></td>
                                <td><?= htmlspecialchars($ai_repair['board_number']) ?></td>
                                <td><?= htmlspecialchars($ai_repair['action_rp']) ?></td>
                                <td><?= htmlspecialchars($ai_repair['operator_name']) ?></td>
                                <td><?= htmlspecialchars($ai_repair['repaired_by']) ?></td>
                                <td><?= htmlspecialchars($ai_repair['verified_ll']) ?></td>
                                <td><?= htmlspecialchars($ai_repair['verified_vi']) ?></td>
                                <td><?= date("d-M g:i a", strtotime($ai_repair['created_at'])) ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align:center; color:red; font-weight:bold;">
                                    NO REPAIR FOUND
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

                <div class="table-container">
                    <h3>HANDWORK REPAIR</h3>
                    <table>
                        <tr>
                            <th>DEFECT</th>
                            <th>LOCATION</th>
                            <th>BOARD NO.</th>
                            <th>ACTION</th>
                            <th>OPERATOR NAME</th>
                            <th>REPAIRER</th>
                            <th>LL VERIFIED BY</th>
                            <th>VI VERIFIED BY</th>
                            <th>DATE</th>
                        </tr>
                        <?php if ($hand_repair): ?>
                            <tr>
                                <td><?= htmlspecialchars($hand_repair['defect']) ?></td>
                                <td><?= htmlspecialchars($hand_repair['location']) ?></td>
                                <td><?= htmlspecialchars($hand_repair['board_number']) ?></td>
                                <td><?= htmlspecialchars($hand_repair['action_rp']) ?></td>
                                <td><?= htmlspecialchars($hand_repair['operator_name']) ?></td>
                                <td><?= htmlspecialchars($hand_repair['repaired_by']) ?></td>
                                <td><?= htmlspecialchars($hand_repair['verified_ll']) ?></td>
                                <td><?= htmlspecialchars($hand_repair['verified_vi']) ?></td>
                                <td><?= date("d-M g:i a", strtotime($hand_repair['created_at'])) ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align:center; color:red; font-weight:bold;">
                                    NO REPAIR FOUND
                                </td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>

            <?php endif; ?>
        </div>


    </div>
</body>

</html>