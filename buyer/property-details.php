<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'buyer') {
    header('Location: ../login.php');
    exit();
}

// Get property ID from URL
$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($property_id <= 0) {
    header('Location: properties.php');
    exit();
}

// Get property details
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch();

if (!$property) {
    header('Location: properties.php');
    exit();
}

// Check if property is in favorites
$stmt = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND property_id = ?");
$stmt->execute([$_SESSION['user_id'], $property_id]);
$is_favorite = $stmt->fetch() ? true : false;

// Handle favorite toggle
if (isset($_POST['toggle_favorite'])) {
    if ($is_favorite) {
        // Remove from favorites
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND property_id = ?");
        $stmt->execute([$_SESSION['user_id'], $property_id]);
        $is_favorite = false;
    } else {
        // Add to favorites
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, property_id) VALUES (?, ?)");
        $stmt->execute([$_SESSION['user_id'], $property_id]);
        $is_favorite = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($property['title']); ?> - REMSR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .property-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }
        .property-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
        }
        .feature-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            border-radius: 50%;
            margin-right: 1rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-md-8">
                <?php if (!empty($property['image_path'])): ?>
                    <img src="../<?php echo htmlspecialchars($property['image_path']); ?>" class="property-image mb-4" alt="Property Image">
                <?php else: ?>
                    <img src="../assets/images/property-placeholder.svg" class="property-image mb-4" alt="Property Image">
                <?php endif; ?>
                
                <div class="property-details">
                    <h1 class="mb-4"><?php echo htmlspecialchars($property['title']); ?></h1>
                    <p class="text-muted mb-4">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($property['location']); ?>
                    </p>
                    <h3 class="text-primary mb-4">₱<?php echo number_format($property['price']); ?></h3>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon">
                                    <i class="fas fa-bed"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Bedrooms</h6>
                                    <p class="mb-0"><?php echo $property['bedrooms'] ?? 'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon">
                                    <i class="fas fa-bath"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Bathrooms</h6>
                                    <p class="mb-0"><?php echo $property['bathrooms'] ?? 'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="feature-icon">
                                    <i class="fas fa-ruler-combined"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Area</h6>
                                    <p class="mb-0"><?php echo $property['area'] ? $property['area'] . ' sq ft' : 'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h4 class="mb-3">Description</h4>
                    <p class="mb-4"><?php echo nl2br(htmlspecialchars($property['description'] ?? 'No description available.')); ?></p>

                    <form method="POST" class="d-inline">
                        <button type="submit" name="toggle_favorite" class="btn btn-primary">
                            <?php if ($is_favorite): ?>
                                <i class="fas fa-heart"></i> Remove from Favorites
                            <?php else: ?>
                                <i class="far fa-heart"></i> Add to Favorites
                            <?php endif; ?>
                        </button>
                    </form>
                    <a href="properties.php" class="btn btn-outline-secondary">Back to Properties</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Contact Agent</h5>
                        <form id="inquiryForm">
                            <input type="hidden" name="property_id" value="<?php echo $property['id']; ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Your Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="4" required>I'm interested in <?php echo htmlspecialchars($property['title']); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('inquiryForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('send_inquiry.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Message sent successfully!');
                this.reset();
                document.getElementById('message').value = 'I\'m interested in <?php echo addslashes(htmlspecialchars($property['title'])); ?>';
            } else {
                alert(data.message || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message');
        });
    });
    </script>
</body>
</html>