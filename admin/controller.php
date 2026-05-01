<?php include 'admin_header.php'; ?>

<?php
// Fetch basic stats (Mock stats for prototype if tables are empty)
$total_products = 0;
$total_users = 0;
$total_internships = 0;
$total_sales = 0;

try {
    $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $total_internships = $pdo->query("SELECT COUNT(*) FROM internships")->fetchColumn();
    $total_sales = $pdo->query("SELECT SUM(price) FROM orders WHERE payment_status = 'paid'")->fetchColumn() ?: 0;
} catch (Exception $e) {
    // Silence for now if tables don't exist
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
    <div>
        <h1 style="font-weight: 800;">Welcome, <?php echo $_SESSION['name']; ?>!</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem;">Master administrative overview of the LAEMMA platform.</p>
    </div>
    <div style="display: flex; align-items: center; gap: 15px; background: rgba(0, 255, 136, 0.1); padding: 12px 24px; border-radius: 50px; border: 1px solid rgba(0, 255, 136, 0.2); color: #00ff88; font-weight: 600;">
        <i class="fas fa-circle" style="font-size: 0.7rem;"></i> System Online
    </div>
</div>

<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }
    .stat-card {
        background: var(--card-gradient);
        border: 1px solid var(--border);
        padding: 35px 25px;
        border-radius: 24px;
        text-align: center;
        box-shadow: var(--shadow);
        transition: 0.3s;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary);
    }
    .stat-card h4 { color: var(--text-muted); font-size: 0.8rem; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
    .stat-card h2 { font-size: 2.5rem; background: var(--gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 900; }
</style>

<div class="stats-grid">
    <div class="stat-card">
        <h4><i class="fas fa-box" style="margin-right: 10px;"></i> Total Products</h4>
        <h2><?php echo $total_products; ?></h2>
    </div>
    <div class="stat-card">
        <h4><i class="fas fa-users" style="margin-right: 10px;"></i> Total Users</h4>
        <h2><?php echo $total_users; ?></h2>
    </div>
    <div class="stat-card">
        <h4><i class="fas fa-user-graduate" style="margin-right: 10px;"></i> Internship Apps</h4>
        <h2><?php echo $total_internships; ?></h2>
    </div>
    <div class="stat-card">
        <h4><i class="fas fa-coins" style="margin-right: 10px;"></i> Total Sales (RWF)</h4>
        <h2><?php echo number_format($total_sales); ?></h2>
    </div>
</div>

<div style="margin-top: 50px; background: var(--card-gradient); border: 1px solid var(--border); border-radius: 24px; padding: 40px; box-shadow: var(--shadow);">
    <h3 style="font-weight: 800; display: flex; align-items: center; gap: 15px;">
        <i class="fas fa-history" style="color: var(--primary);"></i> Recent Activity
    </h3>
    <div style="color: var(--text-muted); margin-top: 30px; text-align: center; padding: 60px; background: rgba(0,0,0,0.2); border-radius: 20px; border: 1px dashed var(--border);">
        <i class="fas fa-shield-alt" style="font-size: 3rem; opacity: 0.2; margin-bottom: 20px; display: block;"></i>
        No recent activities found in system logs.
    </div>
</div>

</div><!-- end main-content -->
</body>
</html>
