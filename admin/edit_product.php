<?php include 'admin_header.php'; ?>

<?php
$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Handle Image Upload
    $image = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $filename = $_FILES['image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $new_filename = uniqid('prod_', true) . '.' . $ext;
            $upload_dir = __DIR__ . '/../assets/uploads/products/';
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename)) {
                $image = $new_filename;
                // Optional: Delete old image if it wasn't default
            }
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, category = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $category, $description, $price, $stock, $image, $id]);
        header('Location: products.php?msg=updated');
        exit;
    } catch (PDOException $e) {
        $error = "Failed to update product: " . $e->getMessage();
    }
}

// Fetch categories for the dropdown
$categories = [];
try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    // Categories table might be missing
}
?>

<div style="max-width: 800px;">
    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 40px;">
        <a href="products.php" style="color: var(--text-muted);"><i class="fas fa-arrow-left"></i></a>
        <h1>Edit <span>Product</span></h1>
    </div>

    <?php if (isset($error)): ?>
        <div style="background: rgba(255, 71, 87, 0.1); border: 1px solid #ff4757; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #ff4757;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" style="background: var(--glass); border: 1px solid var(--glass-border); padding: 40px; border-radius: 25px;">
        <div class="form-group" style="margin-bottom: 25px;">
            <label>Product Name *</label>
            <input type="text" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
            <div class="form-group">
                <label>Category *</label>
                <select name="category" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php if($product['category'] == $cat['name']) echo 'selected'; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?>
                        <option value="Computers" <?php if($product['category'] == 'Computers') echo 'selected'; ?>>Computers</option>
                        <option value="Electronics" <?php if($product['category'] == 'Electronics') echo 'selected'; ?>>Electronics</option>
                        <option value="Accessories" <?php if($product['category'] == 'Accessories') echo 'selected'; ?>>Accessories</option>
                        <option value="Cables" <?php if($product['category'] == 'Cables') echo 'selected'; ?>>Cables</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Price (RWF) *</label>
                <input type="number" name="price" required value="<?php echo $product['price']; ?>" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 25px;">
            <label>Stock Quantity *</label>
            <input type="number" name="stock" required value="<?php echo $product['stock']; ?>" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
        </div>

        <div class="form-group" style="margin-bottom: 25px;">
            <label>Current Image</label>
            <div style="margin-bottom: 15px;">
                <img src="/laemmainfotech/assets/uploads/products/<?php echo $product['image']; ?>" 
                     onerror="this.src='https://via.placeholder.com/100?text=No+Image'"
                     style="width: 100px; height: 100px; object-fit: cover; border-radius: 10px; border: 1px solid var(--glass-border);">
            </div>
            <label>Update Image</label>
            <input type="file" name="image" accept="image/*" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
        </div>

        <div class="form-group" style="margin-bottom: 30px;">
            <label>Description *</label>
            <textarea name="description" rows="5" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white; resize: vertical;"><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <button type="submit" class="btn-submit" style="width: 100%; padding: 15px; border-radius: 50px; font-size: 1.1rem;">
            Update Product Information
        </button>
    </form>
</div>

</div><!-- end main-content -->
</body>
</html>
