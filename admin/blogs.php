<?php include 'admin_header.php'; ?>

<?php
// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: blogs.php?msg=deleted');
    exit;
}

// Fetch Blogs
$stmt = $pdo->query("SELECT b.*, u.name as author_name, c.name as category_name 
                     FROM blogs b 
                     JOIN users u ON b.author_id = u.id 
                     LEFT JOIN blog_categories c ON b.category_id = c.id 
                     ORDER BY b.created_at DESC");
$blogs = $stmt->fetchAll();
?>

<div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1>Manage <span>Blogs</span></h1>
        <p style="color: var(--text-muted);">Create and manage news and updates.</p>
    </div>
    <a href="add_blog.php" class="btn-submit" style="text-decoration: none; padding: 12px 25px; display: inline-block;">
        <i class="fas fa-plus"></i> Add New Post
    </a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88;">
        Action completed successfully.
    </div>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Title</th>
            <th>Category</th>
            <th>Author</th>
            <th>Status</th>
            <th>Views</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($blogs as $b): ?>
            <tr>
                <td>
                    <?php if ($b['image']): ?>
                        <img src="../<?php echo htmlspecialchars($b['image']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                    <?php else: ?>
                        <div style="width: 50px; height: 50px; background: rgba(255,255,255,0.1); border-radius: 5px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image"></i>
                        </div>
                    <?php endif; ?>
                </td>
                <td>
                    <div style="font-weight: 600; max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        <?php echo htmlspecialchars($b['title']); ?>
                    </div>
                </td>
                <td>
                    <span style="font-size: 0.85rem; padding: 3px 8px; background: rgba(102, 126, 234, 0.2); border-radius: 5px; color: #667eea;">
                        <?php echo htmlspecialchars($b['category_name'] ?? 'Uncategorized'); ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($b['author_name']); ?></td>
                <td>
                    <span class="badge <?php echo ($b['status'] === 'published') ? 'badge-paid' : 'badge-pending'; ?>">
                        <?php echo ucfirst($b['status']); ?>
                    </span>
                </td>
                <td><?php echo number_format($b['views']); ?></td>
                <td><?php echo date('M d, Y', strtotime($b['created_at'])); ?></td>
                <td>
                    <a href="edit_blog.php?id=<?php echo $b['id']; ?>" class="btn-sm" style="background: #667eea; color: white; text-decoration: none; margin-right: 5px;"><i class="fas fa-edit"></i></a>
                    <a href="?delete=<?php echo $b['id']; ?>" class="btn-sm" style="background: #ff4757; color: white; text-decoration: none;" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                    <a href="../single_blog.php?slug=<?php echo $b['slug']; ?>" target="_blank" class="btn-sm" style="background: rgba(255,255,255,0.1); color: white; text-decoration: none; margin-left: 5px;"><i class="fas fa-eye"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($blogs)): ?>
            <tr>
                <td colspan="8" style="text-align: center; padding: 30px; color: var(--text-muted);">No blog posts found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div><!-- end main-content -->
</body>
</html>
