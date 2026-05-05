<?php

// upon visit to the system's domain, the user is redirected to the login page if user_process session variable is not set yet, upon login
// the switch case within this file will fire, and redirect the user to the appropriate page depending on their process

// Every process files in this project will have sidebar.php included

session_start();

if (!isset($_SESSION['user_process'])) {
    header('Location: login.php');
    exit;
}

switch ($_SESSION['user_process']) {
    case 'LABELLER':
        header('Location: label_registration.php');
        exit;
        break;
    case 'SPA':
        header('Location: spa_process.php');
        exit;
        break;
    case 'MOUNTER':
        header('Location: mounter.php');
        exit;
        break;
    case 'VISUAL INSPECTION':
        header('Location: vi_process.php');
        exit;
        break;
    case 'AUTOMATIC INSERTION':
        header('Location: ai_process.php');
        exit;
        break;
    case 'MANUAL INSERTION':
        header('Location: manual_insertion.php');
        exit;
        break;
    case 'MODIFICATOR 1':
        header('Location: mod1.php');
        exit;
        break;
    case 'MODIFICATOR 2':
        header('Location: mod2.php');
        exit;
        break;
    case 'FVI SOLDERSIDE':
        header('Location: fviss.php');
        exit;
        break;
    case 'PARTSIDE 1':
        header('Location: partside.php');
        exit;
        break;
    case 'PARTSIDE 2':
        header('Location: partside2.php');
        exit;
        break;
    case 'MICROSCOPE INSPECTION':
        header('Location: micro.php');
        exit;
        break;
    case 'WITHSTAND INSULATION TEST':
        header('Location: wi.php');
        exit;
        break;
    case 'REPAIRER':
        header('Location: repair_table.php');
        exit;
        break;
    case 'ENGINEERING':
        header('Location: report_batchlot.php');
        exit;
        break;
    case 'ADMIN':
        header('Location: useraccounts.php');
        exit;
        break;
    case 'LL VERIFICATION':
        header('Location: ll_verification.php');
        exit;
        break;
    default:
        header('Location: login.php');
        exit;
        break;
}
