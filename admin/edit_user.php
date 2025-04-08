<?php
session_start();
include '../db_connection.php';

// Admin verification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_GET['id'];

// Fetch user details
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $user_type = $_POST['user_type'];
    
    // Update user
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, user_type = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $user_type, $user_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully";
        header("Location: manage_users.php");
        exit();
    } else {
        $error = "Error updating user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        <?php include 'admin_styles.php'; ?>
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>

    <div class="col-md-9 col-lg-10 admin-content">
        <h2 class="mb-4"><i class="fas fa-user-edit"></i> Edit User</h2>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-body">
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo $user['name']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>User Type</label>
                        <select name="user_type" class="form-control" required>
                            <option value="customer" <?php echo $user['user_type'] == 'customer' ? 'selected' : ''; ?>>Customer</option>
                            <option value="admin" <?php echo $user['user_type'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="manage_users.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                </form>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>