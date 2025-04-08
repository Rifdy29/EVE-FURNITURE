<?php
session_start();
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/admin_auth.php';

if (!isAdminLoggedIn()) {
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management | EVE Furniture</title>
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #1a1a1a;
            --primary-light: #2d2d2d;
            --accent: #FFA500;
            --text-primary: #ffffff;
            --text-secondary: #cccccc;
        }

        body {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
            color: var(--text-primary);
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .sidebar {
            width: 280px;
            height: 100vh;
            position: fixed;
            background: var(--primary-dark);
            padding: 2rem 1.5rem;
        }

        .main-content {
            margin-left: 280px;
            padding: 3rem 2rem;
        }

        .product-card {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .product-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.06);
        }

        .btn-accent {
            background: var(--accent);
            color: #fff;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-accent:hover {
            background: #e69500;
            transform: translateY(-2px);
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }

        .status-active {
            background: #00c853;
        }

        .status-inactive {
            background: #ff1744;
        }

        .nav-link {
            color: var(--text-secondary) !important;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary) !important;
        }

        .nav-link.active {
            background: var(--accent) !important;
            color: #fff !important;
        }
    </style>
</head>
<body>
    <div class="sidebar glass-panel">
        <div class="d-flex flex-column h-100">
            <h3 class="mb-4">
                <i class="fas fa-cube mr-2"></i>
                EVE Admin
            </h3>
            
            <nav class="nav flex-column mb-auto">
                <a class="nav-link active" href="admin_dashboard.php">
                    <i class="fas fa-boxes mr-2"></i>Manage Products
                </a>
                <a class="nav-link mt-2" href="../logout.php">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </nav>
            
            <div class="mt-auto text-center">
                <small class="text-muted">Version 1.0.0</small>
            </div>
        </div>
    </div>

    <main class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="h3 mb-0">
                <i class="fas fa-cubes mr-2"></i>Product Management
            </h1>
            <a href="add_product.php" class="btn btn-accent">
                <i class="fas fa-plus-circle mr-2"></i>Add Product
            </a>
        </div>

        <div class="row">
            <?php
            $products = db_fetch_all("SELECT * FROM products ORDER BY created_at DESC");
            foreach ($products as $product): 
            ?>
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="product-card glass-panel p-3 h-100">
                    <div class="position-relative mb-3">
                        <img src="../assets/images/products/<?= htmlspecialchars($product['image']) ?>" 
                             class="img-fluid rounded-lg" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             style="height: 200px; object-fit: cover; width: 100%;">
                        <div class="position-absolute top-0 right-0 mt-2 mr-2">
                            <span class="status-indicator <?= $product['stock'] > 0 ? 'status-active' : 'status-inactive' ?>"></span>
                        </div>
                    </div>
                    
                    <h5 class="mb-2"><?= htmlspecialchars($product['name']) ?></h5>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="h5 text-accent">MYR <?= number_format($product['price'], 2) ?></span>
                        <span class="badge bg-dark">Stock: <?= $product['stock'] ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="edit_product.php?id=<?= $product['id'] ?>" 
                           class="btn btn-sm btn-outline-light">
                            <i class="fas fa-edit mr-2"></i>Edit
                        </a>
                        <a href="admin_dashboard.php?delete=<?= $product['id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Permanently delete this product?')">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>