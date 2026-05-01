<?php include 'admin_header.php'; ?>

<?php
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
    $author_id = $_SESSION['user_id'];
    
    // Check if slug exists
    $stmt = $pdo->prepare("SELECT id FROM blogs WHERE slug = ?");
    $stmt->execute([$slug]);
    if ($stmt->fetch()) {
        $slug = $slug . '-' . time();
    }
    
    // Handle Image Upload
    $imagePath = '';
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
            $stmt = $pdo->prepare("INSERT INTO blogs (title, slug, content, author_id, category_id, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $content, $author_id, $category_id, $imagePath, $status]);
            
            header('Location: blogs.php?msg=created');
            exit;
        } catch (PDOException $e) {
            $error = "Error creating blog post: " . $e->getMessage();
        }
    }
}
?>

<div style="margin-bottom: 30px;">
    <h1>Add New <span>Blog Post</span></h1>
    <p style="color: var(--text-muted);">Share news, updates, or articles.</p>
</div>

<?php if ($error): ?>
    <div style="background: rgba(255, 71, 87, 0.1); border: 1px solid #ff4757; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #ff4757;">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" style="background: var(--glass); padding: 40px; border-radius: 20px; border: 1px solid var(--glass-border);">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Left Column -->
        <div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Title</label>
                <input type="text" name="title" required placeholder="Enter blog title" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Content</label>
                <textarea name="content" required placeholder="Write your content here..." style="width: 100%; height: 300px; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white; resize: vertical;"></textarea>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Category</label>
                <select name="category_id" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Featured Image</label>
                <input type="file" name="image" accept="image/*" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 10px; color: var(--text-muted);">Status</label>
                <select name="status" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>

            <button type="submit" class="btn-submit" style="width: 100%; padding: 15px; background: var(--gradient); border: none; border-radius: 10px; color: white; font-weight: bold; cursor: pointer;">
                Save Blog Post
            </button>
            <a href="blogs.php" style="display: block; text-align: center; margin-top: 15px; color: var(--text-muted); text-decoration: none;">Cancel</a>
        </div>
    </div>
</form>

</div><!-- end main-content -->
</body>
</html>
