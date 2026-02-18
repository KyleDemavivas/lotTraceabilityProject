<?php
$dsn = "sqlsrv:Server=localhost;Database=prod_traceability";
$username = "sa";
$password = "Kepi-123";

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $e->getMessage()]));
}

if (isset($_POST['user_username'])) {
    $user_username = trim($_POST['user_username']);

    $stmt = $conn->prepare("SELECT COUNT(*) FROM user_account WHERE user_username = :user_username");
    $stmt->bindParam(':user_username', $user_username, PDO::PARAM_STR);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(["status" => "exists", "message" => "Username already exists!"]);
    } else {
        echo json_encode(["status" => "available", "message" => "Username available."]);
    }
}
