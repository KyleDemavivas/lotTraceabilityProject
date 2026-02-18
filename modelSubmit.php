<?php
header('content-type: application/json');
include 'db_connect.php';
date_default_timezone_set('Asia/Manila');
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $assy_code = strtoupper($_POST['assy_code']);
    $model_name = strtoupper($_POST['model_name']);
    $letter_allocation = strtoupper($_POST['letter_allocation']);
    $serial_qty = $_POST['serial_qty'];
    $created_by = $_SESSION['user_namefl'] ?? 'Unknown';
    $created_date = date('Y-m-d H:i:s');

    try {
        $sql = "INSERT INTO model_data (assy_code, model_name, letter_allocation, serial_qty, created_by, created_date) 
                VALUES (:assy_code, :model_name, :letter_allocation, :serial_qty, :created_by, :created_date)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':assy_code'=>$assy_code,
            ':model_name'=>$model_name,
            ":letter_allocation"=>$letter_allocation,
            ":serial_qty"=>$serial_qty,
            ":created_by"=>$created_by,
            ":created_date"=>$created_date
        ]);
        
        $response['success']=true;
        $response['data']= 'Model Registration Successful';
        $response['message'] = 'Model was added successfully';
        echo json_encode($response);
        exit;
        
    } catch (PDOException $e) {
        $response['success']=false;
        $response['data']='Database Error';
        $response['message'] = $e->getMessage();
        echo json_encode($response);
        exit;
    }
}

?>