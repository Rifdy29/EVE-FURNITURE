<?php
require_once 'db_connection.php';
require_once 'admin/admin_auth.php'; // Path to admin auth functions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    try {
        $user = db_fetch_one("SELECT * FROM users WHERE email = ?", [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['user_type'] === 'admin') {
                // Use the new initAdminSession function
                initAdminSession($user['id'], $user['name']);
                header("Location: admin/admin_dashboard.php");
            } else {
                // Regular user login
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                header("Location: customer_dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid email or password";
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        $error = "System error. Please try again later.";
    }
}
?>
<!-- Rest of your login form HTML remains the same -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVE Furniture - Login</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-header {
            color: #FFA500;
            text-align: center;
            margin-bottom: 30px;
        }
        .btn-login {
            background-color: #FFA500;
            color: white;
            width: 100%;
            padding: 10px;
        }
        .btn-login:hover {
            background-color: #e69500;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="login-header"><i class="fas fa-sign-in-alt"></i> EVE Furniture Login</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-login">Login</button>
            
            </form>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>