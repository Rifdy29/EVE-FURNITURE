<div class="container-fluid">
    <div class="row">
        <!-- Admin Navigation -->
        <div class="col-md-3 col-lg-2 p-0 admin-nav">
            <div class="admin-nav-header text-center">
                <h4><i class="fas fa-user-shield"></i> Admin Panel</h4>
                <p>Welcome, <?php echo $_SESSION['name']; ?></p>
            </div>
            <a href="admin_dashboard.php" class="admin-nav-link"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="manage_products.php" class="admin-nav-link"><i class="fas fa-boxes"></i> Manage Products</a>
            <a href="manage_orders.php" class="admin-nav-link"><i class="fas fa-shopping-cart"></i> Manage Orders</a>
            <a href="manage_users.php" class="admin-nav-link"><i class="fas fa-users"></i> Manage Users</a>
            <a href="manage_categories.php" class="admin-nav-link"><i class="fas fa-tags"></i> Categories</a>
            <a href="site_settings.php" class="admin-nav-link"><i class="fas fa-cog"></i> Site Settings</a>
            <a href="../logout.php" class="admin-nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>