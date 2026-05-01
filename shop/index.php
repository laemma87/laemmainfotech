<?php
include '../includes/db.php';

// Fetch categories
$categories = ['All', 'Computers', 'Electronics', 'Accessories', 'Cables'];
$current_category = $_GET['category'] ?? 'All';

// Fetch products based on category
if ($current_category === 'All') {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
    $stmt->execute([$current_category]);
}
$products = $stmt->fetchAll();

// Trending products (just the latest 3 for now)
$trending_stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 3");
$trending_products = $trending_stmt->fetchAll();

?>
<?php include '../includes/header.php'; ?>

<main style="padding-top: 120px; padding-bottom: 80px;">
    <div class="container">
        <!-- Shop Header -->
        <div style="text-align: center; margin-bottom: 60px;">
            <h1>Our <span>Products</span></h1>
            <p style="color: var(--text-muted);">High-quality electronics and IT equipment.</p>
        </div>

        <!-- Trending Section -->
        <section style="margin-bottom: 80px;">
            <h2 style="margin-bottom: 30px;"><i class="fas fa-fire" style="color: #ff4757;"></i> Trending <span>Now</span></h2>
            <div class="grid-4" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                <?php foreach ($trending_products as $product): ?>
                    <div class="card" style="padding: 20px;">
                        <div style="height: 200px; background: rgba(255,255,255,0.05); border-radius: 15px; overflow: hidden; margin-bottom: 20px; position: relative;">
                            <?php if ($product['discount'] > 0): ?>
                                <span style="position: absolute; top: 10px; right: 10px; background: #ff4757; color: white; padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: bold;">Sale</span>
                            <?php endif; ?>
                            <img src="/laemmainfotech/assets/uploads/products/<?php echo $product['image']; ?>" 
                                 alt="<?php echo $product['name']; ?>" 
                                 onerror="this.src='https://via.placeholder.com/300x200?text=<?php echo urlencode($product['name']); ?>'"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin: 10px 0;"><?php echo substr(htmlspecialchars($product['description']), 0, 60); ?>...</p>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                            <span style="font-size: 1.25rem; font-weight: 800; color: var(--primary);"><?php echo number_format($product['price']); ?> RWF</span>
                            <a href="product.php?id=<?php echo $product['id']; ?>" style="background: var(--glass); border: 1px solid var(--glass-border); padding: 8px 15px; border-radius: 10px; font-size: 0.9rem;"><i class="far fa-eye"></i> View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Categories & Filter -->
        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 40px; justify-content: center;">
            <?php foreach ($categories as $cat): ?>
                <a href="?category=<?php echo $cat; ?>" 
                   style="padding: 10px 25px; border-radius: 50px; background: <?php echo ($current_category === $cat) ? 'var(--gradient)' : 'var(--glass)'; ?>; border: 1px solid var(--glass-border); font-weight: 600;">
                   <?php echo $cat; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- All Products Grid -->
        <div class="grid-4">
            <?php if (empty($products)): ?>
                <p style="grid-column: 1/-1; text-align: center; color: var(--text-muted); padding: 50px;">No products found in this category.</p>
            <?php endif; ?>

            <?php foreach ($products as $product): ?>
                <div class="card" style="padding: 20px;">
                    <div style="height: 180px; background: rgba(255,255,255,0.05); border-radius: 15px; overflow: hidden; margin-bottom: 20px;">
                        <img src="/laemmainfotech/assets/uploads/products/<?php echo $product['image']; ?>" 
                             onerror="this.src='https://via.placeholder.com/300x180?text=<?php echo urlencode($product['name']); ?>'"
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <span style="color: var(--primary); font-weight: 800; font-size: 1.1rem; display: block; margin: 10px 0;"><?php echo number_format($product['price']); ?> RWF</span>
                    <a href="product.php?id=<?php echo $product['id']; ?>" class="btn-submit" style="display: block; text-align: center; font-size: 0.9rem; padding: 10px; border-radius: 10px; text-decoration: none;">Buy Now</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>

<!-- Chatbox Trigger -->
<div style="position: fixed; bottom: 30px; right: 30px; z-index: 1000;">
    <a href="https://wa.me/250789011738" target="_blank" style="width: 60px; height: 60px; border-radius: 50%; background: #25d366; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px rgba(37, 211, 102, 0.4); color: white; font-size: 1.8rem;">
        <i class="fab fa-whatsapp"></i>
    </a>
</div>

<?php include '../includes/footer.php'; ?>
