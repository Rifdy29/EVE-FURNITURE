<?php
session_start();

// Redirect if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit();
}

// Validate cart items and calculate total
$total = 0;
$validItems = [];

foreach ($_SESSION['cart'] as $id => $item) {
    if (is_array($item) && isset($item['price']) && isset($item['quantity'])) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $validItems[$id] = $item;
    }
}

// Update session with valid items only
$_SESSION['cart'] = $validItems;

// If cart becomes empty after validation, redirect back
if (empty($_SESSION['cart'])) {
    header("Location: products.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - EVE Furniture</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/templatemo-sixteen.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        .cart-summary {
            margin-bottom: 30px;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .quantity-controls button {
            width: 30px;
            height: 30px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .total {
            font-weight: bold;
            font-size: 1.2em;
            text-align: right;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }
        .btn-checkout {
            background-color: #ff6600;
            color: white;
            width: 100%;
            padding: 12px;
            font-size: 1.1em;
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <div class="page-heading checkout-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>complete your order</h4>
                        <h2>Checkout</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="checkout-container">
                    <h2>Order Summary</h2>
                    
                    <div class="cart-summary">
                        <?php foreach ($_SESSION['cart'] as $id => $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                            <div class="cart-item">
                                <div>
                                    <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                                    <p>MYR<?php echo number_format($item['price'], 2); ?> each</p>
                                </div>
                                <div class="quantity-controls">
                                    <button class="btn btn-outline-secondary decrease-quantity" data-id="<?php echo $id; ?>">-</button>
                                    <span class="quantity"><?php echo $item['quantity']; ?></span>
                                    <button class="btn btn-outline-secondary increase-quantity" data-id="<?php echo $id; ?>">+</button>
                                </div>
                                <div>
                                    <p>MYR<?php echo number_format($subtotal, 2); ?></p>
                                    <button class="btn btn-danger remove-item" data-id="<?php echo $id; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="total">
                            <h4>Total: MYR<?php echo number_format($total, 2); ?></h4>
                        </div>
                    </div>

                    <form method="POST" action="process_order.php" id="checkout-form">
                        <h3>Shipping Information</h3>
                        
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Poscode</label>
                            <input type="text" name="zip" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-checkout">Place Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Quantity controls
            $(document).on('click', '.increase-quantity', function() {
                const id = $(this).data('id');
                updateCartItem(id, 1);
            });
            
            $(document).on('click', '.decrease-quantity', function() {
                const id = $(this).data('id');
                updateCartItem(id, -1);
            });
            
            // Remove item
            $(document).on('click', '.remove-item', function() {
                const id = $(this).data('id');
                if (confirm('Remove this item from cart?')) {
                    $.ajax({
                        url: 'update_cart.php',
                        method: 'POST',
                        data: { 
                            action: 'remove',
                            id: id
                        },
                        success: function() {
                            location.reload();
                        }
                    });
                }
            });
            
            function updateCartItem(id, change) {
                $.ajax({
                    url: 'update_cart.php',
                    method: 'POST',
                    data: { 
                        action: 'update',
                        id: id,
                        change: change
                    },
                    success: function() {
                        location.reload();
                    }
                });
            }
        });
    </script>
</body>
</html>