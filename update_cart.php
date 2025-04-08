<?php
session_start();
include 'db_connection.php';

$response = [
    'success' => false, 
    'count' => 0,
    'message' => '',
    'restoredStock' => false,
    'stockUpdated' => false
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);
    
    switch ($action) {
        case 'add':
            $name = $_POST['name'] ?? '';
            $price = floatval($_POST['price'] ?? 0);
            $image = $_POST['image'] ?? '';
            $stock = intval($_POST['stock'] ?? 0);
            
            // Check product stock
            $result = $conn->query("SELECT stock FROM products WHERE id = $id");
            if ($result && $row = $result->fetch_assoc()) {
                $availableStock = $row['stock'];
                
                // Check if already in cart
                $currentQuantity = $_SESSION['cart'][$id]['quantity'] ?? 0;
                
                if ($availableStock > 0 && $currentQuantity < $availableStock) {
                    if (isset($_SESSION['cart'][$id])) {
                        $_SESSION['cart'][$id]['quantity']++;
                    } else {
                        $_SESSION['cart'][$id] = [
                            'name' => $name,
                            'price' => $price,
                            'image' => $image,
                            'quantity' => 1
                        ];
                    }
                    
                    // Update stock in database
                    $conn->query("UPDATE products SET stock = stock - 1 WHERE id = $id");
                    
                    $response['success'] = true;
                } else {
                    $response['message'] = 'Sorry, this item is out of stock or you\'ve reached the maximum available quantity.';
                }
            } else {
                $response['message'] = 'Product not found.';
            }
            break;
            
        case 'update':
            $change = intval($_POST['change'] ?? 0);
            
            if (isset($_SESSION['cart'][$id])) {
                // Get current product stock
                $result = $conn->query("SELECT stock FROM products WHERE id = $id");
                $availableStock = $result && $row = $result->fetch_assoc() ? $row['stock'] : 0;
                
                $newQuantity = $_SESSION['cart'][$id]['quantity'] + $change;
                
                // Validate against stock
                if ($change > 0) {
                    // For increasing quantity, check stock
                    if ($availableStock > 0) {
                        $_SESSION['cart'][$id]['quantity'] = $newQuantity;
                        $conn->query("UPDATE products SET stock = stock - 1 WHERE id = $id");
                        $response['stockUpdated'] = true;
                        $response['newStock'] = $availableStock - 1;
                    } else {
                        $response['message'] = 'No more of this item in stock.';
                    }
                } else {
                    // For decreasing quantity
                    if ($newQuantity > 0) {
                        $_SESSION['cart'][$id]['quantity'] = $newQuantity;
                        $conn->query("UPDATE products SET stock = stock + 1 WHERE id = $id");
                        $response['stockUpdated'] = true;
                        $response['newStock'] = $availableStock + 1;
                    } else {
                        // Quantity would go to 0, so remove the item
                        unset($_SESSION['cart'][$id]);
                        $conn->query("UPDATE products SET stock = stock + {$_SESSION['cart'][$id]['quantity']} WHERE id = $id");
                        $response['stockUpdated'] = true;
                        $response['newStock'] = $availableStock + $_SESSION['cart'][$id]['quantity'];
                    }
                }
                
                $response['success'] = true;
            }
            break;
            
        case 'remove':
            if (isset($_SESSION['cart'][$id])) {
                // Get quantity being removed
                $quantity = $_SESSION['cart'][$id]['quantity'];
                
                // Restore stock
                $conn->query("UPDATE products SET stock = stock + $quantity WHERE id = $id");
                
                // Remove from cart
                unset($_SESSION['cart'][$id]);
                
                $response['success'] = true;
                $response['restoredStock'] = true;
                $response['productId'] = $id;
                $response['quantity'] = $quantity;
            }
            break;
    }
    
    // Calculate total items in cart
    $response['count'] = array_reduce($_SESSION['cart'], function($carry, $item) {
        return $carry + ($item['quantity'] ?? 0);
    }, 0);
}

header('Content-Type: application/json');
echo json_encode($response);
$conn->close();
?>