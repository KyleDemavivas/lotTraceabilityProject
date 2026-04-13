<?php

require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/sidebar.php';
require $_SERVER['DOCUMENT_ROOT'].'/traceabilitydev/db_connect.ini';

$query = "SELECT * FROM repair_boardanalysis WHERE status = 'll' AND status IS NOT NULL";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

     <link rel="stylesheet" href="css/repair_boardanalysis.css">
     <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
     <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

</head>
<body>

<script>

    $(document).ready(function(){
        
    })
</script>
</body>
</html>