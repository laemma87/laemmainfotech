/**
 * Email Notification Helper
 * Handles all email notifications for the LAEMMA INFO TECH platform
 */
require_once __DIR__ . '/config.php';

// Email configuration
define('SMTP_ENABLED', true); // Set to true when SMTP is configured
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 465);
define('SMTP_USERNAME', 'laemma50@gmail.com');
define('SMTP_PASSWORD', 'aciqovdtttccawyb');
define('SMTP_FROM_EMAIL', 'noreply@laemmainfotech.com');
define('SMTP_FROM_NAME', 'LAEMMA INFO TECH');

/**
 * Send email using SMTP or local mail()
 */
function sendEmail($to, $subject, $htmlBody) {
    // For development: log emails instead of sending
    if (!SMTP_ENABLED) {
        $logFile = __DIR__ . '/../email_logs.txt';
        $logEntry = "\n" . str_repeat("=", 80) . "\n";
        $logEntry .= "Date: " . date('Y-m-d H:i:s') . "\n";
        $logEntry .= "To: $to\n";
        $logEntry .= "Subject: $subject\n";
        $logEntry .= "Body:\n$htmlBody\n";
        $logEntry .= str_repeat("=", 80) . "\n";
        
        file_put_contents($logFile, $logEntry, FILE_APPEND);
        return true; // Simulate success
    }

    try {
        require_once __DIR__ . '/SMTPClient.php';
        $smtp = new SMTPClient();
        $smtp->setServer(SMTP_HOST, SMTP_PORT, 'ssl'); // Using SSL for Gmail
        $smtp->setSender(SMTP_FROM_EMAIL, SMTP_USERNAME, SMTP_PASSWORD);
        $smtp->setMail($to, $subject, $htmlBody, 'text/html', 'UTF-8');
        return $smtp->sendMail();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Get email template wrapper
 */
function getEmailTemplate($content) {
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            body {
                font-family: "Segoe UI", Arial, sans-serif;
                background-color: #0a0e27;
                margin: 0;
                padding: 0;
            }
            .email-container {
                max-width: 600px;
                margin: 40px auto;
                background: linear-gradient(135deg, #1a1f3a 0%, #0a0e27 100%);
                border-radius: 20px;
                overflow: hidden;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }
            .email-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                padding: 30px;
                text-align: center;
            }
            .email-header h1 {
                color: white;
                margin: 0;
                font-size: 24px;
            }
            .email-body {
                padding: 40px 30px;
                color: #e0e0e0;
                line-height: 1.6;
            }
            .email-body h2 {
                color: #667eea;
                margin-top: 0;
            }
            .status-badge {
                display: inline-block;
                padding: 10px 20px;
                border-radius: 50px;
                font-weight: bold;
                margin: 20px 0;
            }
            .status-success {
                background: rgba(0, 255, 136, 0.2);
                color: #00ff88;
                border: 1px solid #00ff88;
            }
            .status-warning {
                background: rgba(255, 165, 2, 0.2);
                color: #ffa502;
                border: 1px solid #ffa502;
            }
            .status-danger {
                background: rgba(255, 71, 87, 0.2);
                color: #ff4757;
                border: 1px solid #ff4757;
            }
            .btn {
                display: inline-block;
                padding: 15px 30px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                text-decoration: none;
                border-radius: 50px;
                margin: 20px 0;
                font-weight: bold;
            }
            .email-footer {
                background: rgba(255, 255, 255, 0.02);
                padding: 20px;
                text-align: center;
                color: #888;
                font-size: 12px;
                border-top: 1px solid rgba(255, 255, 255, 0.05);
            }
            .info-box {
                background: rgba(255, 255, 255, 0.05);
                border-left: 4px solid #667eea;
                padding: 15px;
                margin: 20px 0;
                border-radius: 5px;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                <h1>🚀 LAEMMA INFO TECH</h1>
            </div>
            <div class="email-body">
                ' . $content . '
            </div>
            <div class="email-footer">
                <p>© 2026 LAEMMA INFO TECH. All rights reserved.</p>
                <p>Kigali, Rwanda | Email: info@laemmainfotech.com</p>
                <p style="margin-top: 10px; font-size: 11px;">
                    This is an automated message. Please do not reply to this email.
                </p>
            </div>
        </div>
    </body>
    </html>
    ';
}

/**
 * Send application confirmation email
 */
function sendApplicationConfirmation($email, $name, $field) {
    $subject = "Application Received - LAEMMA INFO TECH Internship";
    
    $content = "
        <h2>Hello $name! 👋</h2>
        <p>Thank you for applying to the <strong>$field</strong> internship program at LAEMMA INFO TECH.</p>
        
        <div class='status-badge status-warning'>
            ⏳ Application Under Review
        </div>
        
        <div class='info-box'>
            <strong>What happens next?</strong>
            <ul>
                <li>Our team will review your application within 2-3 business days</li>
                <li>You will receive an email notification once a decision is made</li>
                <li>You can check your application status anytime on your dashboard</li>
            </ul>
        </div>
        
        <p>If you have any questions, feel free to contact our support team.</p>
        
        <a href='<?php echo BASE_URL; ?>/internship/dashboard.php' class='btn'>
            View Application Status
        </a>
        
        <p style='margin-top: 30px; color: #888;'>
            Best regards,<br>
            <strong>LAEMMA INFO TECH Team</strong>
        </p>
    ";
    
    return sendEmail($email, $subject, getEmailTemplate($content));
}

/**
 * Send status update notification
 */
function sendStatusUpdate($email, $name, $status, $notes = '') {
    $statusUpper = strtoupper($status);
    $subject = "Application Update - $statusUpper";
    
    if ($status === 'accepted') {
        $badgeClass = 'status-success';
        $icon = '✅';
        $message = "
            <h2>Congratulations $name! 🎉</h2>
            <p>We are pleased to inform you that your internship application has been <strong>ACCEPTED</strong>!</p>
            
            <div class='status-badge $badgeClass'>
                $icon Application Accepted
            </div>
            
            <div class='info-box'>
                <strong>Next Steps:</strong>
                <ul>
                    <li>Complete your payment to confirm your spot (20,000 RWF)</li>
                    <li>Check your email for the onboarding link and interview schedule</li>
                    <li>Download the curriculum and onboarding materials from your dashboard</li>
                    <li>Prepare your laptop and required software</li>
                </ul>
            </div>
            
            <p>We look forward to having you on our team!</p>
        ";
    } elseif ($status === 'rejected') {
        $badgeClass = 'status-danger';
        $icon = '❌';
        $message = "
            <h2>Hello $name,</h2>
            <p>Thank you for your interest in the LAEMMA INFO TECH internship program.</p>
            
            <div class='status-badge $badgeClass'>
                $icon Application Not Successful
            </div>
            
            <p>Unfortunately, we are unable to accept your application at this time.</p>
        ";
        
        if (!empty($notes)) {
            $message .= "
                <div class='info-box'>
                    <strong>Feedback:</strong>
                    <p>$notes</p>
                </div>
            ";
        }
        
        $message .= "
            <p>We encourage you to apply again in the future. Keep developing your skills!</p>
            
            <div class='info-box'>
                <strong>Recommendations:</strong>
                <ul>
                    <li>Continue building your technical skills</li>
                    <li>Work on personal projects to strengthen your portfolio</li>
                    <li>Consider online courses and certifications</li>
                    <li>Apply again in the next intake period</li>
                </ul>
            </div>
        ";
    } else {
        $badgeClass = 'status-warning';
        $icon = '⏳';
        $message = "
            <h2>Hello $name,</h2>
            <p>Your application status has been updated to: <strong>$statusUpper</strong></p>
            
            <div class='status-badge $badgeClass'>
                $icon $statusUpper
            </div>
        ";
    }
    
    $message .= "
        <a href='<?php echo BASE_URL; ?>/internship/dashboard.php' class='btn'>
            View Dashboard
        </a>
        
        <p style='margin-top: 30px; color: #888;'>
            Best regards,<br>
            <strong>LAEMMA INFO TECH Team</strong>
        </p>
    ";
    
    return sendEmail($email, $subject, getEmailTemplate($message));
}

/**
 * Send payment confirmation email
 */
function sendPaymentConfirmation($email, $name, $amount, $reference, $provider = 'Mobile Money') {
    $subject = "Payment Confirmed - LAEMMA INFO TECH";
    
    $content = "
        <h2>Hello $name! 👋</h2>
        <p>Your payment has been successfully processed.</p>
        
        <div class='status-badge status-success'>
            ✅ Payment Confirmed
        </div>
        
        <div class='info-box'>
            <strong>Payment Details:</strong>
            <ul>
                <li><strong>Amount:</strong> " . number_format($amount) . " RWF</li>
                <li><strong>Payment Method:</strong> $provider</li>
                <li><strong>Transaction Reference:</strong> $reference</li>
                <li><strong>Date:</strong> " . date('F j, Y - g:i A') . "</li>
            </ul>
        </div>
        
        <p>Your payment has been recorded and your application is now fully processed.</p>
        
        <a href='<?php echo BASE_URL; ?>/internship/dashboard.php' class='btn'>
            Access Your Dashboard
        </a>
        
        <p style='margin-top: 30px; color: #888;'>
            Best regards,<br>
            <strong>LAEMMA INFO TECH Team</strong>
        </p>
    ";
    
    return sendEmail($email, $subject, getEmailTemplate($content));
}

/**
 * Send payment reminder email
 */
function sendPaymentReminder($email, $name, $amount) {
    $subject = "Payment Pending - Complete Your Registration";
    
    $content = "
        <h2>Hello $name! 👋</h2>
        <p>Your internship application has been <strong>ACCEPTED</strong>, but we haven't received your payment yet.</p>
        
        <div class='status-badge status-warning'>
            ⏳ Payment Pending
        </div>
        
        <div class='info-box'>
            <strong>Payment Information:</strong>
            <ul>
                <li><strong>Amount Due:</strong> " . number_format($amount) . " RWF</li>
                <li><strong>Payment Methods:</strong> MTN Mobile Money, Tigo Cash, Airtel Money</li>
            </ul>
        </div>
        
        <p>Please complete your payment to secure your spot in the program.</p>
        
        <a href='<?php echo BASE_URL; ?>/internship/payment.php?type=internship' class='btn'>
            Complete Payment Now
        </a>
        
        <p style='margin-top: 30px; color: #888;'>
            Best regards,<br>
            <strong>LAEMMA INFO TECH Team</strong>
        </p>
    ";
    
    return sendEmail($email, $subject, getEmailTemplate($content));
}
/**
 * Send email verification link
 */
function sendVerificationEmail($email, $name, $token) {
    $subject = "Verify Your Email - LAEMMA INFO TECH";
    $verificationLink = BASE_URL . "/verify.php?token=$token";
    
    $content = "
        <h2>Welcome $name! 👋</h2>
        <p>Thank you for creating an account with LAEMMA INFO TECH. To complete your registration, please verify your email address.</p>
        
        <div class='info-box'>
            <p>Click the button below to activate your account:</p>
        </div>
        
        <a href='$verificationLink' class='btn'>
            Verify Email Address
        </a>
        
        <p>Or copy and paste this link into your browser:</p>
        <p style='background: rgba(255,255,255,0.05); padding: 10px; border-radius: 5px; font-family: monospace; word-break: break-all;'>
            $verificationLink
        </p>
        
        <p style='margin-top: 30px; color: #888;'>
            If you did not create this account, please ignore this email.
        </p>
    ";
    
    return sendEmail($email, $subject, getEmailTemplate($content));
}

/**
 * Send password reset link
 */
function sendPasswordResetEmail($email, $token) {
    $subject = "Reset Your Password - LAEMMA INFO TECH";
    $resetLink = BASE_URL . "/reset_password.php?token=$token";
    
    $content = "
        <h2>Password Reset Request</h2>
        <p>We received a request to reset your password. If you didn't make this request, you can safely ignore this email.</p>
        
        <a href='$resetLink' class='btn'>
            Reset Password
        </a>
        
        <p>This link will expire in 1 hour.</p>
        
        <p style='margin-top: 30px; color: #888;'>
            Best regards,<br>
            <strong>LAEMMA INFO TECH Team</strong>
        </p>
    ";
    
    return sendEmail($email, $subject, getEmailTemplate($content));
}
?>
