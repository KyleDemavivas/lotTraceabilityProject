<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["assy_code"])) {
    $assy_code = $_POST["assy_code"];

    $stmt = $conn->prepare("SELECT model_name, letter_allocation, serial_qty FROM model_data WHERE assy_code = ?");
    $stmt->execute([$assy_code]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        echo json_encode([
            "success" => true,
            "model_name" => $result["model_name"],
            "letter_allocation" => $result["letter_allocation"],
            "serial_qty" => $result["serial_qty"]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Invalid Assy Code. Please enter a valid one."]);
    }
    exit;
}
