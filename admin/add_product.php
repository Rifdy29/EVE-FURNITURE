<?php
session_start();
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/admin_auth.php';

if (!isAdminLoggedIn()) {
    header("Location: ../login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        $name = htmlspecialchars($_POST['name']);
        $price = (float)$_POST['price'];
        $stock = (int)$_POST['stock'];
        $description = htmlspecialchars($_POST['description']);
        
        // Handle image upload
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
            $fileType = mime_content_type($_FILES['image']['tmp_name']);
            
            if (in_array($fileType, $allowedTypes)) {
                $uploadDir = '../assets/images/products/';
                $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
                move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName);
                $image = $fileName;
            } else {
                throw new Exception("Only JPG, PNG, and WEBP files are allowed");
            }
        }

        // Insert product
        db_query("INSERT INTO products (name, price, stock, description, image) VALUES (?, ?, ?, ?, ?)",
                [$name, $price, $stock, $description, $image]);

        $success = "Product added successfully!";
        $_POST = []; // Clear form
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
    <title>Add Product | EVE Furniture</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .preview-image {
            max-width: 200px;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="mb-4">Add New Product</h2>
            
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
                           value="<?= $_POST['name'] ?? '' ?>" required>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Price (MYR)</label>
                        <input type="number" step="0.01" name="price" class="form-control" 
                               value="<?= $_POST['price'] ?? '' ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label>Stock Quantity</label>
                        <input type="number" name="stock" class="form-control" 
                               value="<?= $_POST['stock'] ?? '' ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" required><?= 
                        $_POST['description'] ?? '' ?></textarea>
                </div>

                <div class="mb-4">
                    <label>Product Image</label>
                    <input type="file" name="image" class="form-control" required>
                    <small class="text-muted">Allowed formats: JPG, PNG, WEBP</small>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>