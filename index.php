<?php
session_start();

// If user is already logged in, redirect to their dashboard
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    switch($_SESSION["user_type"]) {
        case 'owner':
            header("location: owner/dashboard.php");
            break;
        case 'agent':
            header("location: agent/dashboard.php");
            break;
        case 'tenant':
            header("location: tenant/dashboard.php");
            break;
        case 'manager':
            header("location: manager/dashboard.php");
            break;
        case 'buyer':
            header("location: buyer/dashboard.php");
            break;
        case 'admin':
            header("location: admin/dashboard.php");
            break;
        default:
            header("location: login.php");
    }
    exit;
} else {
    // If not logged in, redirect to login page
    header("location: login.php");
    exit;
}
?> 