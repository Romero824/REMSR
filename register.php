<?php
session_start();
require_once "config/database.php";
require_once "models/User.php";

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$username_err = $password_err = $confirm_password_err = $email_err = $full_name_err = $phone_err = $user_type_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate username
    if(empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        $user->username = trim($_POST["username"]);
        if($user->usernameExists()) {
            $username_err = "This username is already taken.";
        }
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $user->password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($user->password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        $user->email = trim($_POST["email"]);
        if($user->emailExists()) {
            $email_err = "This email is already registered.";
        }
    }
    
    // Validate full name
    if(empty(trim($_POST["full_name"]))) {
        $full_name_err = "Please enter your full name.";
    } else {
        $user->full_name = trim($_POST["full_name"]);
    }
    
    // Validate phone
    if(empty(trim($_POST["phone"]))) {
        $phone_err = "Please enter your phone number.";
    } else {
        $user->phone = trim($_POST["phone"]);
    }
    
    // Validate user type
    if(empty(trim($_POST["user_type"]))) {
        $user_type_err = "Please select a user type.";
    } else {
        $user->user_type = trim($_POST["user_type"]);
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && 
       empty($email_err) && empty($full_name_err) && empty($phone_err) && empty($user_type_err)) {
        
        if($user->create()) {
            header("location: login.php");
            exit;
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Real Estate Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <h2 class="text-center mb-4">Register</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($_POST["username"]) ? htmlspecialchars($_POST["username"]) : ''; ?>">
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>">
                    <span class="invalid-feedback"><?php echo $confirm_password_err; ?></span>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($_POST["email"]) ? htmlspecialchars($_POST["email"]) : ''; ?>">
                    <span class="invalid-feedback"><?php echo $email_err; ?></span>
                </div>
                
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-control <?php echo (!empty($full_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($_POST["full_name"]) ? htmlspecialchars($_POST["full_name"]) : ''; ?>">
                    <span class="invalid-feedback"><?php echo $full_name_err; ?></span>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" name="phone" class="form-control <?php echo (!empty($phone_err)) ? 'is-invalid' : ''; ?>" value="<?php echo isset($_POST["phone"]) ? htmlspecialchars($_POST["phone"]) : ''; ?>">
                    <span class="invalid-feedback"><?php echo $phone_err; ?></span>
                </div>
                
                <div class="mb-3">
                    <label for="user_type" class="form-label">User Type</label>
                    <select name="user_type" class="form-control <?php echo (!empty($user_type_err)) ? 'is-invalid' : ''; ?>">
                        <option value="">Select User Type</option>
                        <option value="owner" <?php echo (isset($_POST["user_type"]) && $_POST["user_type"] == "owner") ? "selected" : ""; ?>>Property Owner</option>
                        <option value="agent" <?php echo (isset($_POST["user_type"]) && $_POST["user_type"] == "agent") ? "selected" : ""; ?>>Real Estate Agent</option>
                        <option value="tenant" <?php echo (isset($_POST["user_type"]) && $_POST["user_type"] == "tenant") ? "selected" : ""; ?>>Tenant</option>
                        <option value="manager" <?php echo (isset($_POST["user_type"]) && $_POST["user_type"] == "manager") ? "selected" : ""; ?>>Property Manager</option>
                        <option value="buyer" <?php echo (isset($_POST["user_type"]) && $_POST["user_type"] == "buyer") ? "selected" : ""; ?>>Buyer/Investor</option>
                    </select>
                    <span class="invalid-feedback"><?php echo $user_type_err; ?></span>
                </div>
                
                <div class="mb-3">
                    <input type="submit" class="btn btn-primary w-100" value="Register">
                </div>
            </form>
            <div class="text-center mt-3">
                <p>Already have an account? <a href="login.php">Login here</a>.</p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 