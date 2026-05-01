<?php include 'admin_header.php'; ?>

<?php
// Handle Category Addition
if (isset($_POST['add_category'])) {
    $cat_name = $_POST['cat_name'];
    if ($cat_name) {
        try {
            $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (?)");
            $stmt->execute([$cat_name]);
            $msg = "Category added successfully.";
        } catch (Exception $e) {}
    }
}

// Fetch categories
$categories = [];
try {
    $categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
} catch (PDOException $e) {
    $error = "The 'categories' table is missing. Please run <a href='../setup.php'>setup.php</a> to fix this.";
}
?>

<div style="max-width: 800px;">
    <h1>System <span>Settings</span></h1>
    <p style="color: var(--text-muted); margin-bottom: 40px;">Configure platform globally and manage administrative options.</p>

    <?php if (isset($msg)): ?>
        <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88;">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; gap: 30px;">
        <!-- Category Management -->
        <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 35px; border-radius: 25px;">
            <h3 style="margin-bottom: 25px;"><i class="fas fa-tags" style="color: var(--primary);"></i> Product Categories</h3>
            <form method="POST" style="display: flex; gap: 10px; margin-bottom: 20px;">
                <input type="text" name="cat_name" placeholder="New Category Name" required style="flex: 1; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                <button type="submit" name="add_category" class="btn-submit" style="padding: 10px 20px; border-radius: 10px;">Add</button>
            </form>
            <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                <?php foreach ($categories as $c): ?>
                    <span style="background: rgba(255,255,255,0.05); padding: 5px 15px; border-radius: 20px; border: 1px solid var(--glass-border); font-size: 0.9rem;">
                        <?php echo htmlspecialchars($c['name']); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Company Info -->
        <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 35px; border-radius: 25px;">
            <h3 style="margin-bottom: 25px;"><i class="fas fa-building" style="color: var(--primary);"></i> Global Company Profile</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Platform Name</label>
                    <input type="text" value="LAEMMA INFO TECH" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
                <div class="form-group">
                    <label>Inquiry Email</label>
                    <input type="email" value="laemma50@gmail.com" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>
            </div>
        </div>

        <!-- System Maintenance -->
        <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 35px; border-radius: 25px;">
            <h3 style="margin-bottom: 25px;"><i class="fas fa-tools" style="color: #ffa502;"></i> Maintenance & Security</h3>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: rgba(255,255,255,0.02); border-radius: 15px; margin-bottom: 15px;">
                <div>
                    <h5 style="margin-bottom: 5px;">Public Registration</h5>
                    <p style="font-size: 0.8rem; color: var(--text-muted);">Allow new users to sign up.</p>
                </div>
                <div style="width: 50px; height: 26px; background: var(--primary); border-radius: 20px; position: relative;">
                    <div style="width: 20px; height: 20px; background: white; border-radius: 50%; position: absolute; right: 3px; top: 3px;"></div>
                </div>
            </div>
        </div>
    </div>
    
    <button class="btn-submit" style="margin-top: 40px; width: 100%; padding: 15px; border-radius: 50px;">Save All Changes</button>
</div>

</div><!-- end main-content -->
</body>
</html>
