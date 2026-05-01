<?php
/**
 * SMTP Test Script
 * Use this script to verify your Gmail SMTP configuration
 */
require_once 'includes/email_helper.php';

// Set the recipient email for the test
$testRecipient = 'laemma50@gmail.com'; // You can change this to another email you own

echo "<h2>SMTP Test Started...</h2>";
echo "<p>Sending test email to: <strong>$testRecipient</strong></p>";

$subject = "SMTP Test - LAEMMA INFO TECH";
$message = "
    <div style='font-family: Arial; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
        <h1 style='color: #667eea;'>Success! 🚀</h1>
        <p>If you are reading this, your <strong>Gmail SMTP</strong> is working perfectly on <strong>LAEMMA INFO TECH</strong>.</p>
        <p>Sent at: " . date('Y-m-d H:i:s') . "</p>
    </div>
";

if (sendEmail($testRecipient, $subject, $message)) {
    echo "<h3 style='color: green;'>✅ Success! The test email was sent successfully.</h3>";
    echo "<p>Please check your inbox (and spam folder) for the test email.</p>";
} else {
    echo "<h3 style='color: red;'>❌ Failed! The email could not be sent.</h3>";
    echo "<p><strong>Possible reasons:</strong></p>";
    echo "<ul>
            <li>Invalid App Password (check for typos)</li>
            <li>SMTP Port mismatch (try 465 if 587 fails)</li>
            <li>PHP OpenSSL extension not enabled in XAMPP</li>
          </ul>";
    echo "<p>Check the XAMPP error logs for more details.</p>";
}
?>
