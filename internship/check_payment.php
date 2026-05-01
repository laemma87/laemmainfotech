<?php
/**
 * Payment Status Checker
 * AJAX endpoint to check payment status
 */
include '../includes/db.php';
include '../includes/payment_helper.php';
include '../includes/email_helper.php';

header('Content-Type: application/json');

$txn = $_GET['txn'] ?? '';
$type = $_GET['type'] ?? 'internship';
$record_id = $_GET['record_id'] ?? 0;

if (empty($txn) || empty($record_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Get current payment status from database
$table = ($type === 'internship') ? 'internships' : 'orders';
$stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ?");
$stmt->execute([$record_id]);
$record = $stmt->fetch();

if (!$record) {
    echo json_encode(['success' => false, 'message' => 'Record not found']);
    exit;
}

// If already paid, return success
if ($record['payment_status'] === 'paid') {
    echo json_encode([
        'success' => true,
        'status' => 'paid',
        'message' => 'Payment confirmed'
    ]);
    exit;
}

// Check payment status with provider (or simulation)
$paymentStatus = verifyPayment($txn);

if ($paymentStatus['status'] === 'success' || $paymentStatus['status'] === 'paid') {
    // Update database
    $stmt = $pdo->prepare("UPDATE $table SET payment_status = 'paid' WHERE id = ?");
    $stmt->execute([$record_id]);
    
    // Send confirmation email
    $amount = ($type === 'internship') ? 20000 : $record['price'];
    sendPaymentConfirmation(
        $record['email'], 
        $record['full_names'] ?? $record['name'], 
        $amount, 
        $txn,
        $record['payment_provider']
    );
    
    echo json_encode([
        'success' => true,
        'status' => 'paid',
        'message' => 'Payment confirmed successfully'
    ]);
} elseif ($paymentStatus['status'] === 'failed') {
    // Update database
    $stmt = $pdo->prepare("UPDATE $table SET payment_status = 'failed' WHERE id = ?");
    $stmt->execute([$record_id]);
    
    echo json_encode([
        'success' => true,
        'status' => 'failed',
        'message' => $paymentStatus['message'] ?? 'Payment failed'
    ]);
} else {
    // Still pending
    echo json_encode([
        'success' => true,
        'status' => 'pending',
        'message' => 'Waiting for confirmation'
    ]);
}
?>
