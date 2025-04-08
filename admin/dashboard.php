<?php
require_once __DIR__ . '/header.php';
require_once __DIR__ . '/../db_connection.php';

// Get statistics - simple version
$result = $conn->query("SELECT COUNT(*) AS count FROM products");
$products_count = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT COUNT(*) AS count FROM orders");
$orders_count = $result->fetch_assoc()['count'];

$result = $conn->query("SELECT SUM(total) AS total FROM orders WHERE status = 'completed'");
$revenue = $result->fetch_assoc()['total'] ?? 0;
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
    
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Products</h5>
                    <h2><?= $products_count ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Orders</h5>
                    <h2><?= $orders_count ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Revenue</h5>
                    <h2>$<?= number_format($revenue, 2) ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>