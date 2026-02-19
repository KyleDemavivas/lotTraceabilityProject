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
    <link rel="stylesheet" href="/traceability/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class="sidebar" id="sidebar">
        <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
        <br><br>
        <br>
        <?php if ($_SESSION['user_process'] === 'ADMIN'): ?>
            <ul class="menu">
                <li class="dropdown <?= in_array($current_page, ['/traceability/register.php', '/traceability/useraccounts.php']) ? 'active open' : '' ?>">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-user-cog"></i> <span>Admin Panel ▾</span></a>
                    <ul class="submenu">
                        <li class="<?= ($current_page == '/traceability/register.php') ? 'active' : '' ?>"><a href="/traceability/register.php">Register User</a></li>
                        <li class="<?= ($current_page == '/traceability/useraccounts.php') ? 'active' : '' ?>"><a href="/traceability/useraccounts.php">Users</a></li>
                    </ul>
                </li>

                <li class="dropdown <?= in_array($current_page, ['/traceability/add_model.php', '/traceability/view_model.php']) ? 'active open' : '' ?>">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-cube"></i> <span>Model Master ▾</span></a>
                    <ul class="submenu">
                        <li class="<?= ($current_page == '/traceability/add_model.php') ? 'active' : '' ?>"><a href="/traceability/add_model.php">Add Model</a></li>
                        <li class="<?= ($current_page == '/traceability/view_model.php') ? 'active' : '' ?>"><a href="/traceability/view_model.php">View Model</a></li>
                    </ul>
                </li>

                <li class="<?= ($current_page == '/traceability/label_registration.php') ? 'active' : '' ?>">
                    <a href="/traceability/label_registration.php"><i class="fas fa-tag"></i> <span>Label Registration</span></a>
                </li>

                <li class="<?= ($current_page == '/traceability/viewlabel_registration.php') ? 'active' : '' ?>">
                    <a href="/traceability/viewlabel_registration.php"><i class="fas fa-tags"></i> <span>View Label Registration</span></a>
                </li>

                <li class="<?= ($current_page == '/traceability/spa_process.php') ? 'active' : '' ?>">
                    <a href="/traceability/spa_process.php"><i class="fas fa-spa"></i> <span>SPA</span></a>
                </li>

                <li class="<?= ($current_page == '/traceability/mounter.php') ? 'active' : '' ?>">
                    <a href="/traceability/mounter.php"><i class="fas fa-microchip"></i> <span>Mounter</span></a>
                </li>

                <li class="<?= ($current_page == '/traceability/hourly_output.php') ? 'active' : '' ?>">
                    <a href="/traceability/hourly_output.php"><i class="fas fa-clock"></i> <span>Hourly Output</span></a>
                </li>

                <li class="<?= ($current_page == '/traceability/processed_lot.php') ? 'active' : '' ?>">
                    <a href="/traceability/processed_lot.php"><i class="fas fa-layer-group"></i> <span>Processed Lot</span></a>
                </li>

                <li class="<?= ($current_page == '/traceability/vi_process.php') ? 'active' : '' ?>">
                    <a href="/traceability/vi_process.php"><i class="fas fa-eye"></i> <span>Visual Inspection</span></a>
                </li>

                <?php /* <li class="<?= ($current_page == '/traceability/repair_process.php') ? 'active' : '' ?>">
                <a href="/traceability/repair_process.php"><i class="fas fa-wrench"></i> <span>SMT Repair</span></a>
            </li> */ ?>

                <?php /* <li class="<?= ($current_page == '/traceability/ng_verification.php') ? 'active' : '' ?>">
                <a href="/traceability/ng_verification.php"><i class="fas fa-wrench"></i> <span>NG Verification</span></a>
            </li> */ ?>

                <?php /* <li class="<?= ($current_page == '/traceability/hwrepair_process.php') ? 'active' : '' ?>">
                <a href="/traceability/hwrepair_process.php"><i class="fas fa-wrench"></i> <span>HW Repair</span></a>
            </li> */ ?>

                <li class="dropdown <?= in_array($current_page, ['/traceability/repair_table.php', '/traceability/verify_repairLL.php', '/traceability/batchlot_repair.php']) ? 'active open' : '' ?>">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-window-restore"></i> <span>Repair Process ▾</span></a>
                    <ul class="submenu">
                        <li class="<?= ($current_page == '/traceability/repair_table.php') ? 'active' : '' ?>"><a href="/traceability/repair_table.php">Repair Table</a></li>
                        <li class="<?= ($current_page == '/traceability/verify_repairLL.php') ? 'active' : '' ?>"><a href="/traceability/verify_repairLL.php">Verify Repair Table</a></li>
                        <li class="<?= ($current_page == '/traceability/batchlot_repair.php') ? 'active' : '' ?>"><a href="/traceability/batchlot_repair.php">Batch Lot Repair</a></li>
                        <li class="<?= ($current_page == '/traceability/verify_processleader.php') ? 'active' : '' ?>"><a href="/traceability/verify_processleader.php">Process Leader Verification</a></li>
                    </ul>
            </ul>
            </li>

            <?php /*  <li class="<?= ($current_page == '/traceability/vi_repair.php') ? 'active' : '' ?>">
                <a href="/traceability/vi_repair.php"><i class="fas fa-gavel"></i> <span>SMT VI Repair</span></a>
            </li> */ ?>

            <li class="<?= ($current_page == '/traceability/ai_process.php') ? 'active' : '' ?>">
                <a href="/traceability/ai_process.php"><i class="fas fa-robot"></i> <span>Automatic Insertion</span></a>
            </li>

            <?php /* <li class="<?= ($current_page == '/traceability/ai_repair_process.php') ? 'active' : '' ?>">
                <a href="/traceability/ai_repair_process.php"><i class="fas fa-wrench"></i> <span>AI Repair</span></a>
            </li> */ ?>

            <?php /* <li class="<?= ($current_page == '/traceability/ai_ngverification.php') ? 'active' : '' ?>">
                <a href="/traceability/ai_ngverification.php"><i class="fas fa-wrench"></i> <span>AI NG Verification</span></a>
            </li> */ ?>

            <?php /*  <li class="<?= ($current_page == '/traceability/ai_verify_repairLL.php') ? 'active' : '' ?>">
                <a href="/traceability/ai_verify_repairLL.php"><i class="fas fa-gavel"></i> <span>AI LL Verify Repair</span></a>
            </li> */ ?>

            <?php /*  <li class="<?= ($current_page == '/traceability/ai_repair.php') ? 'active' : '' ?>">
                <a href="/traceability/ai_repair.php"><i class="fas fa-gavel"></i> <span>SMT AI Repair</span></a>
            </li> */ ?>

            <li class="<?= ($current_page == '/traceability/manual_insertion.php') ? 'active' : '' ?>">
                <a href="/traceability/manual_insertion.php"><i class="fas fa-handshake-angle"></i> <span>Manual Insertion</span></a>
            </li>

            <li class="<?= ($current_page == '/traceability/mod1.php') ? 'active' : '' ?>">
                <a href="/traceability/mod1.php"><i class="fas fa-handshake-angle"></i> <span>MOD 1</span></a>
            </li>

            <li class="<?= ($current_page == '/traceability/mod2.php') ? 'active' : '' ?>">
                <a href="/traceability/mod2.php"><i class="fas fa-handshake-angle"></i> <span>MOD 2</span></a>
            </li>

            <li class="<?= ($current_page == '/traceability/fviss.php') ? 'active' : '' ?>">
                <a href="/traceability/fviss.php"><i class="fas fa-map-signs"></i> <span>FVISS</span></a>
            </li>
            <li class="<?= ($current_page == '/traceability/partside.php') ? 'active' : '' ?>">
                <a href="/traceability/partside.php"><i class="fas fa-map-signs"></i> <span>Part Side 1</span></a>
            </li>
            <li class="<?= ($current_page == '/traceability/partside2.php') ? 'active' : '' ?>">
                <a href="/traceability/partside2.php"><i class="fas fa-map-signs"></i> <span>Part Side 2</span></a>
            </li>
            <li class="<?= ($current_page == '/traceability/micro.php') ? 'active' : '' ?>">
                <a href="/traceability/micro.php"><i class="fas fa-map-signs"></i> <span>Micro</span></a>
            </li>
            <li class="<?= ($current_page == '/traceability/wi.php') ? 'active' : '' ?>">
                <a href="/traceability/wi.php"><i class="fas fa-map-signs"></i> <span>WI</span></a>
            </li>

            <li class="dropdown <?= in_array($current_page, ['/traceability/stencil_master.php', '/traceability/squeegee_master.php']) ? 'active open' : '' ?>">
                <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-window-restore"></i> <span>Stencil/Squeegee ▾</span></a>
                <ul class="submenu">
                    <li class="<?= ($current_page == '/traceability/stencil_master.php') ? 'active' : '' ?>"><a href="/traceability/stencil_master.php">Stencil</a></li>
                    <li class="<?= ($current_page == '/traceability/squeegee_master.php') ? 'active' : '' ?>"><a href="/traceability/squeegee_master.php">Squeegee</a></li>
                </ul>
            </li>

            <li class="dropdown <?= in_array($current_page, ['/traceability/ad_solderpaste.php', '/traceability/ad_bonding.php']) ? 'active open' : '' ?>">
                <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-thermometer-0"></i> <span>Adhesive ▾</span></a>
                <ul class="submenu">
                    <li class="<?= ($current_page == '/traceability/ad_solderpaste.php') ? 'active' : '' ?>"><a href="/traceability/ad_solderpaste.php">Solder Paste</a></li>
                    <li class="<?= ($current_page == '/traceability/ad_bonding.php') ? 'active' : '' ?>"><a href="/traceability/ad_bonding.php">Bonding</a></li>
                </ul>
            </li>

            <?php /* <li class="dropdown <?= in_array($current_page, ['/traceability/report_repair.php', '/traceability/report_batchlot.php']) ? 'active open' : '' ?>">
                <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-folder-open"></i> <span>Report ▾</span></a>
                <ul class="submenu">
                    <li class="<?= ($current_page == '/traceability/report_repair.php') ? 'active' : '' ?>"><a href="/traceability/report_repair.php">SMT Repair Report</a></li>
                    <li class="<?= ($current_page == '/traceability/report_batchlot.php') ? 'active' : '' ?>"><a href="/traceability/report_batchlot.php">Batch Lot Report</a></li>
                </ul>
            </li> */ ?>

            <li class="<?= ($current_page == '/traceability/report_batchlot.php') ? 'active' : '' ?>">
                <a href="/traceability/report_batchlot.php"><i class="fas fa-folder-open"></i> <span>History</span></a>
            </li>

            <li class="dropdown <?= in_array($current_page, ['/traceability/fvissBatchlot.php', '/traceability/partside1Batchlot.php', '/traceability/microBatchlot.php', '/traceability/wiBatchlot.php', '/traceability/partside2Batchlot.php']) ? 'active open' : '' ?>">
                <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-folder-open"></i> <span>Batchlot ▾</span></a>
                <ul class="submenu">
                    <li class="<?= ($current_page == '/traceability/fvissBatchlot.php') ? 'active' : '' ?>"><a href="/traceability/fvissBatchlot.php">FVISS</a></li>
                    <li class="<?= ($current_page == '/traceability/partside1Batchlot.php') ? 'active' : '' ?>"><a href="/traceability/partside1Batchlot.php">Part Side 1</a></li>
                    <li class="<?= ($current_page == '/traceability/partside2Batchlot.php') ? 'active' : '' ?>"><a href="/traceability/partside2Batchlot.php">Part Side 2</a></li>
                    <li class="<?= ($current_page == '/traceability/microBatchlot.php') ? 'active' : '' ?>"><a href="/traceability/microBatchlot.php">Micro</a></li>
                    <li class="<?= ($current_page == '/traceability/wiBatchlot.php') ? 'active' : '' ?>"><a href="/traceability/wiBatchlot.php">WI</a></li>
                </ul>
            </li>

            <li class="<?= ($current_page == 'account_settings.php') ? 'active' : '' ?>">
                <a href="account_settings.php"><i class="fas fa-user-cog"></i> <span>Account Settings</span></a>
            </li>
            <hr>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        <?php endif; ?>

        <!-- FOR NON ADMIN USER PROCESS -->
        <?php if ($_SESSION['user_process'] !== 'ADMIN'): ?>
            <ul class="menu">
                <!--REPAIR MENUS-->
                <?php if ($_SESSION['user_process'] === 'REPAIRER'): ?>
                    <li class="<?= ($current_page == '/traceability/repair_table.php') ? 'active' : '' ?>">
                        <a href="/traceability/repair_table.php"><i class="fas fa-window-wrench"></i> <span>Repair Table</span></a>
                    </li>
                    <li class="<?= ($current_page == '/traceability/verify_repairLL.php') ? 'active' : '' ?>">
                        <a href="/traceability/verify_repairLL.php"><i class="fas fa-wrench"></i> <span>Verify Repair Table</span></a>
                    </li>
                    <li class="<?= ($current_page == '/traceability/batchlot_repair.php') ? 'active' : '' ?>">
                        <a href="/traceability/batchlot_repair.php"><i class="fas fa-window-wrench"></i> <span>Batch Lot Repair</span></a>
                    </li>
                <?php endif; ?>

                <!--LABELLER MENUS-->
                <?php if ($_SESSION['user_process'] === 'LABELLER'): ?>
                    <li class="<?= ($current_page == '/traceability/label_registration.php') ? 'active' : '' ?>">
                        <a href="/traceability/label_registration.php"><i class="fas fa-tag"></i> <span>Label Registration</span></a>
                    </li>

                    <li class="<?= ($current_page == '/traceability/viewlabel_registration.php') ? 'active' : '' ?>">
                        <a href="/traceability/viewlabel_registration.php"><i class="fas fa-tags"></i> <span>View Label Registration</span></a>
                    </li>
                <?php endif; ?>

                <!--MODIFICATOR MENUS-->
                <?php if ($_SESSION['user_process'] === 'MODIFICATOR'): ?>
                    <li class="<?= ($current_page == '/traceability/mod1.php') ? 'active' : '' ?>">
                        <a href="/traceability/mod1.php"><i class="fas fa-microchip"></i> <span>Modificator 1</span></a>
                    </li>

                    <li class="<?= ($current_page == '/traceability/mod2.php') ? 'active' : '' ?>">
                        <a href="/traceability/mod2.php"><i class="fas fa-microchip"></i> <span>Modificator 2</span></a>
                    </li>
                <?php endif; ?>

                <!--ENGINEERING MENUS-->
                <?php if ($_SESSION['user_process'] === 'ENGINEERING'): ?>
                    <li class="<?= ($current_page == '/traceability/report_batchlot.php') ? 'active' : '' ?>">
                        <a href="/traceability/report_batchlot.php"><i class="fas fa-microchip"></i> <span>Status Report</span></a>
                    </li>

                    <li class="<?= ($current_page == '/traceability/viewlabel_registration.php') ? 'active' : '' ?>">
                        <a href="/traceability/viewlabel_registration.php"><i class="fas fa-tags"></i> <span>View Label Registration</span></a>
                    </li>
                <?php endif; ?>

                <li class="<?= ($current_page == 'account_settings.php') ? 'active' : '' ?>">
                    <a href="account_settings.php"><i class="fas fa-user-cog"></i> <span>Account Settings</span></a>
                </li>
                <hr>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        <?php endif; ?>

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