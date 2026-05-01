<?php
include_once __DIR__ . '/config.php';
include_once __DIR__ . '/db.php';
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LAEMMA INFO TECH</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <a href="<?php echo BASE_URL; ?>/index.php" class="logo">
                    <img src="<?php echo BASE_URL; ?>/assets/images/logo.png" alt="LIT" style="height: 40px; display: none;" id="logo-img" onerror="this.style.display='none'; document.getElementById('logo-text').style.display='block'">
                    <span id="logo-text">LAEMMA <span>INFO TECH</span></span>
                </a>
                <ul class="nav-links">
                    <li><a href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/index.php#services">Services</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/shop/index.php">Our Products</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/index.php#faqs">FAQs</a></li>
                    <li><a href="<?php echo BASE_URL; ?>/index.php#contact">Contact Us</a></li>
                    
                    <li>
                        <form action="<?php echo BASE_URL; ?>/search.php" method="GET" style="display: flex; align-items: center;">
                            <input type="text" name="q" placeholder="Search..." style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 5px 15px; color: white; width: 150px; font-size: 0.9rem;">
                            <button type="submit" style="background: none; border: none; color: white; margin-left: -30px; cursor: pointer;"><i class="fas fa-search"></i></button>
                        </form>
                    </li>

                    <?php if ($is_admin): ?>
                        <li><a href="<?php echo BASE_URL; ?>/admin/controller.php" style="color: #ff4757; font-weight: bold;"><i class="fas fa-user-shield"></i> CONTROLLER</a></li>
                    <?php endif; ?>

                    <?php if ($is_logged_in): ?>
                        <li><a href="<?php echo BASE_URL; ?>/settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/logout.php" class="btn-login" style="margin-left: 10px;">Logout</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo BASE_URL; ?>/login.php" class="btn-login" style="margin-left: 10px;">Login</a></li>
                    <?php endif; ?>
                </ul>
                <div class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </div>
            </nav>
        </div>
    </header>
