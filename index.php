<?php
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

        case 'MODIFICATOR':
        header('Location: mod1.php');
        break;

        case 'FVISS':
        header('Location: fviss.php');
        break;

        case 'PARTSIDE 1':
        header('Location: partside.php');
        break;

    case 'PARTSIDE 2':
        header('Location: partside2.php');
        break;

    case 'MICROSCOPE INSPECTION':
        header('Location: microscope_process.php');
        break;

    case 'WITHSTAND INSULATION TEST':
        header('Location: wi_process.php');   
        break; 
            
    case 'REPAIRER':
        header('Location: repair_table.php');
        break;

    case 'AUTOMATIC INSERTION':
        header('Location: ai_process.php');
        break;
        
    case 'ENGINEERING':    
        header('Location: report_batchlot.php');
        break;

    case 'LABELLER':
        header('Location: label_registration.php');    
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
?>