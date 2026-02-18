<?php
include 'db_connect.php';

if (!isset($_POST['line']) || empty($_POST['line'])) {
    echo "<p>No line specified.</p>";
    exit;
}

$line = $_POST['line'];

date_default_timezone_set('Asia/Manila');
$currentHour = (int)date('G');
$currentDate = date('Y-m-d');

$isDayShift = ($currentHour >= 6 && $currentHour < 18);

if ($isDayShift) {
    $shiftLabel = "Day Shift (6 AM - 6 PM)";
    $startTime = "$currentDate 06:00:00";
    $endTime = "$currentDate 17:59:59";
} else {
    $shiftLabel = "Night Shift (6 PM - 6 AM)";
    $startTime = "$currentDate 18:00:00";
    $endTime = date('Y-m-d', strtotime('+1 day')) . " 05:59:59";
}

$hourlyQuery = "SELECT DATEPART(HOUR, created_at) AS hour_slot, COALESCE(SUM(TRY_CAST(qty_input AS INT)), 0) AS total_qty FROM mounter_process WHERE line = :line AND created_at BETWEEN :startTime AND :endTime
                GROUP BY DATEPART(HOUR, created_at) ORDER BY hour_slot";

$stmt = $conn->prepare($hourlyQuery);
$stmt->execute([
    ':line' => $line,
    ':startTime' => $startTime,
    ':endTime' => $endTime
]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$hourlyData = [];

if ($isDayShift) {
    for ($h = 6; $h <= 17; $h++) $hourlyData[$h] = 0;
} else {
    for ($h = 18; $h <= 23; $h++) $hourlyData[$h] = 0;
    for ($h = 0; $h <= 5; $h++) $hourlyData[$h] = 0;
}

foreach ($results as $row) {
    $hourlyData[$row['hour_slot']] = $row['total_qty'];
}
?>

<link rel="stylesheet" href="css/hourly_output.css">

<div class="hourly-output">
    <h4><strong><?= htmlspecialchars($line) ?></strong> Hourly Output <br><?= $shiftLabel ?></h4>
    <div class="table-wrapper">
        <table class="output-table">
            <thead>
                <tr>
                    <th>Time Range</th>
                    <th>Quantity</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalQty = 0;
                foreach ($hourlyData as $hour => $qty):
                    $start = date("g A", strtotime("$hour:00"));
                    $end = date("g A", strtotime(($hour + 1) . ":00"));
                    $totalQty += $qty;
                ?>
                    <tr>
                        <td><?= "$start - $end" ?></td>
                        <td><?= ($qty > 0 ? $qty : "No Production") ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td>Total</td>
                    <td><?= ($totalQty > 0 ? $totalQty : "No Production") ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>