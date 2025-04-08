<?php
session_start();
include '../db_connection.php';

// Admin verification
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$order_id = $_GET['id'];

// Fetch order details
$order = $conn->query("
    SELECT o.*, u.name as customer_name, u.email, u.phone 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = $order_id
")->fetch_assoc();

// Fetch order items
$items = $conn->query("
    SELECT oi.*, p.name as product_name, p.image 
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = $order_id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        <?php include 'admin_styles.php'; ?>
        .order-item-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'admin_nav.php'; ?>

    <div class="col-md-9 col-lg-10 admin-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-receipt"></i> Order Details #<?php echo $order_id; ?></h2>
            <a href="manage_orders.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Customer Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                        <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $order['phone'] ?? 'N/A'; ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($order['created_at'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong> 
                            <span class="badge 
                                <?php 
                                switch($order['status']) {
                                    case 'completed': echo 'badge-success'; break;
                                    case 'pending': echo 'badge-warning'; break;
                                    case 'cancelled': echo 'badge-danger'; break;
                                    default: echo 'badge-primary';
                                }
                                ?>
                            ">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </p>
                        <p><strong>Total Amount:</strong> $<?php echo number_format($order['total'], 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $items->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="../<?php echo $item['image']; ?>" alt="<?php echo $item['product_name']; ?>" class="order-item-img mr-3">
                                        <?php echo $item['product_name']; ?>
                                    </div>
                                </td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td><strong>$<?php echo number_format($order['total'], 2); ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>