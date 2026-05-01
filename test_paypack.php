<?php
/**
 * Paypack Rwanda Test Script
 * Use this script to verify your Paypack configuration
 */
require_once 'includes/payment_helper.php';

// Test Configuration
$testPhone = '0789011738'; // You can change this to your phone number for testing
$testAmount = 100; // Small amount for testing (100 RWF)

echo "<h2>Paypack Integration Test</h2>";

// 1. Test Authentication
echo "<h3>1. Testing Authentication...</h3>";
$auth = getPaypackAccessToken();

if ($auth['success']) {
    $token = $auth['token'];
    echo "<p style='color: green;'>✅ Success! Connection to Paypack established.</p>";
    
    // 2. Test Payment Initiation
    echo "<h3>2. Testing Payment Initiation...</h3>";
    echo "<p>Checking for prompt on: <strong>$testPhone</strong> ($testAmount RWF)</p>";
    
    $result = initiatePaypackPayment($testPhone, $testAmount, 'Test Payment - LAEMMA');
    
    if ($result['success']) {
        echo "<p style='color: green;'>✅ Success! Payment prompt sent.</p>";
        echo "<p><strong>Transaction Ref:</strong> " . $result['transaction_id'] . "</p>";
        echo "<p>Please check your phone for the mobile money prompt.</p>";
        
        // 3. Test Status Check
        echo "<h3>3. Testing Status Verification...</h3>";
        $statusResult = verifyPaymentProduction($result['transaction_id']);
        echo "<p>Current Status: <strong>" . strtoupper($statusResult['status']) . "</strong></p>";
        
    } else {
        echo "<p style='color: red;'>❌ Payment Initiation Failed!</p>";
        echo "<p><strong>Error:</strong> " . $result['message'] . "</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Authentication Failed!</p>";
    echo "<p><strong>API Response:</strong> " . htmlspecialchars($auth['message']) . "</p>";
    echo "<p>Please check your Client ID and Client Secret in <code>includes/payment_helper.php</code>.</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Back to Home</a></p>";
?>
