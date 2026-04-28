<?php

include $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['line'], $_POST['shift'], $_POST['date'])) {
    $line = $_POST['line'];
    $shift = $_POST['shift'];
    $selectedDate = $_POST['date'];

    $isDayShift = $shift === 'day';
    $shiftLabel = $isDayShift ? 'Day Shift (6 AM - 6 PM)' : 'Night Shift (6 PM - 6 AM)';
    $formattedDate = date('F j, Y', strtotime($selectedDate));
    $headerTitle = "Hourly Output for {$line} - {$shiftLabel} on {$formattedDate}";

    if ($isDayShift) {
        $dateFilter = "created_at BETWEEN '{$selectedDate} 06:00:00' AND '{$selectedDate} 17:59:59'";
        $hours = range(6, 17);
    } else {
        $nextDate = date('Y-m-d', strtotime($selectedDate.' +1 day'));
        $dateFilter = "(created_at BETWEEN '{$selectedDate} 18:00:00' AND '{$selectedDate} 23:59:59' OR created_at BETWEEN '{$nextDate} 00:00:00' AND '{$nextDate} 05:59:59')";
        $hours = array_merge(range(18, 23), range(0, 5));
    }

    $hourlyQuery = "SELECT DATEPART(HOUR, created_at) AS hour_slot, COALESCE(SUM(TRY_CAST(qty_input AS INT)), 0) AS total_qty FROM mounter_process WHERE line = :line AND $dateFilter 
        GROUP BY DATEPART(HOUR, created_at) ORDER BY hour_slot";

    $stmt = $conn->prepare($hourlyQuery);
    $stmt->execute([':line' => $line]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $hourlyData = array_fill_keys($hours, 0);
    foreach ($results as $row) {
        $hourlyData[(int) $row['hour_slot']] = $row['total_qty'];
    }

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=Hourly_Output_{$line}_{$selectedDate}_{$shift}.csv");

    $output = fopen('php://output', 'w');

    fputcsv($output, [$headerTitle]);
    fputcsv($output, []);
    fputcsv($output, ['Time Range', 'Quantity']);

    $totalQty = 0;
    foreach ($hourlyData as $hour => $qty) {
        $start = date('g A', strtotime("$hour:00"));
        $end = date('g A', strtotime(($hour + 1).':00'));
        fputcsv($output, ["$start - $end", $qty]);
        $totalQty += $qty;
    }

    fputcsv($output, ['Total', $totalQty]);

    fclose($output);
    exit;
} else {
    echo 'Invalid request.';
    exit;
}
