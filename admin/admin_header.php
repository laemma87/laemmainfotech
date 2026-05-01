<?php
include __DIR__ . '/../includes/db.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | LAEMMA</title>
    <link rel="stylesheet" href="/laemmainfotech/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { display: flex; min-height: 100vh; background: var(--background); color: var(--text-main); }
        .sidebar {
            width: 280px;
            background: rgba(15, 11, 48, 0.95);
            border-right: 1px solid var(--border);
            padding: 30px 20px;
            position: fixed;
            height: 100vh;
            backdrop-filter: blur(20px);
            z-index: 100;
        }
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 40px;
            background-image: radial-gradient(circle at 100% 0%, rgba(112, 0, 255, 0.05), transparent);
        }
        .sidebar-brand { font-weight: 800; font-size: 1.2rem; margin-bottom: 40px; display: block; color: var(--text-main); }
        .nav-menu li { margin-bottom: 8px; }
        .nav-menu a {
            display: flex; align-items: center; gap: 15px; padding: 12px 20px; border-radius: 12px;
            color: var(--text-muted); font-weight: 500; transition: 0.3s;
        }
        .nav-menu a.active, .nav-menu a:hover {
            background: rgba(255, 255, 255, 0.05); color: var(--primary); 
            box-shadow: inset 4px 0 0 var(--primary);
        }
        .admin-table {
            width: 100%; border-collapse: collapse; margin-top: 30px; background: var(--card-gradient);
            border: 1px solid var(--border); border-radius: 24px; overflow: hidden;
            box-shadow: var(--shadow);
        }
        .admin-table th, .admin-table td { padding: 18px 24px; text-align: left; border-bottom: 1px solid var(--border); }
        .admin-table th { background: rgba(0,0,0,0.2); color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
        .badge { padding: 6px 14px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .badge-pending { background: rgba(255, 165, 2, 0.1); color: #ffa502; border: 1px solid rgba(255, 165, 2, 0.2); }
        .badge-paid { background: rgba(0, 255, 136, 0.1); color: #00ff88; border: 1px solid rgba(0, 255, 136, 0.2); }
        .btn-sm { padding: 8px 16px; border-radius: 10px; font-size: 0.8rem; cursor: pointer; border: none; font-weight: 600; transition: 0.3s; }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="/laemmainfotech/index.php" class="sidebar-brand">LAEMMA <span style="color: var(--primary);">CONTROLLER</span></a>
        <ul class="nav-menu">
            <li><a href="controller.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="products.php"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="transactions.php"><i class="fas fa-money-bill-wave"></i> Transactions</a></li>
            <li><a href="internships.php"><i class="fas fa-user-graduate"></i> Internships</a></li>
            <li><a href="blogs.php"><i class="fas fa-newspaper"></i> Blogs</a></li>
            <li><a href="partners.php"><i class="fas fa-handshake"></i> Partners</a></li>
            <li><a href="messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="social_media.php"><i class="fas fa-share-alt"></i> Social Media</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li style="margin-top: 50px;"><a href="/laemmainfotech/logout.php" style="color: #ff4757;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="main-content">
