<?php
include 'includes/db.php';

$msg = '';
$error = '';
$token = $_GET['token'] ?? '';
$validToken = false;

if ($token) {
    // Validate token
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $validToken = true;
    } else {
        $error = "Invalid or expired password reset token.";
    }
} else {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
        $stmt->execute([$hashed_password, $user['id']]);
        
        $msg = "Password has been reset successfully. You can now login.";
        $validToken = false; // Hide form
    } else {
        $error = "Passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set New Password | LAEMMA INFO TECH</title>
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
        <h2 style="text-align: center; margin-bottom: 30px; font-weight: 800;">Set New Password</h2>
        
        <?php if ($msg): ?>
            <p style="color: #2ed573; text-align: center; margin-bottom: 20px; font-weight: 500;"><?php echo $msg; ?></p>
            <a href="login.php" class="btn-submit" style="display:block; text-align:center; text-decoration:none;">Login Now</a>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <p style="color: #ff4757; text-align: center; margin-bottom: 20px; font-weight: 500;"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <?php if ($validToken): ?>
        <form method="POST">
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="password" required placeholder="New password">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="Repeat password">
            </div>
            <button type="submit" class="btn-submit">Reset Password</button>
        </form>
        <?php endif; ?>
        
        <?php if (!$validToken && !$msg): ?>
            <p style="text-align: center; margin-top: 25px;">
                <a href="forgot_password.php" style="color: var(--primary);">Request new link</a>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>
