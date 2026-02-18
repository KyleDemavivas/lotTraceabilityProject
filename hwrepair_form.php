<?php include 'sidebar.php'; ?>
<?php
if (!isset($_SESSION['user_namefl'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$defects = [];
try {
    $stmt = $conn->query("SELECT defect FROM defect_master ORDER BY defect ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $defects[] = $row['defect'];
    }
} catch (PDOException $e) {
    die("Error fetching defects: " . $e->getMessage());
}

$locations = [];
try {
    $stmt = $conn->query("SELECT location FROM location_master ORDER BY location ASC");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $locations[] = $row['location'];
    }
} catch (PDOException $e) {
    die("Error fetching locations: " . $e->getMessage());
}
?>