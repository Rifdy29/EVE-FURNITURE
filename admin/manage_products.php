<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/header.php';

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        db_query("DELETE FROM products WHERE id = ?", [$_POST['delete_id']]);
        $_SESSION['message'] = "Product deleted successfully";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting product: " . $e->getMessage();
    }
    header("Location: manage_products.php");
    exit();
}

// Get all products
$products = db_fetch_all("SELECT * FROM products ORDER BY created_at DESC");
?>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fas fa-boxes"></i> Manage Products</h2>
    
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="add_product.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td><img src="../<?= $product['image'] ?>" width="50" class="img-thumbnail"></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td>$<?= number_format($product['price'], 2) ?></td>
                            <td><?= $product['stock'] ?></td>
                            <td><?= date('M j, Y', strtotime($product['created_at'])) ?></td>
                            <td>
                                <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Delete this product?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>