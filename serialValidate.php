<?php
include "/traceability/sidebar.php";
require "/traceability/db_connect.php";

if(!(isset($_SESSION['user_namefl']))){
    header("Location: login.php");
    exit();
}

if (isset($_POST['serial_code'])) {
    $serial_code = $_POST['serial_code'];
    $serial_code = trim($serial_code);
    $serial_code = strtoupper($serial_code);
    $serial_code = str_replace(" ", "", $serial_code);

    try{
        $stmt = $conn->prepare("SELECT FROM fviss_process WHERE serial_code = :serial_code");
        $stmt->execute(['serial_code' => $serial_code]);
        $serialData = $stmt->fetchAll();

        switch($_POST['process']){
            case 'FVISS':
                break;
            case 'PARTSIDE 1':
                break;
            case 'PARTSIDE 2':
                break;
            case 'MICRO':
                break;
            case 'WI':
                break;
                
                default:
                break;
        }


    }
    catch(PDOException $e){
        echo json_encode(["success"=> false, "message"=> $e->getMessage()]);
    }
}
?>