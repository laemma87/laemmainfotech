<?php
include '../includes/db.php';

$product_id = $_GET['product_id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];
    
    // Create guest account or link to existing if logged in
    $user_id = $_SESSION['user_id'] ?? null;

    try {
        $price = $product['price'];
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, name, phone, email, address, price, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $product_id, $name, $phone, $email, $address, $price, $payment_method]);
        $order_id = $pdo->lastInsertId();
        
        // Redirect to payment simulator
        header("Location: ../internship/payment.php?type=product&product_id=$product_id&order_id=$order_id");
        exit;
    } catch (PDOException $e) {
        $error = "Failed to process order. Please try again.";
    }
}

?>
<?php include '../includes/header.php'; ?>

<main style="padding-top: 150px; padding-bottom: 80px;">
    <div class="container" style="max-width: 900px;">
        <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 40px;">
            <!-- Checkout Form -->
            <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 40px; border-radius: 25px;">
                <h2 style="margin-bottom: 30px;">Checkout <span>Details</span></h2>
                
                <?php if ($error): ?>
                    <p style="color: #ff4757; text-align: center; margin-bottom: 20px;"><?php echo $error; ?></p>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Full Name *</label>
                        <input type="text" name="name" required placeholder="John Doe" value="<?php echo $_SESSION['name'] ?? ''; ?>" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="text" name="phone" required placeholder="+250 7XX XXX XXX" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" placeholder="john@example.com" value="<?php echo $_SESSION['email'] ?? ''; ?>" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 25px;">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="3" placeholder="Street, City, District" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; resize: none;"></textarea>
                    </div>

                    <div style="margin-top: 40px; border-top: 1px solid var(--glass-border); padding-top: 30px;">
                        <h4 style="margin-bottom: 20px;">Payment Method</h4>
                        <div style="display: flex; gap: 20px; flex-direction: column;">
                            <label style="display: flex; align-items: center; gap: 15px; background: rgba(255,255,255,0.02); padding: 15px; border-radius: 12px; cursor: pointer; border: 1px solid var(--glass-border);">
                                <input type="radio" name="payment_method" value="Mobile Money" checked>
                                <span style="font-weight: 500;">MTN / Airtel Mobile Money</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 15px; background: rgba(255,255,255,0.02); padding: 15px; border-radius: 12px; cursor: pointer; border: 1px solid var(--glass-border);">
                                <input type="radio" name="payment_method" value="Bank Card">
                                <span style="font-weight: 500;">Credit / Debit Card</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="width: 100%; padding: 18px; border-radius: 50px; font-size: 1.2rem; margin-top: 40px;">Place Order Now</button>
                </form>
            </div>

            <!-- Order Summary -->
            <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 30px; border-radius: 25px; height: fit-content; position: sticky; top: 150px;">
                <h3 style="margin-bottom: 25px;">Order <span>Summary</span></h3>
                
                <div style="display: flex; gap: 15px; margin-bottom: 25px; align-items: center;">
                    <img src="https://via.placeholder.com/80x80?text=IMG" style="width: 80px; height: 80px; border-radius: 10px; background: rgba(0,0,0,0.2);">
                    <div>
                        <h5 style="margin-bottom: 5px;"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p style="color: var(--text-muted); font-size: 0.8rem;"><?php echo $product['category']; ?></p>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--glass-border); padding-top: 20px; margin-top: 20px;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: var(--text-muted);">Product Price</span>
                        <span><?php echo number_format($product['price']); ?> RWF</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                        <span style="color: var(--text-muted);">Delivery Fee</span>
                        <span style="color: #00ff88;">FREE</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 20px; padding-top: 15px; border-top: 2px solid var(--glass-border); font-size: 1.2rem; font-weight: 800;">
                        <span>Total</span>
                        <span style="color: var(--primary);"><?php echo number_format($product['price']); ?> RWF</span>
                    </div>
                </div>

                <div style="margin-top: 40px; text-align: center;">
                    <p style="font-size: 0.8rem; color: var(--text-muted);"><i class="fas fa-shield-alt"></i> Secure Encrypted Payment</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
