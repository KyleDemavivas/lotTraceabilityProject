<?php include 'sidebar.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'].'/traceability/db_connect.ini'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Hourly Output</title>
    <link rel="stylesheet" href="css/hourly.css">
</head>

<body>
    <div class="output-container">
        <form method="POST" class="filter-form">
            <label for="line">Select Line:</label>
            <select name="line" id="line" required>
                <option value="">-- Select Line --</option>
                <?php
                $lineQuery = 'SELECT DISTINCT line FROM mounter_process ORDER BY line';
$lineStmt = $conn->prepare($lineQuery);
$lineStmt->execute();
$lines = $lineStmt->fetchAll(PDO::FETCH_COLUMN);
foreach ($lines as $line) {
    $selected = isset($_POST['line']) && $_POST['line'] == $line ? 'selected' : '';
    echo "<option value=\"$line\" $selected>$line</option>";
}
?>
            </select>

            <label for="shift">Select Shift:</label>
            <select name="shift" id="shift" required>
                <option value="">-- Select Shift --</option>
                <option value="day" <?php echo (isset($_POST['shift']) && $_POST['shift'] == 'day') ? 'selected' : ''; ?>>Day Shift (6 AM - 6 PM)</option>
                <option value="night" <?php echo (isset($_POST['shift']) && $_POST['shift'] == 'night') ? 'selected' : ''; ?>>Night Shift (6 PM - 6 AM)</option>
            </select>

            <label for="date">Select Date:</label>
            <input type="date" name="date" id="date" value="<?php echo $_POST['date'] ?? ''; ?>" required>

            <button type="submit">Show Output</button>
        </form>
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['line'], $_POST['shift'], $_POST['date'])) { ?>
            <form method="POST" action="download_hourlyoutput.php" style="margin-top: 10px;">
                <input type="hidden" name="line" value="<?php echo htmlspecialchars($_POST['line']); ?>">
                <input type="hidden" name="shift" value="<?php echo htmlspecialchars($_POST['shift']); ?>">
                <input type="hidden" name="date" value="<?php echo htmlspecialchars($_POST['date']); ?>">
                <button type="submit">Download</button>
            </form>
        <?php } ?>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['line'], $_POST['shift'], $_POST['date'])) {
            $line = $_POST['line'];
            $shift = $_POST['shift'];
            $selectedDate = $_POST['date'];

            $isDayShift = $shift === 'day';
            $shiftLabel = $isDayShift ? 'Day Shift (6 AM - 6 PM)' : 'Night Shift (6 PM - 6 AM)';

            if ($isDayShift) {
                $dateFilter = "created_at BETWEEN '{$selectedDate} 06:00:00' AND '{$selectedDate} 17:59:59'";
                $hours = range(6, 17);
            } else {
                $nextDate = date('Y-m-d', strtotime($selectedDate.' +1 day'));
                $dateFilter = "(created_at BETWEEN '{$selectedDate} 18:00:00' AND '{$selectedDate} 23:59:59' OR created_at BETWEEN '{$nextDate} 00:00:00' AND '{$nextDate} 05:59:59')";
                $hours = array_merge(range(18, 23), range(0, 5));
            }

            $hourlyQuery = "SELECT DATEPART(HOUR, created_at) AS hour_slot, COALESCE(SUM(TRY_CAST(qty_input AS INT)), 0) AS total_qty 
            FROM mounter_process WHERE line = :line AND $dateFilter GROUP BY DATEPART(HOUR, created_at) ORDER BY hour_slot";

            $stmt = $conn->prepare($hourlyQuery);
            $stmt->execute([':line' => $line]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $hourlyData = array_fill_keys($hours, 0);
            foreach ($results as $row) {
                $hourlyData[(int) $row['hour_slot']] = $row['total_qty'];
            }

            echo "<div class='table-wrapper'>";
            echo "<h2>Hourly Output for <strong>{$line}</strong> - {$shiftLabel} on ".date('F j, Y', strtotime($selectedDate)).'</h2>';
            echo "<table class='styled-table'>";
            echo '<thead><tr><th>Time Range</th><th>Quantity</th></tr></thead><tbody>';

            $totalQty = 0;
            foreach ($hourlyData as $hour => $qty) {
                $start = date('g A', strtotime("$hour:00"));
                $end = date('g A', strtotime(($hour + 1).':00'));
                echo "<tr><td>{$start} - {$end}</td><td>".($qty > 0 ? $qty : "<span class='no-production'>No Production</span>").'</td></tr>';
                $totalQty += $qty;
            }

            echo "<tr class='total-row'><td>Total</td><td>".($totalQty > 0 ? $totalQty : "<span class='no-production'>No Production</span>").'</td></tr>';
            echo '</tbody></table></div>';
        }
?>
    </div>
</body>

</html>