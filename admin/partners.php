<?php include 'admin_header.php'; ?>

<?php
// Handle Add Partner
if (isset($_POST['add_partner'])) {
    $name = $_POST['name'];
    $url = $_POST['url'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    
    // Handle Logo Upload
    $logoPath = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/partners/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = time() . '_' . basename($_FILES['logo']['name']);
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $fileName)) {
            $logoPath = 'uploads/partners/' . $fileName;
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO partners (name, website_url, status, description, logo) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $url, $status, $description, $logoPath]);
    header('Location: partners.php?msg=added');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM partners WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: partners.php?msg=deleted');
    exit;
}

// Handle Status Update
if (isset($_POST['update_status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE partners SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    header('Location: partners.php?msg=updated');
    exit;
}

// Fetch Partners
$partners = $pdo->query("SELECT * FROM partners ORDER BY name ASC")->fetchAll();
?>

<div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Manage <span>Partners</span></h1>
        <p style="color: var(--text-muted);">Track partner website status and details.</p>
    </div>
    <button onclick="document.getElementById('addPartnerModal').style.display='flex'" class="btn-primary" style="border: none; cursor: pointer;">
        <i class="fas fa-plus"></i> Add Partner
    </button>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88;">
        Action completed successfully.
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
    <?php foreach ($partners as $p): ?>
        <div style="background: var(--glass); padding: 25px; border-radius: 20px; border: 1px solid var(--glass-border);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div style="display: flex; gap: 15px; align-items: center;">
                    <div style="width: 50px; height: 50px; background: white; border-radius: 10px; padding: 5px; display: flex; align-items: center; justify-content: center;">
                        <?php if ($p['logo']): ?>
                            <img src="../<?php echo htmlspecialchars($p['logo']); ?>" style="max-width: 100%; max-height: 100%;">
                        <?php else: ?>
                            <i class="fas fa-handshake" style="color: var(--darker); font-size: 1.5rem;"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3 style="font-size: 1.1rem; margin: 0;"><?php echo htmlspecialchars($p['name']); ?></h3>
                        <a href="<?php echo htmlspecialchars($p['website_url']); ?>" target="_blank" style="color: var(--primary); font-size: 0.85rem; text-decoration: none;">Visit Website <i class="fas fa-external-link-alt" style="font-size: 0.7rem;"></i></a>
                    </div>
                </div>
                <a href="?delete=<?php echo $p['id']; ?>" onclick="return confirm('Delete this partner?')" style="color: #ff4757;"><i class="fas fa-trash"></i></a>
            </div>
            
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px; min-height: 40px;">
                <?php echo htmlspecialchars($p['description']); ?>
            </p>
            
            <form method="POST" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 10px;">
                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                <input type="hidden" name="update_status" value="1">
                <label style="display: block; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 5px;">Current Status</label>
                <div style="display: flex; gap: 10px;">
                    <select name="status" style="flex: 1; padding: 8px; background: rgba(0,0,0,0.3); border: 1px solid var(--glass-border); color: white; border-radius: 5px;">
                        <option value="Online" <?php if($p['status']=='Online') echo 'selected'; ?>>Online</option>
                        <option value="Maintenance" <?php if($p['status']=='Maintenance') echo 'selected'; ?>>Maintenance</option>
                        <option value="Development" <?php if($p['status']=='Development') echo 'selected'; ?>>Development</option>
                    </select>
                    <button type="submit" style="background: var(--primary); color: white; border: none; padding: 0 15px; border-radius: 5px; cursor: pointer;">Update</button>
                </div>
            </form>
            
            <div style="margin-top: 15px; text-align: center;">
                <?php 
                $color = '#00ff88';
                if($p['status']=='Maintenance') $color = '#ffa502';
                if($p['status']=='Development') $color = '#ff4757';
                ?>
                <span style="color: <?php echo $color; ?>; font-weight: bold; font-size: 0.9rem;">
                    <i class="fas fa-circle" style="font-size: 0.6rem;"></i> <?php echo $p['status']; ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add Partner Modal -->
<div id="addPartnerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: var(--glass); padding: 30px; border-radius: 20px; width: 500px; border: 1px solid var(--glass-border); position: relative;">
        <button onclick="document.getElementById('addPartnerModal').style.display='none'" style="position: absolute; top: 15px; right: 15px; background: none; border: none; color: white; cursor: pointer; font-size: 1.2rem;"><i class="fas fa-times"></i></button>
        
        <h2 style="margin-bottom: 20px;">Add New Partner</h2>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="add_partner" value="1">
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Partner Name</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); color: white; border-radius: 5px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Website URL</label>
                <input type="url" name="url" required placeholder="https://..." style="width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); color: white; border-radius: 5px;">
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Status</label>
                <select name="status" style="width: 100%; padding: 10px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); color: white; border-radius: 5px;">
                    <option value="Online">Online</option>
                    <option value="Maintenance">Maintenance</option>
                    <option value="Development">Development</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 15px;">
                <label>Description</label>
                <textarea name="description" rows="3" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); color: white; border-radius: 5px;"></textarea>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px;">
                <label>Logo</label>
                <input type="file" name="logo" accept="image/*" style="width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); color: white; border-radius: 5px;">
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%; border: none; padding: 12px; border-radius: 50px; cursor: pointer;">Add Partner</button>
        </form>
    </div>
</div>

</div><!-- end main-content -->
</body>
</html>
