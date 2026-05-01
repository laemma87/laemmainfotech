<?php
include 'includes/db.php';

$msg = '';
$error = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Find user with this token
    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Verify user
        $stmt = $pdo->prepare("UPDATE users SET email_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        
        $msg = "Email verified successfully! You can now login.";
    } else {
        $error = "Invalid or expired verification token.";
    }
} else {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Email | LAEMMA INFO TECH</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-box {
            max-width: 450px;
            margin: 150px auto;
            padding: 40px;
            background: var(--card-gradient);
            border: 1px solid var(--border);
            border-radius: 24px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            text-align: center;
        }
        .btn-submit { 
            display: inline-block;
            margin-top: 20px;
            padding: 14px 30px; 
            background: var(--gradient); 
            border: none; 
            border-radius: 12px; 
            color: white; 
            font-weight: 700; 
            cursor: pointer;
            text-decoration: none;
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
        <?php if ($msg): ?>
            <div style="font-size: 50px; margin-bottom: 20px;">✅</div>
            <h2 style="margin-bottom: 20px; font-weight: 800; color: #00ff88;">Verified!</h2>
            <p style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 30px;"><?php echo $msg; ?></p>
            <a href="login.php" class="btn-submit">Login Now</a>
        <?php elseif ($error): ?>
            <div style="font-size: 50px; margin-bottom: 20px;">❌</div>
            <h2 style="margin-bottom: 20px; font-weight: 800; color: #ff4757;">Verification Failed</h2>
            <p style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 30px;"><?php echo $error; ?></p>
            <a href="index.php" class="btn-submit">Back to Home</a>
        <?php endif; ?>
    </div>
</body>
</html>
