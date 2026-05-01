<?php
include '../includes/db.php';
include '../includes/payment_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$type = $_GET['type'] ?? 'internship';
$user_id = $_SESSION['user_id'];

// Get payment details based on type
if ($type === 'internship') {
    $stmt = $pdo->prepare("SELECT * FROM internships WHERE user_id = ? AND status = 'accepted' ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $record = $stmt->fetch();
    $amount = 20000; // 20,000 RWF for internship
    $description = "Internship Program Payment";
    $table = 'internships';
} else {
    $order_id = $_GET['order_id'] ?? 0;
    $stmt = $pdo->prepare("SELECT o.*, p.price FROM orders o JOIN products p ON o.product_id = p.id WHERE o.id = ?");
    $stmt->execute([$order_id]);
    $record = $stmt->fetch();
    $amount = $record['price'] ?? 0;
    $description = "Product Order Payment";
    $table = 'orders';
}

if (!$record || $record['payment_status'] === 'paid') {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Handle payment initiation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $provider = $_POST['provider'];
    $phone = $_POST['phone'];
    
    // Validate phone number
    $validatedPhone = validatePhoneNumber($phone);
    if (!$validatedPhone) {
        $error = "Invalid phone number format. Please use format: +250 7XX XXX XXX or 07XX XXX XXX";
    } else {
        // Initiate payment
        $result = initiateMobilePayment($provider, $validatedPhone, $amount, $description);
        
        if ($result['success']) {
            // Update database with payment attempt
            updatePaymentStatus($pdo, $table, $record['id'], 'pending', $result['transaction_id'], $provider, $validatedPhone);
            
            // Redirect to payment status page
            header("Location: payment_status.php?type=$type&txn=" . $result['transaction_id'] . "&record_id=" . $record['id']);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<?php include '../includes/header.php'; ?>

<main style="padding-top: 150px; padding-bottom: 80px;">
    <div class="container" style="max-width: 700px;">
        <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 50px; border-radius: 30px; backdrop-filter: blur(15px);">
            <div style="text-align: center; margin-bottom: 40px;">
                <div style="width: 80px; height: 80px; background: var(--gradient); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem;">
                    💳
                </div>
                <h2>Complete <span>Payment</span></h2>
                <p style="color: var(--text-muted); margin-top: 10px;">Secure mobile money payment</p>
            </div>

            <?php if ($error): ?>
                <div style="background: rgba(255, 71, 87, 0.1); border: 1px solid #ff4757; padding: 15px; border-radius: 10px; margin-bottom: 25px; color: #ff4757;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Payment Amount -->
            <div style="background: rgba(255,255,255,0.03); padding: 25px; border-radius: 15px; margin-bottom: 30px; text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Amount to Pay</p>
                <h1 style="color: var(--primary); font-size: 2.5rem; margin: 0;"><?php echo number_format($amount); ?> <span style="font-size: 1.5rem;">RWF</span></h1>
                <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 10px;"><?php echo $description; ?></p>
            </div>

            <form method="POST" id="paymentForm">
                <!-- Provider Selection -->
                <div style="margin-bottom: 30px;">
                    <label style="display: block; margin-bottom: 15px; font-weight: 600;">Select Mobile Money Provider *</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <label class="provider-option" data-provider="MTN" style="display: flex; flex-direction: column; align-items: center; padding: 20px; background: rgba(255,255,255,0.02); border: 2px solid var(--glass-border); border-radius: 15px; cursor: pointer; transition: all 0.3s;">
                            <input type="radio" name="provider" value="MTN" required style="display: none;">
                            <div style="font-size: 2.5rem; margin-bottom: 10px;">📱</div>
                            <span style="font-weight: 600; color: #FFCC00;">MTN Mobile Money</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">078 / 079</span>
                        </label>
                        <label class="provider-option" data-provider="Airtel" style="display: flex; flex-direction: column; align-items: center; padding: 20px; background: rgba(255,255,255,0.02); border: 2px solid var(--glass-border); border-radius: 15px; cursor: pointer; transition: all 0.3s;">
                            <input type="radio" name="provider" value="Airtel" required style="display: none;">
                            <div style="font-size: 2.5rem; margin-bottom: 10px;">📱</div>
                            <span style="font-weight: 600; color: #FF0000;">Airtel Money</span>
                            <span style="font-size: 0.75rem; color: var(--text-muted); margin-top: 5px;">073 / 072</span>
                        </label>
                    </div>
                </div>

                <!-- Phone Number Input -->
                <div class="form-group" style="margin-bottom: 30px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600;">Mobile Phone Number *</label>
                    <input type="tel" name="phone" id="phoneInput" required placeholder="+250 788 123 456" 
                           style="width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; font-size: 1.1rem; text-align: center;">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 8px; text-align: center;">
                        <i class="fas fa-info-circle"></i> Enter the number that will receive the payment prompt
                    </p>
                </div>

                <!-- Information Box -->
                <div style="background: rgba(102, 126, 234, 0.1); border-left: 4px solid #667eea; padding: 15px; border-radius: 5px; margin-bottom: 30px;">
                    <h4 style="margin: 0 0 10px 0; color: #667eea;"><i class="fas fa-shield-alt"></i> How it works</h4>
                    <ol style="margin: 0; padding-left: 20px; font-size: 0.85rem; line-height: 1.8;">
                        <li>Select your mobile money provider</li>
                        <li>Enter your phone number</li>
                        <li>Click "Pay Now" to initiate payment</li>
                        <li>You'll receive a prompt on your phone</li>
                        <li>Enter your mobile money PIN to confirm</li>
                        <li>Payment confirmation will be instant</li>
                    </ol>
                </div>

                <button type="submit" class="btn-submit" style="width: 100%; padding: 18px; border-radius: 50px; font-size: 1.2rem;">
                    <i class="fas fa-lock"></i> Pay Now - <?php echo number_format($amount); ?> RWF
                </button>
            </form>

            <p style="text-align: center; margin-top: 20px; color: var(--text-muted); font-size: 0.8rem;">
                <i class="fas fa-shield-alt"></i> Secure encrypted payment • Your information is protected
            </p>
        </div>
    </div>
</main>

<style>
.provider-option:hover {
    border-color: var(--primary) !important;
    background: rgba(102, 126, 234, 0.1) !important;
    transform: translateY(-2px);
}

.provider-option input:checked + div,
.provider-option:has(input:checked) {
    border-color: var(--primary) !important;
    background: rgba(102, 126, 234, 0.15) !important;
    box-shadow: 0 0 20px rgba(102, 126, 234, 0.3);
}
</style>

<script>
// Provider selection styling
document.querySelectorAll('.provider-option').forEach(option => {
    option.addEventListener('click', function() {
        const radio = this.querySelector('input[type="radio"]');
        radio.checked = true;
        
        // Auto-fill phone prefix based on provider
        const provider = this.dataset.provider;
        const phoneInput = document.getElementById('phoneInput');
        if (phoneInput.value === '' || phoneInput.value.startsWith('+250')) {
            if (provider === 'MTN') {
                phoneInput.value = '+250 78';
            } else if (provider === 'Airtel') {
                phoneInput.value = '+250 73';
            }
        }
        phoneInput.focus();
    });
});

// Phone number formatting
document.getElementById('phoneInput').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.startsWith('250')) {
        value = value.substring(3);
    } else if (value.startsWith('0')) {
        value = value.substring(1);
    }
    
    if (value.length > 0) {
        let formatted = '+250 ';
        if (value.length > 0) formatted += value.substring(0, 3);
        if (value.length > 3) formatted += ' ' + value.substring(3, 6);
        if (value.length > 6) formatted += ' ' + value.substring(6, 9);
        e.target.value = formatted.trim();
    }
});
</script>

<?php include '../includes/footer.php'; ?>
