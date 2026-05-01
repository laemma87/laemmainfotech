<?php
include '../includes/db.php';

$msg = $_GET['msg'] ?? '';

?>
<?php include '../includes/header.php'; ?>

<main style="padding-top: 180px; padding-bottom: 100px; text-align: center;">
    <div class="container" style="max-width: 600px;">
        <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 60px 40px; border-radius: 30px; backdrop-filter: blur(20px);">
            <div style="width: 80px; height: 80px; background: rgba(0, 255, 136, 0.1); color: #00ff88; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 30px; border: 1px solid rgba(0, 255, 136, 0.3);">
                <i class="fas fa-check"></i>
            </div>
            
            <h1 style="margin-bottom: 20px;">Order <span>Confirmed!</span></h1>
            <p style="color: var(--text-muted); font-size: 1.1rem; line-height: 1.6; margin-bottom: 40px;">
                Thank you for your purchase. Your payment has been successfully verified. Our team is now processing your order for delivery.
            </p>

            <div style="background: rgba(255,255,255,0.03); border: 1px dashed var(--glass-border); padding: 30px; border-radius: 20px; margin-bottom: 40px;">
                <h4 style="margin-bottom: 10px;">Proof of Purchase</h4>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 20px;">Download your official confirmation letter to present upon delivery.</p>
                <a href="generate_letter.php" class="btn-submit" style="display: inline-block; padding: 12px 30px; border-radius: 50px; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-file-download"></i> Download Confirmation Letter
                </a>
            </div>

            <div style="display: flex; gap: 20px; justify-content: center;">
                <a href="index.php" style="color: var(--primary); font-weight: 600;">Continue Shopping</a>
                <span style="color: var(--glass-border);">|</span>
                <a href="../index.php" style="color: var(--text-muted);">Back to Home</a>
            </div>
        </div>
        
        <div style="margin-top: 50px;">
            <p style="color: var(--text-muted);">Need help? <a href="https://wa.me/250789011738" style="color: #25d366;"><i class="fab fa-whatsapp"></i> Chat with Admin</a></p>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
