<?php
session_start();
include 'db_connection.php';

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch products from database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Eve Furniture - Products</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/templatemo-sixteen.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
    /* Orange Add to Cart Button */
    .add-to-cart {
        background-color: #FFA500;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .add-to-cart:hover {
        background-color: #e69500;
    }
    
    .add-to-cart.disabled {
        background-color: #cccccc;
        cursor: not-allowed;
    }

    /* Orange Cart Header */
    .card-header.cart-header {
        background-color: #FFA500 !important;
        color: white !important;
    }
    
    /* Stock styles */
    .stock-info {
        font-size: 0.9em;
        margin-bottom: 8px;
    }
    .text-success {
        color: #28a745;
    }
    .text-danger {
        color: #dc3545;
    }
    </style>
</head>
<body>
    <!-- Cart Notification -->
    <div class="cart-notification" id="cartNotification"></div>

    <!-- Header -->
    <?php include 'header.php'; ?>
    
    <!-- Page Heading -->
    <div class="page-heading products-heading header-text">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="text-content">
                        <h4>Quality Furniture</h4>
                        <h2>Our Products</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="products">
        <div class="container">
            <div class="row">
                <!-- Products Column -->
                <div class="col-md-9">
                    <div class="row">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="product-item">
                                    <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                                    <div class="down-content">
                                        <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                        <h6>MYR<?php echo number_format($row['price'], 2); ?></h6>
                                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                                        <button class="add-to-cart <?php echo $row['stock'] <= 0 ? 'disabled' : ''; ?>" 
                                                data-id="<?php echo htmlspecialchars($row['id']); ?>" 
                                                data-name="<?php echo htmlspecialchars($row['name']); ?>" 
                                                data-price="<?php echo htmlspecialchars($row['price']); ?>"
                                                data-image="<?php echo htmlspecialchars($row['image']); ?>"
                                                data-stock="<?php echo htmlspecialchars($row['stock']); ?>"
                                                <?php echo $row['stock'] <= 0 ? 'disabled' : ''; ?>>
                                            <i class="fas fa-cart-plus"></i> 
                                            <?php echo $row['stock'] <= 0 ? 'Out of Stock' : 'Add to Cart'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Shopping Cart Column -->
                <div class="col-md-3">
                    <div class="cart-container card">
                        <div class="card-header cart-header">
                            <h4 class="mb-0"><i class="fas fa-shopping-cart"></i> Your Cart</h4>
                        </div>
                        <div class="card-body">
                            <div id="cart-items">
                                <?php if (!empty($_SESSION['cart'])): ?>
                                    <?php 
                                    $total = 0;
                                    foreach ($_SESSION['cart'] as $id => $item): 
                                        if (!is_array($item) || !isset($item['price']) || !isset($item['quantity'])) {
                                            continue;
                                        }
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal;
                                    ?>
                                        <div class="cart-item">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                                    <small class="text-muted">$<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?></small>
                                                </div>
                                                <div class="text-right">
                                                    <span class="font-weight-bold">MYR<?php echo number_format($subtotal, 2); ?></span>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between mt-2">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-secondary decrease-quantity" data-id="<?php echo $id; ?>">-</button>
                                                    <button class="btn btn-outline-secondary increase-quantity" data-id="<?php echo $id; ?>">+</button>
                                                </div>
                                                <button class="btn btn-sm btn-danger remove-item" data-id="<?php echo $id; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <hr>
                                    <div class="d-flex justify-content-between font-weight-bold">
                                        <span>Total:</span>
                                        <span>MYR<?php echo number_format($total, 2); ?></span>
                                    </div>
                                    <a href="checkout.php" class="btn btn-success btn-block mt-3">
                                        <i class="fas fa-credit-card"></i> Checkout
                                    </a>
                                <?php else: ?>
                                    <div class="text-center py-3">
                                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Your cart is empty</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <!-- JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add to cart with animation
            $('.add-to-cart').click(function() {
                const button = $(this);
                if (button.hasClass('disabled')) return;
                
                const id = button.data('id');
                const name = button.data('name');
                const price = parseFloat(button.data('price'));
                const image = button.data('image');
                const stock = parseInt(button.data('stock'));
                
                // Add animation
                button.html('<i class="fas fa-spinner fa-spin"></i> Adding...');
                button.prop('disabled', true);
                
                // Add to cart
                $.ajax({
                    url: 'update_cart.php',
                    method: 'POST',
                    data: { 
                        action: 'add',
                        id: id,
                        name: name,
                        price: price,
                        image: image,
                        stock: stock
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update cart display
                            $('#cart-items').load(location.href + ' #cart-items > *');
                            
                            // Show notification
                            const notification = $('#cartNotification');
                            notification.text(name + ' added to cart!');
                            notification.fadeIn();
                            setTimeout(() => notification.fadeOut(), 2000);
                            
                            // Update cart count in header
                            $('.cart-badge').text(response.count);
                            
                            // Update stock display
                            const newStock = stock - 1;
                            button.data('stock', newStock);
                            button.closest('.product-item').find('.stock-info').html(
                                newStock > 0 
                                    ? '<span class="text-success"><i class="fas fa-check"></i> In Stock (' + newStock + ' available)</span>'
                                    : '<span class="text-danger"><i class="fas fa-times"></i> Out of Stock</span>'
                            );
                            
                            if (newStock <= 0) {
                                button.addClass('disabled').prop('disabled', true).html('<i class="fas fa-cart-plus"></i> Out of Stock');
                            }
                        } else {
                            alert(response.message || 'Failed to add to cart. Please try again.');
                        }
                    },
                    complete: function() {
                        // Reset button
                        if (button.data('stock') > 0) {
                            button.html('<i class="fas fa-cart-plus"></i> Add to Cart');
                            button.prop('disabled', false);
                        }
                    }
                });
            });
            
            // Quantity controls
            $(document).on('click', '.increase-quantity', function() {
                updateQuantity($(this).data('id'), 1);
            });
            
            $(document).on('click', '.decrease-quantity', function() {
                updateQuantity($(this).data('id'), -1);
            });
            
            // Remove item
            $(document).on('click', '.remove-item', function() {
                if (confirm('Remove this item from cart?')) {
                    $.ajax({
                        url: 'update_cart.php',
                        method: 'POST',
                        data: { 
                            action: 'remove',
                            id: $(this).data('id')
                        },
                        success: function(response) {
                            $('#cart-items').load(location.href + ' #cart-items > *');
                            $('.cart-badge').text(response.count);
                            
                            // Update stock display if needed
                            if (response.restoredStock) {
                                $('.product-item').each(function() {
                                    const productId = $(this).find('.add-to-cart').data('id');
                                    if (productId == response.productId) {
                                        const newStock = parseInt($(this).find('.add-to-cart').data('stock')) + response.quantity;
                                        $(this).find('.add-to-cart').data('stock', newStock);
                                        $(this).find('.stock-info').html(
                                            '<span class="text-success"><i class="fas fa-check"></i> In Stock (' + newStock + ' available)</span>'
                                        );
                                        $(this).find('.add-to-cart')
                                            .removeClass('disabled')
                                            .prop('disabled', false)
                                            .html('<i class="fas fa-cart-plus"></i> Add to Cart');
                                    }
                                });
                            }
                        }
                    });
                }
            });
            
            function updateQuantity(id, change) {
                $.ajax({
                    url: 'update_cart.php',
                    method: 'POST',
                    data: { 
                        action: 'update',
                        id: id,
                        change: change
                    },
                    success: function(response) {
                        $('#cart-items').load(location.href + ' #cart-items > *');
                        $('.cart-badge').text(response.count);
                        
                        if (response.stockUpdated) {
                            // Update the product stock display if quantity was adjusted due to stock limits
                            $('.product-item').each(function() {
                                const productId = $(this).find('.add-to-cart').data('id');
                                if (productId == id) {
                                    const newStock = response.newStock;
                                    $(this).find('.add-to-cart').data('stock', newStock);
                                    $(this).find('.stock-info').html(
                                        newStock > 0 
                                            ? '<span class="text-success"><i class="fas fa-check"></i> In Stock (' + newStock + ' available)</span>'
                                            : '<span class="text-danger"><i class="fas fa-times"></i> Out of Stock</span>'
                                    );
                                    
                                    if (newStock <= 0) {
                                        $(this).find('.add-to-cart')
                                            .addClass('disabled')
                                            .prop('disabled', true)
                                            .html('<i class="fas fa-cart-plus"></i> Out of Stock');
                                    } else {
                                        $(this).find('.add-to-cart')
                                            .removeClass('disabled')
                                            .prop('disabled', false)
                                            .html('<i class="fas fa-cart-plus"></i> Add to Cart');
                                    }
                                }
                            });
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
<?php $conn->close(); ?>