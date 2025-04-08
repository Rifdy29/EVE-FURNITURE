<?php
session_start();
include '../db_connection.php';

// Admin verification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $site_title = $_POST['site_title'];
    $admin_email = $_POST['admin_email'];
    $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
    
    // In a real application, you would save these to a settings table
    $_SESSION['message'] = "Settings updated successfully (demo)";
    header("Location: site_settings.php");
    exit();
}

// Demo settings - in real app you would fetch from database
$settings = [
    'site_title' => 'EVE Furniture',
    'admin_email' => 'admin@evefurniture.com',
    'maintenance_mode' => 0
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        <?php include 'admin_styles.php'; ?>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #FFA500;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>

    <div class="col-md-9 col-lg-10 admin-content">
        <h2 class="mb-4"><i class="fas fa-cog"></i> Site Settings</h2>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Site Title</label>
                        <input type="text" name="site_title" class="form-control" value="<?php echo $settings['site_title']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Admin Email</label>
                        <input type="email" name="admin_email" class="form-control" value="<?php echo $settings['admin_email']; ?>" required>
                    </div>
                    
                    <div class="form-group form-check">
                        <label class="form-check-label">
                            Maintenance Mode
                            <label class="switch ml-2">
                                <input type="checkbox" name="maintenance_mode" <?php echo $settings['maintenance_mode'] ? 'checked' : ''; ?>>
                                <span class="slider"></span>
                            </label>
                        </label>
                        <small class="form-text text-muted">When enabled, only admins can access the site</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Settings
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>