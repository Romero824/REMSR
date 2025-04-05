<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REMSR - Real Estate Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-home"></i> REMSR
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white" href="register.php">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">Find Your Perfect Property</h1>
                    <p class="lead mb-4">Discover your dream home with our extensive collection of properties for sale and rent.</p>
                    <div class="hero-buttons">
                        <a href="login.php" class="btn btn-primary btn-lg me-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Get Started
                        </a>
                        <a href="#features" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-info-circle me-2"></i>Learn More
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="hero-image">
                        <img src="assets/images/hero-image.jpg" alt="Modern Home" class="img-fluid rounded shadow">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section py-5">
        <div class="container">
            <h2 class="text-center mb-5 section-title">Why Choose REMSR?</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search-dollar"></i>
                        </div>
                        <h3>For Buyers</h3>
                        <p>Find your dream property with our extensive listings and advanced search features.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <h3>Easy Process</h3>
                        <p>Simple and straightforward process to find and inquire about properties.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h3>For Admins</h3>
                        <p>Manage properties and users efficiently with our comprehensive admin tools.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="about-image-container">
                        <img src="assets/images/About REMSR.jpg" alt="About REMSR" class="img-fluid rounded shadow-lg">
                        <div class="image-overlay"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2 class="section-title">About REMSR</h2>
                    <p class="lead">Your trusted partner in real estate management.</p>
                    <p>REMSR provides a comprehensive platform for managing real estate properties, connecting buyers and administrators in one seamless system.</p>
                    <ul class="about-features">
                        <li><i class="fas fa-check-circle text-primary"></i> Secure property management</li>
                        <li><i class="fas fa-check-circle text-primary"></i> Easy transaction processing</li>
                        <li><i class="fas fa-check-circle text-primary"></i> 24/7 customer support</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <style>
    .about-image-container {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        height: 400px;
        width: 100%;
    }

    .about-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .about-image-container:hover img {
        transform: scale(1.05);
    }
    
    .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    
    .about-features li {
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }
    
    .about-features i {
        margin-right: 10px;
    }

    @media (max-width: 768px) {
        .about-image-container {
            height: 300px;
        }
    }
    </style>

    <!-- Footer -->
    <footer class="footer py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>REMSR</h5>
                    <p>Your trusted partner in real estate management.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#features">Features</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-envelope"></i> cebu@gmail.com</li>
                        <li><i class="fas fa-phone"></i> +63 912 345 6789</li>
                        <li><i class="fas fa-map-marker-alt"></i> Sanciangko St, Cebu City</li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2024 REMSR. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 