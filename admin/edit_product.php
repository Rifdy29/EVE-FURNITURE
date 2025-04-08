<?php
session_start();
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/admin_auth.php';

if (!isAdminLoggedIn() || !isset($_GET['id'])) {
    header("Location: ../login.php");
    exit();
}

$productId = (int)$_GET['id'];
$product = db_fetch_one("SELECT * FROM products WHERE id = ?", [$productId]);

if (!$product) {
    header("Location: admin_dashboard.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = htmlspecialchars($_POST['name']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $description = htmlspecialchars($_POST['description']);
        $image = $product['image'];

        // Handle image update
        if (!empty($_FILES['image']['name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);
            
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Only JPG, PNG, and WEBP files are allowed");
            }

            $uploadDir = '../assets/images/products/';
            $newImage = uniqid() . '_' . basename($_FILES['image']['name']);
            
            // Delete old image
            if ($image && file_exists($uploadDir . $image)) {
                unlink($uploadDir . $image);
            }
            
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newImage);
            $image = $newImage;
        }

        // Update product
        db_query("UPDATE products SET name = ?, price = ?, stock = ?, description = ?, image = ? WHERE id = ?",
                [$name, $price, $stock, $description, $image, $productId]);

        $success = "Product updated successfully!";
        $product = db_fetch_one("SELECT * FROM products WHERE id = ?", [$productId]); // Refresh data
    } catch (Exception $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | EVE Furniture</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .preview-image {
            max-width: 300px;
            margin: 1rem 0;
            border: 2px solid #dee2e6;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="mb-4">Edit Product</h2>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Product Name</label>
                    <input type="text" name="name" class="form-control" 
                           value="<?= htmlspecialchars($product['name']) ?>" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Price (MYR)</label>
                        <input type="number" step="0.01" name="price" class="form-control" 
                               value="<?= htmlspecialchars($product['price']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" class="form-control" 
                               value="<?= htmlspecialchars($product['stock']) ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" required><?= 
                        htmlspecialchars($product['description']) ?></textarea>
                </div>

                <div class="mb-4">
                    <label>Current Image</label>
                    <div>
                        <img src="../assets/images/products/<?= htmlspecialchars($product['image']) ?>" 
                             class="preview-image" 
                             alt="Current product image">
                    </div>
                    <label class="mt-2">Upload New Image</label>
                    <input type="file" name="image" class="form-control">
                    <small class="text-muted">Leave blank to keep current image</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>