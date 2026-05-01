<?php
/**
 * Mobile Money Payment Helper
 * Handles MTN Mobile Money, Tigo Cash, and Airtel Money integration
 * 
 * DEVELOPMENT MODE: Uses simulation for testing
 * PRODUCTION MODE: Integrate with actual payment provider APIs
 */

// Payment configuration
// Payment configuration
define('PAYMENT_MODE', 'DEVELOPMENT'); // Change to 'PRODUCTION' when ready

// Flutterwave API Keys (Recommended)
define('FLW_PUBLIC_KEY', 'your-public-key');
define('FLW_SECRET_KEY', 'your-secret-key');
define('FLW_ENCRYPTION_KEY', 'your-encryption-key');

// Direct Provider Keys (Client ID/Secret)
// Use these if you are connecting directly to MTN/Airtel portals
define('PAYMENT_CLIENT_ID', '02c77dc2-2155-45da-90ce-80ec0d7c0570');
define('PAYMENT_CLIENT_SECRET', 'HM8x8A1nW4cZj8A5gzY3boeU6slye1pk');
define('PAYMENT_ENCRYPTION_KEY', 'M6FIo4u28/iRK778f4FEkUk4O0yv+Nq5+1q4ubnO8Nw=');

/**
 * Validate Rwanda phone number format
 */
function validatePhoneNumber($phone) {
    // Remove spaces and special characters
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Rwanda phone number patterns
    // +250 7XX XXX XXX or 07XX XXX XXX
    $patterns = [
        '/^\+?250[7][0-9]{8}$/',  // +250 7XX XXX XXX
        '/^0[7][0-9]{8}$/'         // 07XX XXX XXX
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $phone)) {
            // Normalize to international format
            if (substr($phone, 0, 1) === '0') {
                $phone = '+250' . substr($phone, 1);
            } elseif (substr($phone, 0, 1) !== '+') {
                $phone = '+' . $phone;
            }
            return $phone;
        }
    }
    
    return false;
}

/**
 * Detect mobile provider from phone number
 */
function detectProvider($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Rwanda mobile network prefixes
    // MTN: 078, 079
    // Airtel: 073, 072
    // Tigo: (no longer active in Rwanda, but kept for legacy)
    
    if (preg_match('/^(250)?(078|079)/', $phone)) {
        return 'MTN';
    } elseif (preg_match('/^(250)?(073|072)/', $phone)) {
        return 'Airtel';
    }
    
    return 'Unknown';
}

/**
 * Generate unique transaction reference
 */
function generateTransactionReference($prefix = 'LAEM') {
    return $prefix . date('Ymd') . strtoupper(substr(uniqid(), -8));
}

/**
 * Initiate mobile money payment
 * Returns transaction ID or false on failure
 */
function initiateMobilePayment($provider, $phone, $amount, $description = 'Payment') {
    global $pdo;
    
    // Validate phone number
    $phone = validatePhoneNumber($phone);
    if (!$phone) {
        return ['success' => false, 'message' => 'Invalid phone number format'];
    }
    
    // Generate transaction reference
    $reference = generateTransactionReference();
    
    // Log payment attempt
    $logFile = __DIR__ . '/../payment_logs.txt';
    $logEntry = "\n" . str_repeat("=", 80) . "\n";
    $logEntry .= "Date: " . date('Y-m-d H:i:s') . "\n";
    $logEntry .= "Provider: $provider\n";
    $logEntry .= "Phone: $phone\n";
    $logEntry .= "Amount: $amount RWF\n";
    $logEntry .= "Reference: $reference\n";
    $logEntry .= "Description: $description\n";
    
    if (PAYMENT_MODE === 'DEVELOPMENT') {
        // SIMULATION MODE
        $logEntry .= "Mode: SIMULATION\n";
        $logEntry .= "Status: Payment prompt sent (simulated)\n";
        file_put_contents($logFile, $logEntry . str_repeat("=", 80) . "\n", FILE_APPEND);
        
        return [
            'success' => true,
            'transaction_id' => $reference,
            'message' => 'Payment prompt sent to ' . $phone,
            'provider' => $provider,
            'phone' => $phone,
            'amount' => $amount,
            'status' => 'pending'
        ];
    } else {
        // PRODUCTION MODE - Integrate with actual APIs
        switch ($provider) {
            case 'MTN':
                return initiateMTNPayment($phone, $amount, $reference, $description);
            case 'Airtel':
                return initiateAirtelPayment($phone, $amount, $reference, $description);
            case 'Tigo':
                return initiateTigoPayment($phone, $amount, $reference, $description);
            default:
                return ['success' => false, 'message' => 'Unsupported payment provider'];
        }
    }
}

/**
 * Get Paypack Access Token (JWT)
 */
function getPaypackAccessToken() {
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://payments.paypack.rw/api/auth/agents/authorize",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'client_id' => PAYMENT_CLIENT_ID,
            'client_secret' => PAYMENT_CLIENT_SECRET
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        error_log("Paypack Auth Error (CURL): " . $err);
        return ['success' => false, 'message' => "CURL Error: " . $err];
    }

    $data = json_decode($response, true);
    if (isset($data['access'])) {
        return ['success' => true, 'token' => $data['access']];
    } else {
        error_log("Paypack Auth Failed: " . $response);
        return ['success' => false, 'message' => $response ?: "Unknown API Error"];
    }
}

/**
 * Initiate Mobile Money Payment via Paypack
 */
function initiatePaypackPayment($phone, $amount, $description) {
    $auth = getPaypackAccessToken();
    if (!$auth['success']) {
        return ['success' => false, 'message' => 'Auth Error: ' . $auth['message']];
    }
    $token = $auth['token'];

    // Paypack expects the number in 07... format for some operations, 
    // but normalizePhoneNumber usually returns +250...
    // Let's ensure it's in the format Paypack likes (e.g., 078...)
    $cleanPhone = str_replace('+250', '0', $phone);

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://payments.paypack.rw/api/transactions/cashin",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'amount' => $amount,
            'number' => $cleanPhone,
            'environment' => PAYMENT_MODE === 'DEVELOPMENT' ? 'test' : 'live'
        ]),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return ['success' => false, 'message' => 'Paypack Request Error: ' . $err];
    }

    $data = json_decode($response, true);
    
    // Paypack response handling
    if (isset($data['ref'])) {
        return [
            'success' => true,
            'transaction_id' => $data['ref'],
            'message' => 'Payment prompt sent to ' . $phone,
            'status' => 'pending'
        ];
    } else {
        return [
            'success' => false, 
            'message' => isset($data['message']) ? $data['message'] : 'Payment initiation failed'
        ];
    }
}

/**
 * Verify Paypack Payment Status
 */
function verifyPaypackPaymentProduction($transactionId) {
    $auth = getPaypackAccessToken();
    if (!$auth['success']) {
        return ['success' => false, 'message' => 'Auth Error: ' . $auth['message']];
    }
    $token = $auth['token'];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://payments.paypack.rw/api/transactions/find/" . $transactionId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        return ['success' => false, 'status' => 'error', 'message' => $err];
    }

    $data = json_decode($response, true);
    
    // Paypack status mapping
    $status = 'pending';
    if (isset($data['status'])) {
        if ($data['status'] === 'successful') {
            $status = 'success';
        } elseif ($data['status'] === 'failed') {
            $status = 'failed';
        }
    }

    return [
        'success' => true,
        'status' => $status,
        'message' => 'Payment status: ' . $status,
        'raw_response' => $data
    ];
}

/**
 * MTN Mobile Money API Integration (Routes to Paypack)
 */
function initiateMTNPayment($phone, $amount, $reference, $description) {
    return initiatePaypackPayment($phone, $amount, $description);
}

/**
 * Airtel Money API Integration (Routes to Paypack)
 */
function initiateAirtelPayment($phone, $amount, $reference, $description) {
    return initiatePaypackPayment($phone, $amount, $description);
}

/**
 * Tigo Cash API Integration (Routes to Paypack)
 */
function initiateTigoPayment($phone, $amount, $reference, $description) {
    return initiatePaypackPayment($phone, $amount, $description);
}

/**
 * Verify payment status
 */
function verifyPayment($transactionId) {
    if (PAYMENT_MODE === 'DEVELOPMENT') {
        // SIMULATION MODE
        // In development, we'll simulate different outcomes
        // You can manually test success/failure scenarios
        
        // Check if there's a session variable for this transaction
        session_start();
        if (isset($_SESSION['payment_sim_' . $transactionId])) {
            return $_SESSION['payment_sim_' . $transactionId];
        }
        
        // Default to pending
        return [
            'success' => true,
            'status' => 'pending',
            'message' => 'Waiting for user confirmation'
        ];
    } else {
        // PRODUCTION MODE
        // Query the payment provider API for transaction status
        require_once __DIR__ . '/payment_helper.php'; // Ensure functions are available
        return verifyPaymentProduction($transactionId);
    }
}

/**
 * Verify payment in production mode
 */
function verifyPaymentProduction($transactionId) {
    // TODO: Implement actual API verification
    // This should query the payment provider's API
    
    return [
        'success' => true,
        'status' => 'pending',
        'message' => 'Checking payment status'
    ];
}

/**
 * Simulate payment confirmation (Development only)
 */
function simulatePaymentConfirmation($transactionId, $status = 'success') {
    if (PAYMENT_MODE !== 'DEVELOPMENT') {
        return false;
    }
    
    session_start();
    $_SESSION['payment_sim_' . $transactionId] = [
        'success' => ($status === 'success'),
        'status' => $status,
        'message' => $status === 'success' ? 'Payment completed successfully' : 'Payment failed',
        'transaction_id' => $transactionId,
        'confirmed_at' => date('Y-m-d H:i:s')
    ];
    
    return true;
}

/**
 * Update payment status in database
 */
function updatePaymentStatus($pdo, $table, $id, $status, $transactionId, $provider, $phone) {
    try {
        $sql = "UPDATE $table SET 
                payment_status = ?,
                transaction_reference = ?,
                payment_provider = ?,
                payment_phone = ?,
                payment_attempted_at = NOW()
                WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$status, $transactionId, $provider, $phone, $id]);
        
        return true;
    } catch (PDOException $e) {
        error_log("Payment update error: " . $e->getMessage());
        return false;
    }
}

/**
 * Format amount for display
 */
function formatAmount($amount) {
    return number_format($amount, 0, '.', ',') . ' RWF';
}

/**
 * Get payment provider logo/icon
 */
function getProviderIcon($provider) {
    $icons = [
        'MTN' => '📱',
        'Airtel' => '📱',
        'Tigo' => '📱',
        'Card' => '💳'
    ];
    
    return $icons[$provider] ?? '💰';
}

/**
 * Get payment provider color
 */
function getProviderColor($provider) {
    $colors = [
        'MTN' => '#FFCC00',
        'Airtel' => '#FF0000',
        'Tigo' => '#0066CC',
        'Card' => '#667eea'
    ];
    
    return $colors[$provider] ?? '#667eea';
}
?>
