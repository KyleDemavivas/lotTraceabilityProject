<?php
try {
    $conn = new PDO("sqlsrv:server=localhost,1433;Database=prod_traceability", "sa", "Kepi-123");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Hello!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
