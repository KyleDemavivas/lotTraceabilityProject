<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini';
include 'sidebar.php';

$serial_code = $_GET['serial_code'] ?? '';

$model_name = '';
$assy_code = '';
$batchlot = '';
$batchlot_data = [];
$process_data = [];
$scrapRecord = null;
$repairHistory = [];

if ($serial_code != '') {
    $stmt = $conn->prepare('SELECT TOP 1 model_name, assy_code, qr_code FROM ai_process WHERE serial_code = ? ORDER BY created_at DESC');
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $model_name = $row['model_name'];
        $assy_code = $row['assy_code'];
        $qr_code = $row['qr_code'];
    } else {
        $stmt = $conn->prepare('SELECT TOP 1 model_name, assy_code, qr_code FROM vi_process WHERE serial_code = ? ORDER BY created_at DESC');
        $stmt->execute([$serial_code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $model_name = $row['model_name'];
            $assy_code = $row['assy_code'];
            $qr_code = $row['qr_code'];
        } else {
            $qr_code = '';
        }
    }

    $stmt = $conn->prepare('SELECT * FROM repair_master WHERE serial_code = :serial_code ORDER BY created_at');
    $stmt->execute(['serial_code' => $serial_code]);
    $repairHistory = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($repairHistory as $row) {
        if ($row['status'] === 'SCRAP') {
            $scrapRecord = $row;
            break;
        }
    }

    // Batchlot check in repair_process
    $stmt2 = $conn->prepare('SELECT COUNT(*) AS count FROM fviss_process WHERE serial_code = ?');
    $stmt2->execute([$serial_code]);
    if ($row2 = $stmt2->fetch(PDO::FETCH_ASSOC)) {
        $batchlot = ($row2['count'] > 0) ? 'YES' : 'NO';
    } else {
        $batchlot = 'NO';
    }

    // SPA
    $spa_row = null;
    if ($qr_code != '') {
        $spa_sql = 'SELECT TOP 1 line, shift, spa_status AS judgement, operator_name AS operator, created_at FROM spa_process WHERE qr_code = ? ORDER BY created_at DESC';
        $stmt = $conn->prepare($spa_sql);
        $stmt->execute([$qr_code]);
        $spa_row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Labelling
    $label_row = null;
    if ($qr_code != '') {
        $label_sql = 'SELECT TOP 1 created_by AS operator, created_date AS created_at FROM label_code WHERE qr_code = ? ORDER BY created_date DESC';
        $stmt = $conn->prepare($label_sql);
        $stmt->execute([$qr_code]);
        $label_row = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Labelling data
    $process_data[] = [
        'process' => 'LABELLING',
        'line' => $spa_row['line'] ?? '',
        'shift' => $spa_row['shift'] ?? '',
        'judgement' => $spa_row['judgement'] ?? '',
        'operator' => $label_row['operator'] ?? '',
        'date_process' => isset($label_row['created_at']) ? date('d-M', strtotime($label_row['created_at'])) : '',
        'time_end_process' => isset($label_row['created_at']) ? date('g:i a', strtotime($label_row['created_at'])) : '',
    ];

    // SPA
    if ($spa_row) {
        $process_data[] = [
            'process' => 'SPA',
            'line' => $spa_row['line'],
            'shift' => $spa_row['shift'],
            'judgement' => $spa_row['judgement'],
            'operator' => $spa_row['operator'],
            'date_process' => date('d-M', strtotime($spa_row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($spa_row['created_at'])),
        ];
    }

    // Mounter
    if ($qr_code != '') {
        $sql = 'SELECT TOP 1 line, shift, mounter_status AS judgement, operator_name AS operator, created_at FROM mounter_process WHERE qr_code = ? ORDER BY created_at DESC';
        $stmt = $conn->prepare($sql);
        $stmt->execute([$qr_code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $process_data[] = [
                'process' => 'MOUNTER',
                'line' => $row['line'],
                'shift' => $row['shift'],
                'judgement' => $row['judgement'],
                'operator' => $row['operator'],
                'date_process' => date('d-M', strtotime($row['created_at'])),
                'time_end_process' => date('g:i a', strtotime($row['created_at'])),
            ];
        }
    }

    // SMT VI
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM vi_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'SMT VI',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // AUTOMATIC INSERTION
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM ai_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'AUTOMATIC INSERTION',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // MANUAL INSERTION
    $sql = 'SELECT TOP 1 line, shift, operator_name AS operator, created_at FROM mi_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'MANUAL INSERTION',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => 'GOOD',
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // MODIFICATOR 1
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM mod1_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'MODIFICATOR 1',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // MODIFICATOR 2
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM mod2_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'MODIFICATOR 2',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // FVISS NORMAL PROCESS
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM fviss_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'FVI SOLDERSIDE',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // PART SIDE
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM partside_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'PARTSIDE 1',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // PART SIDE 2
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM partside2_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'PARTSIDE 2',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // MICROSCOPE INSPECTION
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM micro_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'MICROSCOPE INSPECTION',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // WITHSTANDING INSULATION TEST
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM wi_process WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $process_data[] = [
            'process' => 'WITHSTANDING INSULATION TEST',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }
    // START OF BATCHLOT PROCESSES
    // FVISS BATCHLOT
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM fviss_batchlot WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $batchlot_data[] = [
            'process' => 'FVI SOLDER SIDE BATCHLOT',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // PARTSIDE 1 BATCHLOT
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM partside_batchlot WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $batchlot_data[] = [
            'process' => 'PARTSIDE 1 BATCHLOT',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // PARTSIDE 2 BATCHLOT
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM partside2_batchlot WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $batchlot_data[] = [
            'process' => 'PARTSIDE 2 BATCHLOT',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // MICROSCOPE BATCHLOT
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM micro_batchlot WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $batchlot_data[] = [
            'process' => 'MICROSCOPE INSPECTION BATCHLOT',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
        ];
    }

    // WITHSTANDING INSULATION BATCHLOT
    $sql = 'SELECT TOP 1 line, shift, board_status AS judgement, operator_name AS operator, created_at FROM wi_batchlot WHERE serial_code = ? ORDER BY created_at DESC';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$serial_code]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $batchlot_data[] = [
            'process' => 'WITHSTANDING INSULATION TEST BATCHLOT',
            'line' => $row['line'],
            'shift' => $row['shift'],
            'judgement' => $row['judgement'],
            'operator' => $row['operator'],
            'date_process' => date('d-M', strtotime($row['created_at'])),
            'time_end_process' => date('g:i a', strtotime($row['created_at'])),
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
                <input type="text" name="serial_code" value="<?php echo htmlspecialchars($serial_code); ?>" required autocomplete="off" minlength="13" maxlength="13">
                <button type="submit">Search</button>
            </form>
            <p><b>MODEL:</b> <?php echo htmlspecialchars($model_name); ?></p>
            <p><b>ASSYCODE:</b> <?php echo htmlspecialchars($assy_code); ?></p>
          
        </div>
        <div class="board-section" style="margin-top: 20px; margin-bottom: 20px">
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
                <?php
             $isScrap = $scrapRecord !== null;
$rows = $isScrap ? array_slice($process_data, 0, -1) : $process_data;
foreach ($rows as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['process']); ?></td>
                        <td><?php echo htmlspecialchars($row['line']); ?></td>
                        <td><?php echo htmlspecialchars($row['shift']); ?></td>
                        <td><?php echo htmlspecialchars($row['judgement']); ?></td>
                        <td><?php echo htmlspecialchars($row['operator']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_process']); ?></td>
                        <td><?php echo htmlspecialchars($row['time_end_process']); ?></td>
                    </tr>
                <?php } ?>
                <?php if ($isScrap) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($repairHistory[0]['process_location']); ?></td>
                        <td><?php echo htmlspecialchars($repairHistory[0]['line']); ?></td>
                        <td><?php echo htmlspecialchars($repairHistory[0]['shift']); ?></td>
                        <td><?php echo htmlspecialchars($repairHistory[0]['status']); ?></td>
                        <td><?php echo htmlspecialchars($repairHistory[0]['operator_name']); ?></td>
                        <td><?php echo htmlspecialchars(date('d-M', strtotime($repairHistory[0]['created_at']))); ?></td>
                        <td><?php echo htmlspecialchars(date('g:i A', strtotime($repairHistory[0]['created_at']))); ?></td>   
                    </tr>
                <?php } ?>
            </table>
                </div>
            <!--BATCHLOT HISTORY TABLE ONGOING DEVELOPEMENT-->
            <div class="board-section" style="margin-top: 20px; margin-bottom: 20px">
                <div class="header">BATCH LOT HISTORY</div>
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
                   <?php
$batchlotRows = $isScrap ? array_slice($batchlot_data, 0, -1) : $batchlot_data;
?>

<?php if (empty($batchlot_data)) { ?>
    <tr><td colspan='7' style='text-align: center;'>No batch lot history available.</td></tr>
<?php } else { ?>
    <?php foreach ($batchlotRows as $row) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['process']); ?></td>
            <td><?php echo htmlspecialchars($row['line']); ?></td>
            <td><?php echo htmlspecialchars($row['shift']); ?></td>
            <td><?php echo htmlspecialchars($row['judgement']); ?></td>
            <td><?php echo htmlspecialchars($row['operator']); ?></td>
            <td><?php echo htmlspecialchars($row['date_process']); ?></td>
            <td><?php echo htmlspecialchars($row['time_end_process']); ?></td>
        </tr>
    <?php } ?>
    <?php if ($isScrap) { ?>
        <tr>
            <td><?php echo htmlspecialchars($scrapRecord['process_location']); ?></td>
            <td><?php echo htmlspecialchars($scrapRecord['line']); ?></td>
            <td><?php echo htmlspecialchars($scrapRecord['shift']); ?></td>
            <td><?php echo htmlspecialchars($scrapRecord['status']); ?></td>
            <td><?php echo htmlspecialchars($scrapRecord['operator_name']); ?></td>
            <td><?php echo htmlspecialchars(date('d-M', strtotime($scrapRecord['created_at']))); ?></td>
            <td><?php echo htmlspecialchars(date('g:i A', strtotime($scrapRecord['created_at']))); ?></td>
        </tr>
    <?php } ?>
<?php } ?>
                </table>
            </div>
            <!--BATCH LOT HISTORY-->

            <div class="board-section">
                <div class="header">REPAIR HISTORY</div>
                <?php
// SMT Repair
$sql = 'SELECT defect, location, board_number, operator_name, action_rp, repaired_by, verified_ll, verified_vi, created_at
                FROM repair_process WHERE serial_code = ? ORDER BY created_at DESC';
$stmt = $conn->prepare($sql);
$stmt->execute([$serial_code]);
$smt_repairs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// AI Repair
$sql = 'SELECT TOP 1 defect, location, board_number, operator_name, action_rp, repaired_by, verified_ll, verified_vi, created_at
                                FROM ai_repair WHERE serial_code = ? ORDER BY created_at DESC';
$stmt = $conn->prepare($sql);
$stmt->execute([$serial_code]);
$ai_repair = $stmt->fetch(PDO::FETCH_ASSOC);

// HANDWORK Repair
$sql = 'SELECT TOP 1 defect, location, board_number, operator_name, action_rp, repaired_by, verified_ll, verified_vi, created_at
                                FROM handwork_repair WHERE serial_code = ? ORDER BY created_at DESC';
$stmt = $conn->prepare($sql);
$stmt->execute([$serial_code]);
$handwork_repair = $stmt->fetch(PDO::FETCH_ASSOC);

?>
                    <table style='font-size:13px;'>
                        <thead>
                            <tr>
                                <th>QR Code</th>
                                <th>Serial Code</th>
                                <th>Assembly Code</th>
                                <th>Process</th>
                                <th>Defect</th>
                                <th>Action Taken</th>
                                <th>Repaired By</th>
                                <th>Line Leader</th>
                                <th>Process Verifier</th>
                                <th>Date</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                if ($scrapRecord) {
                                    echo "<tr><td colspan='11' style='text-align: center; color: red; font-weight: bold;'><h2>This board has been scrapped at "
                                    .htmlspecialchars(date('F d, Y h:i A', strtotime($scrapRecord['created_at']))).' on '
                                    .htmlspecialchars($scrapRecord['process_location']).' process</h2> </td></tr>';

                                    return;
                                } elseif (empty($repairHistory)) {
                                    echo "<tr><td colspan='11' style='text-align: center;'>No repair history found for this board.</td></tr>";
                                }
?>
                            <?php foreach ($repairHistory as $row) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['qr_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['serial_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['assy_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['process_location']); ?></td>
                                    <td><?php echo htmlspecialchars($row['defect']); ?></td>
                                    <td><?php echo htmlspecialchars($row['action_rp']); ?></td>
                                    <td><?php echo htmlspecialchars($row['repaired_by']); ?></td>
                                    <td><?php echo $row['ll_verified'] ? htmlspecialchars($row['ll_verified']) : 'N/A'; ?> </td>
                                    <td><?php echo $row['process_lead'] ? htmlspecialchars($row['process_lead']) : 'N/A'; ?></td>
                                    <td><?php echo htmlspecialchars(date('F d, Y', strtotime($row['created_at']))); ?></td>
                                    <td><?php echo htmlspecialchars(date('h:i A', strtotime($row['created_at']))); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
            </div>

</body>

</html>