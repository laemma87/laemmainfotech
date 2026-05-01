<?php
include 'includes/db.php';
include 'includes/email_helper.php';

$msg = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    // Check if email exists
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $stmt->execute([$token, $expiry, $user['id']]);
        
        // Send email
        if (sendPasswordResetEmail($email, $token)) {
            $msg = "Password reset link has been sent to your email.";
        } else {
            $error = "Failed to send email. Please try again later.";
        }
    } else {
        // Prepare ambiguous message for security
        $msg = "If an account exists with this email, a reset link will be sent.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | LAEMMA INFO TECH</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-box {
            max-width: 400px;
            margin: 150px auto;
            padding: 40px;
            background: var(--card-gradient);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--text-muted); font-weight: 500; }
        .form-group input { 
            width: 100%; 
            padding: 12px 16px; 
            background: rgba(255, 255, 255, 0.05); 
            border: 1px solid var(--border); 
            border-radius: 12px; 
            color: var(--text-main);
            transition: 0.3s;
        }
        .btn-submit { 
            width: 100%; 
            padding: 14px; 
            background: var(--gradient); 
            border: none; 
            border-radius: 12px; 
            color: white; 
            font-weight: 700; 
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 14px 0 rgba(0, 102, 255, 0.4);
        }
        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 102, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2 style="text-align: center; margin-bottom: 30px; font-weight: 800;">Reset Password</h2>
        <?php if ($msg): ?>
            <p style="color: #2ed573; text-align: center; margin-bottom: 20px; font-weight: 500;"><?php echo $msg; ?></p>
        <?php endif; ?>
        <?php if ($error): ?>
            <p style="color: #ff4757; text-align: center; margin-bottom: 20px; font-weight: 500;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com">
            </div>
            <button type="submit" class="btn-submit">Send Reset Link</button>
        </form>
        
        <p style="text-align: center; margin-top: 25px;">
            <a href="login.php" style="color: var(--text-muted); font-size: 0.9rem;">← Back to Login</a>
        </p>
    </div>
</body>
</html>
