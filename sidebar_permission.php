<?php
session_start();

if (!isset($_SESSION['user_namefl'])) {
    header('Location: login.php');
    exit;
}

$user_process = $_SESSION['user_process'] ?? '';
$is_admin = $user_process === 'ADMIN';

$menuPermissions = [
    'index.php' => ['*'],

    'add_model.php' => ['ADMIN'],
    'view_model.php' => ['ADMIN'],

    'label_registration.php' => ['LABELLER', 'ADMIN'],
    'viewlabel_registration.php' => ['LABELLER', 'ADMIN'],

    'spa_process.php' => ['SPA', 'ADMIN'],
    'mounter.php' => ['MOUNTER', 'ADMIN'],
    'hourly_output.php' => ['MOUNTER', 'ADMIN'],
    'processed_lot.php' => ['MOUNTER', 'ADMIN'],
    'vi_process.php' => ['VISUAL INSPECTION', 'ADMIN'],
    'repair_process.php' => ['REPAIRER', 'ADMIN'],
    'verify_repairLL.php' => ['REPAIRER', 'ADMIN'],
    'vi_repair.php' => ['VISUAL INSPECTION', 'ADMIN'],
    'ai_process.php' => ['AI', 'ADMIN'],

    'stencil_master.php' => ['ADMIN'],
    'squeegee_master.php' => ['ADMIN'],

    'ad_solderpaste.php' => ['ADMIN'],
    'ad_bonding.php' => ['ADMIN'],

    'account_settings.php' => ['*'],
    'logout.php' => ['*'],
];

function canAccess($page)
{
    global $menuPermissions, $user_process, $is_admin;

    if (!isset($menuPermissions[$page])) {
        return false;
    }

    $allowedProcesses = $menuPermissions[$page];

    return $is_admin || in_array('*', $allowedProcesses) || in_array($user_process, $allowedProcesses);
}
?>
<?php include 'sidebar_permission.php'; ?>
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
    <div class="sidebar">
        <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
        <br>
        <ul class="menu">
            <?php if (canAccess('index.php')): ?>
                <li><a href="index.php"><i class="fas fa-home"></i> <span>Home</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('add_model.php') || canAccess('view_model.php')): ?>
                <li class="dropdown">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-cube"></i> <span>Model Master ▾</span></a>
                    <ul class="submenu">
                        <?php if (canAccess('add_model.php')): ?><li><a href="add_model.php">Add Model</a></li><?php endif; ?>
                        <?php if (canAccess('view_model.php')): ?><li><a href="view_model.php">View Model</a></li><?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (canAccess('label_registration.php')): ?>
                <li><a href="label_registration.php"><i class="fas fa-tag"></i> <span>Label Registration</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('viewlabel_registration.php')): ?>
                <li><a href="viewlabel_registration.php"><i class="fas fa-tags"></i> <span>View Label Registration</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('spa_process.php')): ?>
                <li><a href="spa_process.php"><i class="fas fa-spa"></i> <span>SPA</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('mounter.php')): ?>
                <li><a href="mounter.php"><i class="fas fa-microchip"></i> <span>Mounter</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('hourly_output.php')): ?>
                <li><a href="hourly_output.php"><i class="fas fa-microchip"></i> <span>Hourly Output</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('processed_lot.php')): ?>
                <li><a href="processed_lot.php"><i class="fas fa-microchip"></i> <span>Processed Lot</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('vi_process.php')): ?>
                <li><a href="vi_process.php"><i class="fas fa-eye"></i> <span>Visual Inspection</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('repair_process.php')): ?>
                <li><a href="repair_process.php"><i class="fas fa-wrench"></i> <span>SMT Repair</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('verify_repairLL.php')): ?>
                <li><a href="verify_repairLL.php"><i class="fas fa-gavel"></i> <span>LL Verify Repair</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('vi_repair.php')): ?>
                <li><a href="vi_repair.php"><i class="fas fa-gavel"></i> <span>SMT VI Repair</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('ai_process.php')): ?>
                <li><a href="ai_process.php"><i class="fas fa-robot"></i> <span>AI Process</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('stencil_master.php') || canAccess('squeegee_master.php')): ?>
                <li class="dropdown">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-window-restore"></i> <span>Stencil/Squeegee ▾</span></a>
                    <ul class="submenu">
                        <?php if (canAccess('stencil_master.php')): ?><li><a href="stencil_master.php">Stencil</a></li><?php endif; ?>
                        <?php if (canAccess('squeegee_master.php')): ?><li><a href="squeegee_master.php">Squeegee</a></li><?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (canAccess('ad_solderpaste.php') || canAccess('ad_bonding.php')): ?>
                <li class="dropdown">
                    <a href="#" onclick="toggleDropdown(event)"><i class="fas fa-thermometer-0"></i> <span>Adhesive ▾</span></a>
                    <ul class="submenu">
                        <?php if (canAccess('ad_solderpaste.php')): ?><li><a href="ad_solderpaste.php">Solder Paste</a></li><?php endif; ?>
                        <?php if (canAccess('ad_bonding.php')): ?><li><a href="ad_bonding.php">Bonding</a></li><?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if (canAccess('account_settings.php')): ?>
                <li><a href="account_settings.php"><i class="fas fa-user-cog"></i> <span>Account Settings</span></a></li>
            <?php endif; ?>

            <?php if (canAccess('logout.php')): ?>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            <?php endif; ?>
        </ul>
    </div>

    <script>
        function toggleSidebar() {
            let sidebar = document.querySelector(".sidebar");
            sidebar.classList.toggle("expanded");
        }

        function toggleDropdown(event) {
            event.preventDefault();
            let parent = event.target.closest('.dropdown');
            parent.classList.toggle("active");
        }
    </script>
</body>

</html>