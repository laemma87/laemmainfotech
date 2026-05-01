<?php
include '../includes/db.php';

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

// Fetch 4 other products for "Related Products"
$related_stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4");
$related_stmt->execute([$product['category'], $id]);
$related_products = $related_stmt->fetchAll();

?>
<?php include '../includes/header.php'; ?>

<main style="padding-top: 150px; padding-bottom: 80px;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: start;">
            <!-- Left: Image -->
            <div style="background: var(--glass); border: 1px solid var(--glass-border); border-radius: 30px; padding: 20px; overflow: hidden; position: sticky; top: 120px;">
                <img src="/laemmainfotech/assets/uploads/products/<?php echo $product['image']; ?>" 
                     alt="<?php echo $product['name']; ?>" 
                     onerror="this.src='https://via.placeholder.com/600x600?text=<?php echo urlencode($product['name']); ?>'"
                     style="width: 100%; border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); height: 500px; object-fit: cover;">
            </div>

            <!-- Right: Details -->
            <div>
                <nav style="margin-bottom: 20px; color: var(--text-muted); font-size: 0.9rem;">
                    <a href="index.php">Shop</a> / <a href="index.php?category=<?php echo $product['category']; ?>"><?php echo $product['category']; ?></a> / <?php echo $product['name']; ?>
                </nav>
                
                <h1 style="font-size: 3rem; line-height: 1.1; margin-bottom: 20px;"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                    <span style="font-size: 2.5rem; font-weight: 800; color: var(--primary);"><?php echo number_format($product['price']); ?> RWF</span>
                    <?php if ($product['discount'] > 0): ?>
                        <span style="text-decoration: line-through; color: var(--text-muted); font-size: 1.2rem;"><?php echo number_format($product['price'] * 1.1); ?> RWF</span>
                        <span style="background: rgba(255, 71, 87, 0.1); color: #ff4757; padding: 5px 12px; border-radius: 5px; font-weight: bold; border: 1px solid rgba(255, 71, 87, 0.2);">Save 10%</span>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom: 40px; padding: 30px; background: var(--glass); border: 1px solid var(--glass-border); border-radius: 20px;">
                    <h4 style="margin-bottom: 15px; color: var(--light);">Technical Specs</h4>
                    <p style="color: var(--text-muted); line-height: 1.8;">
                        <?php echo nl2br(htmlspecialchars($product['description'])); ?>
                    </p>
                    <div style="margin-top: 25px; display: flex; gap: 40px; border-top: 1px solid var(--glass-border); padding-top: 25px;">
                        <div>
                            <p style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Availability</p>
                            <p style="font-weight: 600; color: <?php echo ($product['stock'] > 0) ? '#00ff88' : '#ff4757'; ?>;">
                                <i class="fas <?php echo ($product['stock'] > 0) ? 'fa-check-circle' : 'fa-times-circle'; ?>"></i> 
                                <?php echo ($product['stock'] > 0) ? 'In Stock ('.$product['stock'].' units)' : 'Out of Stock'; ?>
                            </p>
                        </div>
                        <div>
                            <p style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase;">Delivery</p>
                            <p style="font-weight: 600;">24-48 Hours</p>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 20px;">
                    <a href="checkout.php?product_id=<?php echo $product['id']; ?>" class="btn-submit" style="flex: 2; text-align: center; padding: 20px; font-size: 1.2rem; border-radius: 50px; text-decoration: none;">
                        <i class="fas fa-shopping-cart"></i> Buy This Now
                    </a>
                    <a href="https://wa.me/250789011738?text=I'm interested in <?php echo urlencode($product['name']); ?>" target="_blank" style="flex: 1; border: 1px solid var(--glass-border); background: var(--glass); border-radius: 50px; display: flex; align-items: center; justify-content: center; color: white; gap: 10px;">
                        <i class="fab fa-whatsapp"></i> Chat Admin
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <?php if ($related_products): ?>
        <section style="margin-top: 100px;">
            <h2 style="margin-bottom: 40px;">Related <span>Products</span></h2>
            <div class="grid-4" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
                <?php foreach ($related_products as $rp): ?>
                    <a href="product.php?id=<?php echo $rp['id']; ?>" class="card" style="padding: 15px;">
                        <img src="/laemmainfotech/assets/uploads/products/<?php echo $rp['image']; ?>" 
                             onerror="this.src='https://via.placeholder.com/250x180?text=<?php echo urlencode($rp['name']); ?>'"
                             style="width: 100%; height: 180px; object-fit: cover; border-radius: 10px; margin-bottom: 15px;">
                        <h5 style="margin-bottom: 10px;"><?php echo htmlspecialchars($rp['name']); ?></h5>
                        <p style="color: var(--primary); font-weight: bold;"><?php echo number_format($rp['price']); ?> RWF</p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
