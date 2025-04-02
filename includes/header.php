<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: ../login.php");
    exit;
}

// Get the current directory name
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$is_root = ($current_dir == 'REMSR' || $current_dir == '');
$path_prefix = $is_root ? '' : '../';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REMSR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #fff;
            padding: 10px 20px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
        }
        .main-content {
            padding: 20px;
        }
        .navbar-brand {
            color: #fff !important;
            font-weight: bold;
        }
        .user-info {
            color: #fff;
            padding: 10px 20px;
            border-bottom: 1px solid #495057;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="user-info">
                    <h5><?php echo htmlspecialchars($_SESSION["username"]); ?></h5>
                    <small><?php echo ucfirst(htmlspecialchars($_SESSION["user_type"])); ?></small>
                </div>
                <ul class="nav flex-column">
                    <?php if($_SESSION["user_type"] == "owner"): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>owner/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>owner/properties.php">
                                <i class="bi bi-house"></i> My Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>owner/tenants.php">
                                <i class="bi bi-people"></i> Tenants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>owner/payments.php">
                                <i class="bi bi-cash"></i> Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>owner/maintenance.php">
                                <i class="bi bi-tools"></i> Maintenance
                            </a>
                        </li>
                    <?php elseif($_SESSION["user_type"] == "agent"): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>agent/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>agent/properties.php">
                                <i class="bi bi-house"></i> Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>agent/clients.php">
                                <i class="bi bi-people"></i> Clients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>agent/visits.php">
                                <i class="bi bi-calendar-check"></i> Property Visits
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>agent/commissions.php">
                                <i class="bi bi-cash"></i> Commissions
                            </a>
                        </li>
                    <?php elseif($_SESSION["user_type"] == "tenant"): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>tenant/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>tenant/lease.php">
                                <i class="bi bi-file-text"></i> Lease Agreement
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>tenant/payments.php">
                                <i class="bi bi-cash"></i> Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>tenant/maintenance.php">
                                <i class="bi bi-tools"></i> Maintenance Requests
                            </a>
                        </li>
                    <?php elseif($_SESSION["user_type"] == "manager"): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>manager/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>manager/properties.php">
                                <i class="bi bi-house"></i> Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>manager/tenants.php">
                                <i class="bi bi-people"></i> Tenants
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>manager/maintenance.php">
                                <i class="bi bi-tools"></i> Maintenance
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>manager/reports.php">
                                <i class="bi bi-file-earmark-text"></i> Reports
                            </a>
                        </li>
                    <?php elseif($_SESSION["user_type"] == "buyer"): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>buyer/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>buyer/properties.php">
                                <i class="bi bi-house"></i> Available Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>buyer/visits.php">
                                <i class="bi bi-calendar-check"></i> Property Visits
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>buyer/offers.php">
                                <i class="bi bi-tag"></i> My Offers
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>buyer/portfolio.php">
                                <i class="bi bi-briefcase"></i> Portfolio
                            </a>
                        </li>
                    <?php elseif($_SESSION["user_type"] == "admin"): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>admin/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>admin/users.php">
                                <i class="bi bi-people"></i> Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>admin/properties.php">
                                <i class="bi bi-house"></i> Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>admin/reports.php">
                                <i class="bi bi-file-earmark-text"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $path_prefix; ?>admin/settings.php">
                                <i class="bi bi-gear"></i> Settings
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item mt-3">
                        <a class="nav-link" href="<?php echo $path_prefix; ?>logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="container-fluid"> 