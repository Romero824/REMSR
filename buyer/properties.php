<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header('Location: ../login.php');
    exit();
}

// Get buyer information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND role = 'buyer'");
$stmt->execute([$_SESSION['user_id']]);
$buyer = $stmt->fetch();

// If buyer not found, redirect to login
if (!$buyer) {
    session_destroy();
    header('Location: ../login.php');
    exit();
}

// Handle search and filters
$location = isset($_GET['location']) ? $_GET['location'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

// Build query
$query = "SELECT * FROM properties WHERE status = 'for_sale'";
$params = [];

if (!empty($location)) {
    $query .= " AND location LIKE ?";
    $params[] = "%$location%";
}

if (!empty($type)) {
    $query .= " AND type = ?";
    $params[] = $type;
}

if (!empty($min_price)) {
    $query .= " AND price >= ?";
    $params[] = $min_price;
}

if (!empty($max_price)) {
    $query .= " AND price <= ?";
    $params[] = $max_price;
}

$query .= " ORDER BY created_at DESC";

// Get properties
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$properties = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties - REMSR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
        }
        .sidebar-sticky {
            position: relative;
            top: 0;
            height: calc(100vh - 48px);
            padding-top: .5rem;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .property-card {
            transition: transform 0.3s;
        }
        .property-card:hover {
            transform: translateY(-5px);
        }
        .property-image {
            height: 200px;
            object-fit: cover;
            border-radius: 10px 10px 0 0;
        }
        .navbar {
            background-color: #343a40;
            padding: 0.5rem 1rem;
        }
        .navbar-brand {
            color: white;
            font-weight: bold;
        }
        .nav-link {
            color: rgba(255,255,255,.75);
        }
        .nav-link:hover {
            color: white;
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">REMSR Buyer</a>
            <div class="d-flex align-items-center">
                <span class="text-white me-3">Welcome, <?php echo htmlspecialchars($buyer['name']); ?></span>
                <a href="../logout.php" class="btn btn-outline-light">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="dashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active text-white" href="properties.php">
                                <i class="fas fa-home me-2"></i> Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="favorites.php">
                                <i class="fas fa-heart me-2"></i> Favorites
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="inquiries.php">
                                <i class="fas fa-comments me-2"></i> Messages
                                <?php
                                // Count unread messages
                                $stmt = $pdo->prepare("
                                    SELECT COUNT(*) as count 
                                    FROM inquiries i 
                                    WHERE i.user_id = ? AND i.status = 'replied'
                                ");
                                $stmt->execute([$_SESSION['user_id']]);
                                $unread = $stmt->fetch();
                                if ($unread['count'] > 0): ?>
                                    <span class="badge bg-danger rounded-pill ms-2"><?php echo $unread['count']; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="profile.php">
                                <i class="fas fa-user me-2"></i> Profile
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Properties</h1>
                </div>

                <!-- Search Section -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Search Properties</h5>
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
                            </div>
                            <div class="col-md-2">
                                <select class="form-select" name="type">
                                    <option value="">Property Type</option>
                                    <option value="house" <?php echo $type === 'house' ? 'selected' : ''; ?>>House</option>
                                    <option value="apartment" <?php echo $type === 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                                    <option value="condo" <?php echo $type === 'condo' ? 'selected' : ''; ?>>Condo</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="min_price" placeholder="Min Price" value="<?php echo htmlspecialchars($min_price); ?>">
                            </div>
                            <div class="col-md-2">
                                <input type="number" class="form-control" name="max_price" placeholder="Max Price" value="<?php echo htmlspecialchars($max_price); ?>">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Properties Grid -->
                <div class="row">
                    <?php foreach ($properties as $property): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card property-card h-100">
                            <?php if (!empty($property['image_path'])): ?>
                                <img src="../<?php echo htmlspecialchars($property['image_path']); ?>" class="card-img-top property-image" alt="Property Image">
                            <?php else: ?>
                                <img src="../assets/images/property-placeholder.svg" class="card-img-top property-image" alt="Property Image">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($property['title']); ?></h5>
                                <p class="card-text text-muted">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['location']); ?>
                                </p>
                                <p class="card-text">
                                    <strong>₱<?php echo number_format($property['price']); ?></strong>
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-primary"><?php echo ucfirst($property['type']); ?></span>
                                    <a href="property-details.php?id=<?php echo $property['id']; ?>" class="btn btn-primary">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>