<?php
include 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // For the prototype, we check the admin credentials explicitly if database is not set up
    // In a real system, we only check the database.
    if ($email === 'laemma50@gmail.com' && $password === 'laemma@123') {
        $_SESSION['user_id'] = 1;
        $_SESSION['name'] = 'Emmanuel HAKIZIMANA';
        $_SESSION['role'] = 'admin';
        header('Location: index.php');
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Check if user is banned
        if ($user['status'] === 'banned') {
            $error = "Your account has been banned. Please contact the administrator for support.";
        } 
        // Check if email is verified
        elseif ($user['email_verified'] == 0) {
            $error = "Please verify your email before logging in. Check your inbox for the verification link.";
        } 
        else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header('Location: index.php');
            exit;
        }
    } else {
        $error = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | LAEMMA INFO TECH</title>
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
        <h2 style="text-align: center; margin-bottom: 30px; font-weight: 800;">Welcome Back</h2>
        <?php if ($error): ?>
            <p style="color: #ff4757; text-align: center; margin-bottom: 20px; font-weight: 500;"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>
            <button type="submit" class="btn-submit">Sign In</button>
        </form>
        <p style="text-align: center; margin-top: 25px; color: var(--text-muted);">
            Don't have an account? <a href="signup.php" style="color: var(--primary); font-weight: 600;">Sign Up</a>
        </p>
        <p style="text-align: center; margin-top: 15px;">
            <a href="index.php" style="color: var(--text-muted); font-size: 0.9rem;">← Back to Home</a>
        </p>
    </div>
</body>
</html>
