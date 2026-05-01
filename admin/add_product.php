<?php include 'admin_header.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    // Handle Image Upload
    $image = 'default_product.jpg';
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
            }
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO products (name, category, description, price, stock, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $category, $description, $price, $stock, $image]);
        header('Location: products.php?msg=added');
        exit;
    } catch (PDOException $e) {
        $error = "Failed to add product: " . $e->getMessage();
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
        <h1>Add New <span>Product</span></h1>
    </div>

    <form method="POST" enctype="multipart/form-data" style="background: var(--glass); border: 1px solid var(--glass-border); padding: 40px; border-radius: 25px;">
        <div class="form-group" style="margin-bottom: 25px;">
            <label>Product Name *</label>
            <input type="text" name="name" required placeholder="e.g. MacBook Pro 2022" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px; margin-bottom: 25px;">
            <div class="form-group">
                <label>Category *</label>
                <select name="category" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                    <?php if (empty($categories)): ?>
                        <option value="Computers">Computers</option>
                        <option value="Electronics">Electronics</option>
                        <option value="Accessories">Accessories</option>
                        <option value="Cables">Cables</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Price (RWF) *</label>
                <input type="number" name="price" required placeholder="e.g. 1500000" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 25px;">
            <label>Stock Quantity *</label>
            <input type="number" name="stock" required value="1" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
        </div>

        <div class="form-group" style="margin-bottom: 30px;">
            <label>Description *</label>
            <textarea name="description" rows="5" required placeholder="Technical specifications and details..." style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white; resize: vertical;"></textarea>
        </div>

        <div class="form-group" style="margin-bottom: 40px;">
            <label>Product Image</label>
            <div style="border: 2px dashed var(--glass-border); border-radius: 15px; padding: 20px; text-align: center; color: var(--text-muted); position: relative;">
                <i class="fas fa-image" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                <input type="file" name="image" accept="image/*" style="cursor: pointer;">
                <p style="font-size: 0.8rem; margin-top: 10px;">Supported formats: JPG, PNG, WEBP</p>
            </div>
        </div>

        <button type="submit" class="btn-submit" style="width: 100%; padding: 15px; border-radius: 50px; font-size: 1.1rem;">
            Save Product to Catalog
        </button>
    </form>
</div>

</div><!-- end main-content -->
</body>
</html>
