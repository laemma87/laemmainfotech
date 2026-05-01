<?php
include '../includes/db.php';
include '../includes/payment_helper.php';
include '../includes/email_helper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$type = $_GET['type'] ?? 'internship';
$txn = $_GET['txn'] ?? '';
$record_id = $_GET['record_id'] ?? 0;

if (empty($txn)) {
    header('Location: dashboard.php');
    exit;
}

// Get record details
$table = ($type === 'internship') ? 'internships' : 'orders';
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$record_id]);
$record = $stmt->fetch();

if (!$record) {
    header('Location: dashboard.php');
    exit;
}

$amount = ($type === 'internship') ? 20000 : $record['price'];
?>
<?php include '../includes/header.php'; ?>

<main style="padding-top: 150px; padding-bottom: 80px;">
    <div class="container" style="max-width: 700px;">
        <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 50px; border-radius: 30px; backdrop-filter: blur(15px);">
            
            <div id="paymentStatus" style="text-align: center;">
                <!-- Pending State -->
                <div id="pendingState">
                    <div class="spinner" style="width: 80px; height: 80px; margin: 0 auto 30px; border: 4px solid rgba(102, 126, 234, 0.2); border-top: 4px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                    <h2>Waiting for <span>Confirmation</span></h2>
                    <p style="color: var(--text-muted); margin: 20px 0;">Please check your phone and enter your PIN to complete the payment</p>
                    
                    <div style="background: rgba(255,255,255,0.03); padding: 25px; border-radius: 15px; margin: 30px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <span style="color: var(--text-muted);">Provider:</span>
                            <span style="font-weight: 600;"><?php echo htmlspecialchars($record['payment_provider']); ?> Mobile Money</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <span style="color: var(--text-muted);">Phone Number:</span>
                            <span style="font-weight: 600;"><?php echo htmlspecialchars($record['payment_phone']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                            <span style="color: var(--text-muted);">Amount:</span>
                            <span style="font-weight: 600; color: var(--primary);"><?php echo number_format($amount); ?> RWF</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Transaction ID:</span>
                            <span style="font-size: 0.85rem; font-family: monospace;"><?php echo htmlspecialchars($txn); ?></span>
                        </div>
                    </div>

                    <div style="background: rgba(102, 126, 234, 0.1); border-left: 4px solid #667eea; padding: 15px; border-radius: 5px; margin: 20px 0; text-align: left;">
                        <p style="margin: 0; font-size: 0.9rem;"><i class="fas fa-mobile-alt"></i> <strong>Check your phone</strong></p>
                        <p style="margin: 10px 0 0 0; font-size: 0.85rem; color: var(--text-muted);">
                            You should receive a payment prompt on <strong><?php echo htmlspecialchars($record['payment_phone']); ?></strong>. 
                            Enter your mobile money PIN to confirm the payment.
                        </p>
                    </div>

                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 30px;">
                        <i class="fas fa-sync-alt fa-spin"></i> Checking payment status...
                    </p>
                </div>

                <!-- Success State (hidden by default) -->
                <div id="successState" style="display: none;">
                    <div style="width: 100px; height: 100px; background: rgba(0, 255, 136, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; border: 3px solid #00ff88;">
                        <i class="fas fa-check" style="font-size: 3rem; color: #00ff88;"></i>
                    </div>
                    <h2 style="color: #00ff88;">Payment <span>Successful!</span></h2>
                    <p style="color: var(--text-muted); margin: 20px 0;">Your payment has been confirmed and processed.</p>
                    
                    <div style="background: rgba(0, 255, 136, 0.1); padding: 20px; border-radius: 15px; margin: 30px 0; border: 1px solid rgba(0, 255, 136, 0.3);">
                        <p style="margin: 0; font-size: 0.9rem;"><i class="fas fa-envelope"></i> A confirmation email has been sent to your email address.</p>
                    </div>

                    <a href="dashboard.php" class="btn-submit" style="display: inline-block; text-decoration: none; padding: 15px 40px; border-radius: 50px; margin-top: 20px;">
                        Go to Dashboard
                    </a>
                </div>

                <!-- Failed State (hidden by default) -->
                <div id="failedState" style="display: none;">
                    <div style="width: 100px; height: 100px; background: rgba(255, 71, 87, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; border: 3px solid #ff4757;">
                        <i class="fas fa-times" style="font-size: 3rem; color: #ff4757;"></i>
                    </div>
                    <h2 style="color: #ff4757;">Payment <span>Failed</span></h2>
                    <p style="color: var(--text-muted); margin: 20px 0;" id="errorMessage">The payment could not be completed. Please try again.</p>
                    
                    <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
                        <a href="payment.php?type=<?php echo $type; ?>" class="btn-submit" style="display: inline-block; text-decoration: none; padding: 15px 30px; border-radius: 50px;">
                            Try Again
                        </a>
                        <a href="dashboard.php" style="display: inline-block; padding: 15px 30px; border-radius: 50px; border: 1px solid var(--glass-border); text-decoration: none; color: white;">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <!-- Development Mode Simulator -->
            <?php if (PAYMENT_MODE === 'DEVELOPMENT'): ?>
            <div style="margin-top: 40px; padding: 20px; background: rgba(255, 165, 2, 0.1); border-radius: 15px; border: 1px solid #ffa502;">
                <h4 style="color: #ffa502; margin: 0 0 15px 0;"><i class="fas fa-flask"></i> Development Mode - Payment Simulator</h4>
                <p style="font-size: 0.85rem; margin-bottom: 15px; color: var(--text-muted);">Simulate payment outcome for testing:</p>
                <div style="display: flex; gap: 10px;">
                    <button onclick="simulatePayment('success')" style="flex: 1; padding: 10px; background: #00ff88; color: var(--darker); border: none; border-radius: 10px; cursor: pointer; font-weight: bold;">
                        ✓ Simulate Success
                    </button>
                    <button onclick="simulatePayment('failed')" style="flex: 1; padding: 10px; background: #ff4757; color: white; border: none; border-radius: 10px; cursor: pointer; font-weight: bold;">
                        ✗ Simulate Failure
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
const txnId = '<?php echo addslashes($txn); ?>';
const recordId = <?php echo $record_id; ?>;
const type = '<?php echo $type; ?>';
let pollInterval;

// Start polling for payment status
function startPolling() {
    pollInterval = setInterval(checkPaymentStatus, 3000); // Check every 3 seconds
}

// Check payment status
function checkPaymentStatus() {
    fetch('check_payment.php?txn=' + txnId + '&type=' + type + '&record_id=' + recordId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'paid') {
                clearInterval(pollInterval);
                showSuccess();
            } else if (data.status === 'failed') {
                clearInterval(pollInterval);
                showFailed(data.message || 'Payment was declined or cancelled');
            }
            // If pending, continue polling
        })
        .catch(error => {
            console.error('Error checking payment status:', error);
        });
}

// Show success state
function showSuccess() {
    document.getElementById('pendingState').style.display = 'none';
    document.getElementById('successState').style.display = 'block';
}

// Show failed state
function showFailed(message) {
    document.getElementById('pendingState').style.display = 'none';
    document.getElementById('failedState').style.display = 'block';
    document.getElementById('errorMessage').textContent = message;
}

// Simulate payment (development mode only)
function simulatePayment(status) {
    fetch('simulate_payment.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'txn=' + txnId + '&status=' + status + '&type=' + type + '&record_id=' + recordId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Force check status immediately
            setTimeout(checkPaymentStatus, 500);
        }
    });
}

// Start polling when page loads
startPolling();

// Stop polling when user leaves page
window.addEventListener('beforeunload', function() {
    clearInterval(pollInterval);
});
</script>

<?php include '../includes/footer.php'; ?>
