<?php
session_start();

if (!isset($_SESSION['user_namefl'])) {
    header('Location: login.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Traceability</title>
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class="sidebar" id="sidebar">
        <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
        <br>
        <ul class="menu">
            <li class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
                <a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a>
            </li>

            <li class="dropdown <?= in_array($current_page, ['add_model.php', 'view_model.php']) ? 'active open' : '' ?>">
                <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-cube"></i> <span>Model Master ▾</span></a>
                <ul class="submenu">
                    <li class="<?= ($current_page == 'add_model.php') ? 'active' : '' ?>"><a href="add_model.php">Add Model</a></li>
                    <li class="<?= ($current_page == 'view_model.php') ? 'active' : '' ?>"><a href="view_model.php">View Model</a></li>
                </ul>
            </li>

            <li class="<?= ($current_page == 'label_registration.php') ? 'active' : '' ?>">
                <a href="label_registration.php"><i class="fas fa-tag"></i> <span>Label Registration</span></a>
            </li>

            <li class="<?= ($current_page == 'viewlabel_registration.php') ? 'active' : '' ?>">
                <a href="viewlabel_registration.php"><i class="fas fa-tags"></i> <span>View Label Registration</span></a>
            </li>

            <li class="<?= ($current_page == 'spa_process.php') ? 'active' : '' ?>">
                <a href="spa_process.php"><i class="fas fa-spa"></i> <span>SPA</span></a>
            </li>

            <li class="<?= ($current_page == 'mounter.php') ? 'active' : '' ?>">
                <a href="mounter.php"><i class="fas fa-microchip"></i> <span>Mounter</span></a>
            </li>

            <li class="<?= ($current_page == 'hourly_output.php') ? 'active' : '' ?>">
                <a href="hourly_output.php"><i class="fas fa-clock"></i> <span>Hourly Output</span></a>
            </li>

            <li class="<?= ($current_page == 'processed_lot.php') ? 'active' : '' ?>">
                <a href="processed_lot.php"><i class="fas fa-layer-group"></i> <span>Processed Lot</span></a>
            </li>

            <li class="<?= ($current_page == 'vi_process.php') ? 'active' : '' ?>">
                <a href="vi_process.php"><i class="fas fa-eye"></i> <span>Visual Inspection</span></a>
            </li>

            <li class="<?= ($current_page == 'repair_process.php') ? 'active' : '' ?>">
                <a href="repair_process.php"><i class="fas fa-wrench"></i> <span>SMT Repair</span></a>
            </li>

            <li class="<?= ($current_page == 'ng_verification.php') ? 'active' : '' ?>">
                <a href="ng_verification.php"><i class="fas fa-wrench"></i> <span>NG Verification</span></a>
            </li>

            <li class="<?= ($current_page == 'verify_repairLL.php') ? 'active' : '' ?>">
                <a href="verify_repairLL.php"><i class="fas fa-gavel"></i> <span>LL Verify Repair</span></a>
            </li>

            <li class="<?= ($current_page == 'vi_repair.php') ? 'active' : '' ?>">
                <a href="vi_repair.php"><i class="fas fa-gavel"></i> <span>SMT VI Repair</span></a>
            </li>

            <li class="<?= ($current_page == 'ai_process.php') ? 'active' : '' ?>">
                <a href="ai_process.php"><i class="fas fa-robot"></i> <span>Automatic Insertion</span></a>
            </li>

            <li class="<?= ($current_page == 'ai_repair_process.php') ? 'active' : '' ?>">
                <a href="ai_repair_process.php"><i class="fas fa-wrench"></i> <span>AI Repair</span></a>
            </li>

            <li class="<?= ($current_page == 'ai_ngverification.php') ? 'active' : '' ?>">
                <a href="ai_ngverification.php"><i class="fas fa-wrench"></i> <span>AI NG Verification</span></a>
            </li>

            <li class="<?= ($current_page == 'ai_verify_repairLL.php') ? 'active' : '' ?>">
                <a href="ai_verify_repairLL.php"><i class="fas fa-gavel"></i> <span>AI LL Verify Repair</span></a>
            </li>

            <li class="<?= ($current_page == 'ai_repair.php') ? 'active' : '' ?>">
                <a href="ai_repair.php"><i class="fas fa-gavel"></i> <span>SMT AI Repair</span></a>
            </li>

            <li class="dropdown <?= in_array($current_page, ['stencil_master.php', 'squeegee_master.php']) ? 'active open' : '' ?>">
                <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-window-restore"></i> <span>Stencil/Squeegee ▾</span></a>
                <ul class="submenu">
                    <li class="<?= ($current_page == 'stencil_master.php') ? 'active' : '' ?>"><a href="stencil_master.php">Stencil</a></li>
                    <li class="<?= ($current_page == 'squeegee_master.php') ? 'active' : '' ?>"><a href="squeegee_master.php">Squeegee</a></li>
                </ul>
            </li>

            <li class="dropdown <?= in_array($current_page, ['ad_solderpaste.php', 'ad_bonding.php']) ? 'active open' : '' ?>">
                <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-thermometer-0"></i> <span>Adhesive ▾</span></a>
                <ul class="submenu">
                    <li class="<?= ($current_page == 'ad_solderpaste.php') ? 'active' : '' ?>"><a href="ad_solderpaste.php">Solder Paste</a></li>
                    <li class="<?= ($current_page == 'ad_bonding.php') ? 'active' : '' ?>"><a href="ad_bonding.php">Bonding</a></li>
                </ul>
            </li>

            <li class="dropdown <?= in_array($current_page, ['report_repair.php', 'report_batchlot.php']) ? 'active open' : '' ?>">
                <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-folder-open"></i> <span>Report ▾</span></a>
                <ul class="submenu">
                    <li class="<?= ($current_page == 'report_repair.php') ? 'active' : '' ?>"><a href="report_repair.php">SMT Repair Report</a></li>
                    <li class="<?= ($current_page == 'report_batchlot.php') ? 'active' : '' ?>"><a href="report_batchlot.php">Batch Lot Report</a></li>
                </ul>
            </li>

            <li class="<?= ($current_page == 'account_settings.php') ? 'active' : '' ?>">
                <a href="account_settings.php"><i class="fas fa-user-cog"></i> <span>Account Settings</span></a>
            </li>

            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
        </ul>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('expanded');
        }

        function toggleDropdown(event) {
            event.preventDefault();
            let parent = event.target.closest('.dropdown');
            parent.classList.toggle('open');
        }
    </script>
</body>

</html>