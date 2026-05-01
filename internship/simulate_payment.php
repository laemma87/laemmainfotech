<?php
/**
 * Payment Simulator (Development Mode Only)
 * Simulates payment success/failure for testing
 */
include '../includes/db.php';
include '../includes/payment_helper.php';

header('Content-Type: application/json');

if (PAYMENT_MODE !== 'DEVELOPMENT') {
    echo json_encode(['success' => false, 'message' => 'Simulator only available in development mode']);
    exit;
}

$txn = $_POST['txn'] ?? '';
$status = $_POST['status'] ?? 'success';
$type = $_POST['type'] ?? 'internship';
$record_id = $_POST['record_id'] ?? 0;

if (empty($txn)) {
    echo json_encode(['success' => false, 'message' => 'Invalid transaction ID']);
    exit;
}

// Simulate payment confirmation
simulatePaymentConfirmation($txn, $status);

echo json_encode([
    'success' => true,
    'message' => 'Payment simulated: ' . $status
]);
?>
