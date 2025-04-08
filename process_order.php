<?php
session_start();

// Check if cart exists and is not empty
if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit();
}

// Process the order when form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize input
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
    $zip = filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_STRING);
    
    // Calculate total
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        if (isset($item['price']) && isset($item['quantity'])) {
            $total += $item['price'] * $item['quantity'];
        }
    }
    
    // Here you would typically:
    // 1. Save to database
    // 2. Process payment
    // 3. Send confirmation email
    
    // For now, we'll just prepare order details
    $order_details = [
        'order_number' => 'ORD-' . uniqid(),
        'customer' => [
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'zip' => $zip
        ],
        'items' => $_SESSION['cart'],
        'total' => $total,
        'date' => date('Y-m-d H:i:s')
    ];
    
    // Clear the cart
    unset($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - EVE Furniture</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/templatemo-sixteen.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .order-summary {
            margin-top: 30px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .order-total {
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }
        .thank-you {
            color: #28a745;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container confirmation-container">
        <h2 class="thank-you">Thank you for your order!</h2>
        <p>Your order has been received and is being processed.</p>
        <p><strong>Order Number:</strong> <?php echo $order_details['order_number']; ?></p>
        <p><strong>Order Date:</strong> <?php echo $order_details['date']; ?></p>
        
        <div class="order-summary">
            <h4>Order Summary</h4>
            <?php foreach ($order_details['items'] as $id => $item): ?>
                <div class="order-item">
                    <div>
                        <h6><?php echo htmlspecialchars($item['name'] ?? 'Product'); ?></h6>
                        <small>Quantity: <?php echo htmlspecialchars($item['quantity'] ?? 0); ?></small>
                    </div>
                    <div>
                        <span>MYR<?php echo number_format($item['price'] ?? 0, 2); ?> each</span><br>
                        <span>MYR<?php echo number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 2); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="order-total">
                <p>Total: MYR<?php echo number_format($order_details['total'], 2); ?></p>
            </div>
        </div>
        
        <div class="shipping-info mt-4">
            <h4>Shipping Information</h4>
            <p>
                <?php echo htmlspecialchars($order_details['customer']['name']); ?><br>
                <?php echo htmlspecialchars($order_details['customer']['address']); ?><br>
                <?php echo htmlspecialchars($order_details['customer']['city']); ?>, 
                <?php echo htmlspecialchars($order_details['customer']['zip']); ?>
            </p>
        </div>
        
        <a href="products.php" class="btn btn-primary mt-4">Continue Shopping</a>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>