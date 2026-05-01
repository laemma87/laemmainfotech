<?php
include 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        include 'includes/email_helper.php';
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $verification_token = bin2hex(random_bytes(32));
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, verification_token, email_verified) VALUES (?, ?, ?, ?, 0)");
            $stmt->execute([$name, $email, $hashed_password, $verification_token]);
            
            // Send verification email
            if (sendVerificationEmail($email, $name, $verification_token)) {
                $success = "Account created successfully! Please check your email to verify your account before logging in.";
            } else {
                $success = "Account created, but failed to send verification email. Please contact support.";
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $error = "Email already registered.";
            } else {
                $error = "An error occurred. Please try again later.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up | LAEMMA INFO TECH</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-box {
            max-width: 450px;
            margin: 100px auto;
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
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(255, 255, 255, 0.1);
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
        <h2 style="text-align: center; margin-bottom: 30px; font-weight: 800;">Create Account</h2>
        <?php if ($error): ?>
            <p style="color: #ff4757; text-align: center; margin-bottom: 20px; font-weight: 500;"><?php echo $error; ?></p>
        <?php endif; ?>
        <?php if ($success): ?>
            <p style="color: #2ed573; text-align: center; margin-bottom: 20px; font-weight: 500;"><?php echo $success; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required placeholder="John Doe">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Create password">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="Repeat password">
            </div>
            <button type="submit" class="btn-submit">Sign Up</button>
        </form>
        <p style="text-align: center; margin-top: 25px; color: var(--text-muted);">
            Already have an account? <a href="login.php" style="color: var(--primary); font-weight: 600;">Login</a>
        </p>
        <p style="text-align: center; margin-top: 15px;">
            <a href="index.php" style="color: var(--text-muted); font-size: 0.9rem;">← Back to Home</a>
        </p>
    </div>
</body>
</html>
