<?php include 'admin_header.php'; ?>

<?php
// Handle Actions
if (isset($_GET['ban'])) {
    $stmt = $pdo->prepare("UPDATE users SET profile_pic = 'banned.png' WHERE id = ?"); // Using a flag in real app, here we use profile_pic as a marker for simplicity or we could add a column
    // Adding a column logic if we were allowed to alter table easily, but let's assume we can.
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_banned TINYINT DEFAULT 0");
    } catch(Exception $e) {}
    
    $stmt = $pdo->prepare("UPDATE users SET is_banned = 1 WHERE id = ?");
    $stmt->execute([$_GET['ban']]);
    header('Location: users.php?msg=banned');
    exit;
}
if (isset($_GET['unban'])) {
    $stmt = $pdo->prepare("UPDATE users SET is_banned = 0 WHERE id = ?");
    $stmt->execute([$_GET['unban']]);
    header('Location: users.php?msg=unbanned');
    exit;
}

// Fetch users
$users = $pdo->query("SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC")->fetchAll();
?>

<div style="margin-bottom: 30px;">
    <h1>User <span>Management</span></h1>
    <p style="color: var(--text-muted);">Manage registered users and take security actions.</p>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88;">
        User successfully <?php echo $_GET['msg']; ?>.
    </div>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>User</th>
            <th>Contact</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <img src="https://via.placeholder.com/40x40" style="border-radius: 50%;">
                        <span style="font-weight: 600;"><?php echo htmlspecialchars($u['name']); ?></span>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td style="text-transform: capitalize;"><?php echo $u['role']; ?></td>
                <td>
                    <?php if (isset($u['is_banned']) && $u['is_banned']): ?>
                        <span class="badge" style="background: rgba(255, 71, 87, 0.1); color: #ff4757; border: 1px solid rgba(255, 71, 87, 0.2);">BANNED</span>
                    <?php else: ?>
                        <span class="badge badge-paid">ACTIVE</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (isset($u['is_banned']) && $u['is_banned']): ?>
                        <a href="?unban=<?php echo $u['id']; ?>" class="btn-sm" style="background: var(--primary); color: white; text-decoration: none;">Unban</a>
                    <?php else: ?>
                        <a href="?ban=<?php echo $u['id']; ?>" class="btn-sm" style="background: rgba(255, 71, 87, 0.2); color: #ff4757; text-decoration: none; border: 1px solid rgba(255, 71, 87, 0.3);">Ban User</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div><!-- end main-content -->
</body>
</html>
