<?php
session_start();

if (!isset($_SESSION['user_process'])) {
    header('Location: /traceabilitydev/login.php');
    exit;
}

switch ($_SESSION['user_process']) {
    case 'LABELLER':
        header('Location: /traceabilitydev/label_registration.php');
        exit;
    case 'SPA':
        header('Location: /traceabilitydev/spa_process.php');
        exit;
    case 'MOUNTER':
        header('Location: /traceabilitydev/mounter.php');
        exit;
    case 'VISUAL INSPECTION':
        header('Location: /traceabilitydev/vi_process.php');
        exit;
    case 'AUTOMATIC INSERTION':
        header('Location: /traceabilitydev/ai_process.php');
        exit;
    case 'MANUAL INSERTION':
        header('Location: /traceabilitydev/manual_insertion.php');
        exit;
    case 'MODIFICATOR 1':
        header('Location: /traceabilitydev/mod1.php');
        exit;
    case 'MODIFICATOR 2':
        header('Location: /traceabilitydev/mod2.php');
        exit;
    case 'FVI SOLDERSIDE':
        header('Location: /traceabilitydev/fviss.php');
        exit;
    case 'PARTSIDE 1':
        header('Location: /traceabilitydev/partside.php');
        exit;
    case 'PARTSIDE 2':
        header('Location: /traceabilitydev/partside2.php');
        exit;
    case 'MICROSCOPE INSPECTION':
        header('Location: /traceabilitydev/micro.php');
        exit;
    case 'WITHSTAND INSULATION TEST':
        header('Location: /traceabilitydev/wi.php');
        exit;
    case 'REPAIRER':
        header('Location: /traceabilitydev/repair_table.php');
        exit;
    case 'ENGINEERING':
        header('Location: /traceabilitydev/report_batchlot.php');
        exit;
    case 'ADMIN':
        header('Location: /traceabilitydev/useraccounts.php');
        exit;
    case 'LL VERIFICATION':
        header('Location: /traceabilitydev/ll_verification.php');
        exit;
    case 'QA':
        header('Location: /traceabilitydev/qa.php');
        exit;    
    default:
        header('Location: /traceabilitydev/login.php');
        exit;
}