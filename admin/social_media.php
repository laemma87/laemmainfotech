<?php include 'admin_header.php'; ?>

<?php
// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['links'] as $id => $url) {
        $stmt = $pdo->prepare("UPDATE social_media SET url = ? WHERE id = ?");
        $stmt->execute([$url, $id]);
    }
    header('Location: social_media.php?msg=updated');
    exit;
}

// Fetch Social Links
try {
    $links = $pdo->query("SELECT * FROM social_media")->fetchAll();
} catch (PDOException $e) {
    $links = []; // Handle case if table missing
}
?>

<div style="margin-bottom: 30px;">
    <h1>Social Media <span>Settings</span></h1>
    <p style="color: var(--text-muted);">Manage your social media profile links.</p>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88;">
        Settings updated successfully.
    </div>
<?php endif; ?>

<div style="background: var(--glass); padding: 40px; border-radius: 30px; border: 1px solid var(--glass-border); max-width: 600px;">
    <form method="POST">
        <?php foreach ($links as $link): ?>
            <div class="form-group" style="margin-bottom: 25px;">
                <label style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; text-transform: capitalize;">
                    <i class="fab fa-<?php echo htmlspecialchars($link['platform']); ?>" style="font-size: 1.2rem; color: var(--primary);"></i> 
                    <?php echo htmlspecialchars($link['platform']); ?>
                </label>
                <input type="url" name="links[<?php echo $link['id']; ?>]" value="<?php echo htmlspecialchars($link['url']); ?>" 
                       style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
            </div>
        <?php endforeach; ?>
        
        <?php if (empty($links)): ?>
            <p style="color: var(--text-muted); margin-bottom: 20px;">No social media links found in database. Run database update script.</p>
        <?php else: ?>
            <button type="submit" class="btn-submit" style="width: 100%; padding: 15px; border-radius: 50px;">Save Changes</button>
        <?php endif; ?>
    </form>
</div>

</div><!-- end main-content -->
</body>
</html>
