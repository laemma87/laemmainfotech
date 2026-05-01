<?php include 'admin_header.php'; ?>

<?php
if (!isset($_GET['id'])) {
    header('Location: blogs.php');
    exit;
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$id]);
$blog = $stmt->fetch();

if (!$blog) {
    header('Location: blogs.php');
    exit;
}

// Fetch Categories
$categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name ASC")->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $category_id = $_POST['category_id'];
    $content = $_POST['content'];
    $status = $_POST['status'];
    
    // Check if slug exists for OTHER posts
    $stmt = $pdo->prepare("SELECT id FROM blogs WHERE slug = ? AND id != ?");
    $stmt->execute([$slug, $id]);
    if ($stmt->fetch()) {
        $slug = $slug . '-' . time();
    }
    
    // Handle Image Upload
    $imagePath = $blog['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../uploads/blog_images/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $imagePath = 'uploads/blog_images/' . $fileName;
        } else {
            $error = "Failed to upload image.";
        }
    }
    
    if (!$error) {
        try {
            $stmt = $pdo->prepare("UPDATE blogs SET title = ?, slug = ?, content = ?, category_id = ?, image = ?, status = ? WHERE id = ?");
            $stmt->execute([$title, $slug, $content, $category_id, $imagePath, $status, $id]);
            
            // Refresh Data
            $stmt = $pdo->prepare("SELECT * FROM blogs WHERE id = ?");
            $stmt->execute([$id]);
            $blog = $stmt->fetch();
            
            $success = "Blog post updated successfully.";
        } catch (PDOException $e) {
            $error = "Error updating blog post: " . $e->getMessage();
        }
    }
}
?>

<div style="margin-bottom: 30px;">
    <h1>Edit <span>Blog Post</span></h1>
    <p style="color: var(--text-muted);">Update existing content.</p>
</div>

<?php if ($error): ?>
    <div style="background: rgba(255, 71, 87, 0.1); border: 1px solid #ff4757; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #ff4757;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88;">
        <?php echo $success; ?> <a href="blogs.php" style="color: #00ff88; font-weight: bold;">Back to List</a>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" style="background: var(--glass); padding: 40px; border-radius: 20px; border: 1px solid var(--glass-border);">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Left Column -->
        <div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Title</label>
                <input type="text" name="title" required value="<?php echo htmlspecialchars($blog['title']); ?>" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Content</label>
                <textarea name="content" required style="width: 100%; height: 300px; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; resize: vertical;"><?php echo htmlspecialchars($blog['content']); ?></textarea>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Category</label>
                <select name="category_id" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php if($c['id'] == $blog['category_id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Featured Image</label>
                <?php if ($blog['image']): ?>
                    <img src="../<?php echo htmlspecialchars($blog['image']); ?>" style="width: 100%; height: 150px; object-fit: cover; border-radius: 10px; margin-bottom: 10px;">
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                <small style="color: var(--text-muted);">Leave empty to keep current image</small>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Status</label>
                <select name="status" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    <option value="draft" <?php if($blog['status'] == 'draft') echo 'selected'; ?>>Draft</option>
                    <option value="published" <?php if($blog['status'] == 'published') echo 'selected'; ?>>Published</option>
                </select>
            </div>

            <button type="submit" class="btn-submit" style="width: 100%; padding: 15px; background: var(--gradient); border: none; border-radius: 10px; color: white; font-weight: bold; cursor: pointer;">
                Update Blog Post
            </button>
            <a href="blogs.php" style="display: block; text-align: center; margin-top: 15px; color: var(--text-muted); text-decoration: none;">Cancel</a>
        </div>
    </div>
</form>

</div><!-- end main-content -->
</body>
</html>
