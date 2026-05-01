<?php include 'admin_header.php'; ?>

<?php
// Handle delete
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: products.php?msg=deleted');
    exit;
}

// Fetch products
$products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <h1>Product <span>Management</span></h1>
    <a href="add_product.php" class="btn-submit" style="text-decoration: none; padding: 10px 20px; border-radius: 50px;">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88;">
        Operation completed successfully.
    </div>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Price (RWF)</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td style="display: flex; align-items: center; gap: 15px;">
                    <img src="/laemmainfotech/assets/uploads/products/<?php echo $p['image']; ?>" 
                         onerror="this.src='https://via.placeholder.com/40x40?text=No'"
                         style="width: 40px; height: 40px; border-radius: 5px; object-fit: cover;">
                    <span><?php echo htmlspecialchars($p['name']); ?></span>
                </td>
                <td><?php echo $p['category']; ?></td>
                <td><?php echo number_format($p['price']); ?></td>
                <td>
                    <span class="badge <?php echo ($p['stock'] > 0) ? 'badge-paid' : 'badge-pending'; ?>">
                        <?php echo $p['stock']; ?> In Stock
                    </span>
                </td>
                <td>
                    <a href="edit_product.php?id=<?php echo $p['id']; ?>" style="color: var(--primary); margin-right: 15px;"><i class="fas fa-edit"></i></a>
                    <a href="?delete=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure?')" style="color: #ff4757;"><i class="fas fa-trash"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div><!-- end main-content -->
</body>
</html>
