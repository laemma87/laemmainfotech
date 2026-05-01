<?php
/**
 * Paypack Endpoint Prober
 */
require_once 'includes/payment_helper.php';

$baseUrls = [
    "payments_api" => "https://payments.paypack.rw/api",
    "payments_no_api" => "https://payments.paypack.rw",
    "sandbox_api" => "https://sandbox.paypack.rw/api",
    "sandbox_no_api" => "https://sandbox.paypack.rw",
    "base_api" => "https://paypack.rw/api",
    "base_no_api" => "https://paypack.rw"
];

$paths = [
    "auth/agents/authorize",
    "auth/authorize",
    "auth/authenticate",
    "auth/login"
];

$endpoints = [];
foreach ($baseUrls as $name => $base) {
    foreach ($paths as $path) {
        $endpoints["$name: $path"] = "$base/$path";
    }
}

echo "<h2>Paypack Endpoint Probe</h2>";

foreach ($endpoints as $name => $url) {
    echo "<h3>Probing: $name ($url)</h3>";
    
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
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
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    echo "<p>HTTP Code: <strong>$httpCode</strong></p>";
    echo "<p>Response: <code>" . htmlspecialchars($response) . "</code></p>";
    echo "<hr>";
}
?>
