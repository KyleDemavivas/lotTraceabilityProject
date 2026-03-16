<?php

// upon visit to the system's domain, the user is redirected to the login page if user_process session variable is not set yet, upon login
// the switch case within this file will fire, and redirect the user to the appropriate page depending on their process

// Every process files in this project will have sidebar.php included

session_start();
switch ($_SESSION['user_process']) {
    case 'LABELLER':
        header('Location: label_registration.php');
        break;
    case 'SPA':
        header('Location: spa_process.php');
        break;
    case 'MOUNTER':
        header('Location: mounter.php');
        break;
    case 'VISUAL INSPECTION':
        header('Location: vi_process.php');
        break;
    case 'AUTOMATIC INSERTION':
        header('Location: ai_process.php');
        break;
    case 'MANUAL INSERTION':
        header('Location: manual_insertion.php');
        break;
    case 'MODIFICATOR 1':
        header('Location: mod1.php');
        break;
    case 'MODIFICATOR 2':
        header('Location: mod2.php');
        break;
    case 'FVI SOLDERSIDE':
        header('Location: fviss.php');
        break;
    case 'PARTSIDE 1':
        header('Location: partside.php');
        break;
    case 'PARTSIDE 2':
        header('Location: partside2.php');
        break;
    case 'MICROSCOPE INSPECTION':
        header('Location: micro.php');
        break;
    case 'WITHSTAND INSULATION TEST':
        header('Location: wi.php');
        break;
    case 'REPAIRER':
        header('Location: repair_table.php');
        break;
    case 'ENGINEERING':
        header('Location: report_batchlot.php');
        break;
    case 'ADMIN':
        header('Location: useraccounts.php');
        break;
    case 'LL VERIFICATION':
        header('Location: ll_verification.php');
        break;
    default:
        header('Location: login.php');
        break;
}
