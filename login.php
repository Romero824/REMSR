<?php
session_start();
require_once "config/database.php";
require_once "models/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$login_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    if($user->login($username, $password)) {
        if($user->status == 'active') {
            $_SESSION["loggedin"] = true;
            $_SESSION["user_id"] = $user->user_id;
            $_SESSION["username"] = $user->username;
            $_SESSION["user_type"] = $user->user_type;
            
            // Redirect based on user type
            switch($user->user_type) {
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
            }
            exit;
        } else {
            $login_err = "Your account is not active. Please contact support.";
        }
    } else {
        $login_err = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - REMSR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="text-center mb-4">Login</h2>
            
            <?php 
            if(!empty($login_err)){
                echo '<div class="alert alert-danger">' . $login_err . '</div>';
            }        
            ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>    
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary w-100" value="Login">
                </div>
            </form>
            <div class="text-center mt-3">
                <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 