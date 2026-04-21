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
    <link rel="icon" type="image/jpg" href="/traceabilitydev/img/icon.jpg">
    <title>Production Traceability</title>
    <link rel="stylesheet" href="/traceability/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
</head>

<body>
    <div class="sidebar" id="sidebar">
        <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
        <br>
        <br>
        <?php if ($_SESSION['user_process'] === 'ADMIN') { ?>
            <ul class="menu">
                <li class="dropdown <?php echo in_array($current_page, ['/traceabilitydev/register.php', '/traceabilitydev/useraccounts.php']) ? 'active open' : ''; ?>">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-user-cog"></i> <span>Admin Panel ▾</span></a>
                    <ul class="submenu">
                        <li class="<?php echo ($current_page == '/traceabilitydev/register.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/register.php">Register User</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/useraccounts.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/useraccounts.php">Users</a></li>
                    </ul>
                </li>

                <li class="dropdown <?php echo in_array($current_page, ['/traceabilitydev/add_model.php', '/traceabilitydev/view_model.php']) ? 'active open' : ''; ?>">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-cube"></i> <span>Model Master ▾</span></a>
                    <ul class="submenu">
                        <li class="<?php echo ($current_page == '/traceabilitydev/add_model.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/add_model.php">Add Model</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/view_model.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/view_model.php">View Model</a></li>
                    </ul>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/label_registration.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/label_registration.php"><i class="fas fa-tag"></i> <span>Label Registration</span></a>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/viewlabel_registration.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/viewlabel_registration.php"><i class="fas fa-tags"></i> <span>View Label Registration</span></a>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/spa_process.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/spa_process.php"><i class="fas fa-spa"></i> <span>SPA</span></a>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/mounter.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/mounter.php"><i class="fas fa-microchip"></i> <span>Mounter</span></a>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/hourly_output.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/hourly_output.php"><i class="fas fa-clock"></i> <span>Hourly Output</span></a>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/processed_lot.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/processed_lot.php"><i class="fas fa-layer-group"></i> <span>Processed Lot</span></a>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/vi_process.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/vi_process.php"><i class="fas fa-eye"></i> <span>Visual Inspection</span></a>
                </li>

                <?php /* <li class="<?= ($current_page == '/traceabilitydev/repair_process.php') ? 'active' : '' ?>">
                <a href="/traceabilitydev/repair_process.php"><i class="fas fa-wrench"></i> <span>SMT Repair</span></a>
            </li> */ ?>

                <?php /* <li class="<?= ($current_page == '/traceabilitydev/ng_verification.php') ? 'active' : '' ?>">
                <a href="/traceabilitydev/ng_verification.php"><i class="fas fa-wrench"></i> <span>NG Verification</span></a>
            </li> */ ?>

                <?php /* <li class="<?= ($current_page == '/traceabilitydev/hwrepair_process.php') ? 'active' : '' ?>">
                <a href="/traceabilitydev/hwrepair_process.php"><i class="fas fa-wrench"></i> <span>HW Repair</span></a>
            </li> */ ?>

                <li class="dropdown <?php echo in_array($current_page, ['/traceabilitydev/repair_table.php', '/traceabilitydev/verify_repairLL.php', '/traceabilitydev/batchlot_repair.php', '/traceabilitydev/verify_processleader.php']) ? 'active open' : ''; ?>">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-window-restore"></i> <span>Repair Process ▾</span></a>
                    <ul class="submenu">
                        <li class="<?php echo ($current_page == '/traceabilitydev/repair_table.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/repair_table.php">Repair Table</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/verify_repairLL.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/verify_repairLL.php">Line Leader</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/batchlot_repair.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/batchlot_repair.php">Batch Lot Verification</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/verify_processleader.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/verify_processleader.php">Process Verification</a></li>
                    </ul>
                </li>

                <li class="dropdown <?php echo in_array($current_page, ['/traceabilitydev/BoardAnalysis.php', '/traceabilitydev/repair_boardanalysis.php', '/traceabilitydev/ll_boardanalysis.php', '/traceabilitydev/mod_boardanalysis.php']) ? 'active open' : ''; ?>">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-window-restore"></i> <span>ICT/WI/FT Repair ▾</span></a>
                    <ul class="submenu">
                        <li class="<?php echo ($current_page == '/traceabilitydev/BoardAnalysis.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/BoardAnalysis.php">Board Analysis</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/repair_boardanalysis.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/repair_boardanalysis.php">Repair Table</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/ll_boardanalysis.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/ll_boardanalysis.php">Line Leader Analysis Table</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/mod_boardanalysis.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/mod_boardanalysis.php">Modificator Analysis Table</a></li>
                    </ul>
                </li>

                <?php /*  <li class="<?= ($current_page == '/traceabilitydev/vi_repair.php') ? 'active' : '' ?>">
                <a href="/traceabilitydev/vi_repair.php"><i class="fas fa-gavel"></i> <span>SMT VI Repair</span></a>
            </li> */ ?>

                <li class="<?php echo ($current_page == '/traceabilitydev/ai_process.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/ai_process.php"><i class="fas fa-robot"></i> <span>Automatic Insertion</span></a>
                </li>

                <?php /* <li class="<?= ($current_page == '/traceabilitydev/ai_repair_process.php') ? 'active' : '' ?>">
                <a href="/traceabilitydev/ai_repair_process.php"><i class="fas fa-wrench"></i> <span>AI Repair</span></a>
            </li> */ ?>

                <?php /* <li class="<?= ($current_page == '/traceabilitydev/ai_ngverification.php') ? 'active' : '' ?>">
                <a href="/traceabilitydev/ai_ngverification.php"><i class="fas fa-wrench"></i> <span>AI NG Verification</span></a>
            </li> */ ?>

                <?php /*  <li class="<?= ($current_page == '/traceabilitydev/ai_verify_repairLL.php') ? 'active' : '' ?>">
                <a href="/traceabilitydev/ai_verify_repairLL.php"><i class="fas fa-gavel"></i> <span>AI LL Verify Repair</span></a>
            </li> */ ?>

                <?php /*  <li class="<?= ($current_page == '/traceabilitydev/ai_repair.php') ? 'active' : '' ?>">
                <a href="/traceabilitydev/ai_repair.php"><i class="fas fa-gavel"></i> <span>SMT AI Repair</span></a>
            </li> */ ?>

                <li class="<?php echo ($current_page == '/traceabilitydev/manual_insertion.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/manual_insertion.php"><i class="fas fa-handshake-angle"></i> <span>Manual Insertion</span></a>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/mod1.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/mod1.php"><i class="fas fa-handshake-angle"></i> <span>MOD 1</span></a>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/mod2.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/mod2.php"><i class="fas fa-handshake-angle"></i> <span>MOD 2</span></a>
                </li>

                <li class="<?php echo ($current_page == '/traceabilitydev/fviss.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/fviss.php"><i class="fas fa-map-signs"></i> <span>FVISS</span></a>
                </li>
                <li class="<?php echo ($current_page == '/traceabilitydev/partside.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/partside.php"><i class="fas fa-map-signs"></i> <span>Part Side 1</span></a>
                </li>
                <li class="<?php echo ($current_page == '/traceabilitydev/partside2.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/partside2.php"><i class="fas fa-map-signs"></i> <span>Part Side 2</span></a>
                </li>
                <li class="<?php echo ($current_page == '/traceabilitydev/micro.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/micro.php"><i class="fas fa-map-signs"></i> <span>Micro</span></a>
                </li>
                <li class="<?php echo ($current_page == '/traceabilitydev/wi.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/wi.php"><i class="fas fa-map-signs"></i> <span>WI</span></a>
                </li>

                <li class="dropdown <?php echo in_array($current_page, ['/traceabilitydev/stencil_master.php', '/traceabilitydev/squeegee_master.php']) ? 'active open' : ''; ?>">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-window-restore"></i> <span>Stencil/Squeegee ▾</span></a>
                    <ul class="submenu">
                        <li class="<?php echo ($current_page == '/traceabilitydev/stencil_master.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/stencil_master.php">Stencil</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/squeegee_master.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/squeegee_master.php">Squeegee</a></li>
                    </ul>
                </li>

                <li class="dropdown <?php echo in_array($current_page, ['/traceabilitydev/add_solderpaste.php', '/traceabilitydev/ad_bonding.php']) ? 'active open' : ''; ?>">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-thermometer-0"></i> <span>Adhesive ▾</span></a>
                    <ul class="submenu">
                        <li class="<?php echo ($current_page == '/traceabilitydev/add_solderpaste.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/ad_solderpaste.php">Solder Paste</a></li>
                        <li class="<?php echo ($current_page == '/traceabilitydev/ad_bonding.php') ? 'active' : ''; ?>"><a href="/traceabilitydev/ad_bonding.php">Bonding</a></li>
                    </ul>
                </li>

                <?php /* <li class="dropdown <?= in_array($current_page, ['/traceabilitydev/report_repair.php', '/traceabilitydev/report_batchlot.php']) ? 'active open' : '' ?>">
                <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-folder-open"></i> <span>Report ▾</span></a>
                <ul class="submenu">
                    <li class="<?= ($current_page == '/traceabilitydev/report_repair.php') ? 'active' : '' ?>"><a href="/traceabilitydev/report_repair.php">SMT Repair Report</a></li>
                    <li class="<?= ($current_page == '/traceabilitydev/report_batchlot.php') ? 'active' : '' ?>"><a href="/traceabilitydev/report_batchlot.php">Batch Lot Report</a></li>
                </ul>
            </li> */ ?>

                <li class="<?php echo ($current_page == '/traceabilitydev/report_batchlot.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/report_batchlot.php"><i class="fas fa-folder-open"></i> <span>History</span></a>
                </li>

                 <li class="<?php echo ($current_page == '/traceabilitydev/scrap_history.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/scrap_history.php"><i class="fas fa-folder-open"></i> <span>Scrapped Boards</span></a>
                </li>

                 <li class="<?php echo ($current_page == '/traceabilitydev/fvissBatchlot.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/fvissBatchlot.php"><i class="fas fa-map-signs"></i> <span>FVISS Batchlot</span></a>
                </li>
                <li class="<?php echo ($current_page == '/traceabilitydev/partside1Batchlot.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/partside1Batchlot.php"><i class="fas fa-map-signs"></i> <span>Part Side 1 Batchlot</span></a>
                </li>
                <li class="<?php echo ($current_page == '/traceabilitydev/partside2Batchlot.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/partside2Batchlot.php"><i class="fas fa-map-signs"></i> <span>Part Side 2 Batchlot</span></a>
                </li>
                <li class="<?php echo ($current_page == '/traceabilitydev/microBatchlot.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/microBatchlot.php"><i class="fas fa-map-signs"></i> <span>Micro Batchlot</span></a>
                </li>
                <li class="<?php echo ($current_page == '/traceabilitydev/wiBatchlot.php') ? 'active' : ''; ?>">
                    <a href="/traceabilitydev/wiBatchlot.php"><i class="fas fa-map-signs"></i> <span>WI Batchlot</span></a>
                </li>

                <li class="<?php echo ($current_page == 'account_settings.php') ? 'active' : ''; ?>">
                    <a href="account_settings.php"><i class="fas fa-user-cog"></i> <span>Account Settings</span></a>
                </li>
                <hr>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        <?php } ?>

        <!-- FOR NON ADMIN USER PROCESS -->
        <?php if ($_SESSION['user_process'] !== 'ADMIN') { ?>
            <ul class="menu">
                <!--REPAIR MENUS-->
                <?php if ($_SESSION['user_process'] === 'REPAIRER') { ?>
                    <li class="<?php echo ($current_page == '/traceabilitydev/repair_table.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/repair_table.php"><i class="fas fa-wrench"></i> <span>Repair Table</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/verify_repairLL.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/verify_repairLL.php"><i class="fas fa-wrench"></i> <span>Line Leader</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/batchlot_repair.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/batchlot_repair.php"><i class="fas fa-wrench"></i> <span>Batch Lot Verify</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/verify_processleader.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/verify_processleader.php"><i class="fas fa-wrench"></i> <span>Process Lead Verify</span></a>
                    </li>
                <?php } ?>

                <!--LABELLER MENUS-->
                <?php if ($_SESSION['user_process'] === 'LABELLER') { ?>
                    <li class="<?php echo ($current_page == '/traceabilitydev/label_registration.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/label_registration.php"><i class="fas fa-tag"></i> <span>Label Registration</span></a>
                    </li>

                    <li class="<?php echo ($current_page == '/traceabilitydev/viewlabel_registration.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/viewlabel_registration.php"><i class="fas fa-tags"></i> <span>View Label Registration</span></a>
                    </li>
                <?php } ?>

                <!--ENGINEERING MENUS-->
                <?php if ($_SESSION['user_process'] === 'ENGINEERING') { ?>
                    <li class="<?php echo ($current_page == '/traceabilitydev/report_batchlot.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/report_batchlot.php"><i class="fas fa-microchip"></i> <span>Status Report</span></a>
                    </li>

                    <li class="<?php echo ($current_page == '/traceabilitydev/viewlabel_registration.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/viewlabel_registration.php"><i class="fas fa-tags"></i> <span>View Label Registration</span></a>
                    </li>
                <?php } ?>

                <!-- VI PROCESS MENUS -->
                 <?php if ($_SESSION['user_process'] === 'VISUAL INSPECTION') { ?>
                    <li class="<?php echo ($current_page == '/traceabilitydev/vi_process.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/vi_process.php"><i class="fas fa-window-wrench"></i> <span>Visual Inspection</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/mounter.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/mounter.php"><i class="fas fa-wrench"></i> <span>Mounter</span></a>
                    </li>
                <?php } ?>
                    
                <!--FVISS MENUS -->
                <?php if ($_SESSION['user_process'] === 'FVI SOLDERSIDE') { ?>
                    <li class="<?php echo ($current_page == '/traceabilitydev/fviss.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/fviss.php"><i class="fas fa-window-wrench"></i> <span>FVI Solderside</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/fvissBatchlot.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/fvissBatchlot.php"><i class="fas fa-wrench"></i> <span>FVISS Batchlot</span></a>
                    </li>
                <?php } ?>

                <!--PARTSIDE MENUS -->
                <?php if ($_SESSION['user_process'] === 'PARTSIDE 1' || $_SESSION['user_process'] === 'PARTSIDE 2') { ?>
                    <li class="<?php echo ($current_page == '/traceabilitydev/partside.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/partside.php"><i class="fas fa-window-wrench"></i> <span>PARTSIDE 1</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/partside2.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/partside2.php"><i class="fas fa-window-wrench"></i> <span>PARTSIDE 2</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/partside1Batchlot.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/partside1Batchlot.php"><i class="fas fa-window-wrench"></i> <span>PARTSIDE 1 BATCHLOT</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/partside2Batchlot.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/partside2Batchlot.php"><i class="fas fa-window-wrench"></i> <span>PARTSIDE 2 BATCHLOT</span></a>
                    </li>
                <?php } ?>

                 <!--MICRO MENUS -->
                <?php if ($_SESSION['user_process'] === 'MICROSCOPE INSPECTION') { ?>
                    <li class="<?php echo ($current_page == '/traceabilitydev/micro.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/micro.php"><i class="fas fa-window-wrench"></i> <span>Micro</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/microBatchlot.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/microBatchlot.php"><i class="fas fa-wrench"></i> <span>Micro Batchlot</span></a>
                    </li>
                <?php } ?>

                 <!--FVISS MENUS -->
                <?php if ($_SESSION['user_process'] === 'WITHSTAND INSULATION TEST') { ?>
                    <li class="<?php echo ($current_page == '/traceabilitydev/wi.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/wi.php"><i class="fas fa-window-wrench"></i> <span>WI</span></a>
                    </li>
                    <li class="<?php echo ($current_page == '/traceabilitydev/wiBatchlot.php') ? 'active' : ''; ?>">
                        <a href="/traceabilitydev/wiBatchlot.php"><i class="fas fa-wrench"></i> <span>WI Batchlot</span></a>
                    </li>
                <?php } ?>

                <li class="<?php echo ($current_page == 'account_settings.php') ? 'active' : ''; ?>">
                    <a href="account_settings.php"><i class="fas fa-user-cog"></i> <span>Account Settings</span></a>
                </li>
                <hr>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        <?php } ?>

    </div>

    <script>
    const UserName = "<?php echo htmlspecialchars($_SESSION['user_namefl']) ?? ''; ?>";
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