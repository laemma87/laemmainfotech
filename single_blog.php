<?php 
include 'includes/db.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: blog.php');
    exit;
}

// Fetch Blog
$stmt = $pdo->prepare("SELECT b.*, u.name as author_name, c.name as category_name 
                       FROM blogs b 
                       JOIN users u ON b.author_id = u.id 
                       LEFT JOIN blog_categories c ON b.category_id = c.id 
                       WHERE b.slug = ? AND b.status = 'published'");
$stmt->execute([$slug]);
$blog = $stmt->fetch();

if (!$blog) {
    header('Location: blog.php');
    exit;
}

// Increment Views
$stmt = $pdo->prepare("UPDATE blogs SET views = views + 1 WHERE id = ?");
$stmt->execute([$blog['id']]);

// Fetch Recent Posts for Sidebar
$recent = $pdo->query("SELECT title, slug, created_at FROM blogs WHERE status='published' AND id != {$blog['id']} ORDER BY created_at DESC LIMIT 5")->fetchAll();

include 'includes/header.php'; 
?>

<main style="padding-top: 150px; padding-bottom: 80px;">
    <div class="container">
        <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 50px;">
            <!-- Blog Content -->
            <article>
                <div style="margin-bottom: 20px;">
                    <span style="background: rgba(102, 126, 234, 0.1); color: var(--primary); padding: 5px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: 600;">
                        <?php echo htmlspecialchars($blog['category_name'] ?? 'General'); ?>
                    </span>
                    <h1 style="font-size: 2.5rem; margin: 15px 0 20px; line-height: 1.2;"><?php echo htmlspecialchars($blog['title']); ?></h1>
                    
                    <div style="display: flex; gap: 20px; color: var(--text-muted); font-size: 0.9rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 20px; margin-bottom: 30px;">
                        <span><i class="far fa-user"></i> <?php echo htmlspecialchars($blog['author_name']); ?></span>
                        <span><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                        <span><i class="far fa-eye"></i> <?php echo number_format($blog['views']); ?> Views</span>
                    </div>
                </div>

                <?php if ($blog['image']): ?>
                    <div style="margin-bottom: 40px; border-radius: 20px; overflow: hidden; border: 1px solid var(--glass-border);">
                        <img src="<?php echo htmlspecialchars($blog['image']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" style="width: 100%; height: auto; display: block;">
                    </div>
                <?php endif; ?>

                <div class="blog-content" style="font-size: 1.1rem; line-height: 1.8; color: rgba(255,255,255,0.9);">
                    <?php echo nl2br(htmlspecialchars($blog['content'])); ?>
                </div>

                <div style="margin-top: 50px; padding-top: 30px; border-top: 1px solid var(--glass-border);">
                    <a href="blog.php" style="display: inline-flex; align-items: center; color: var(--text-muted); text-decoration: none; font-weight: 600;">
                        <i class="fas fa-arrow-left" style="margin-right: 10px;"></i> Back to Blog
                    </a>
                </div>
            </article>

            <!-- Sidebar -->
            <aside>
                <div style="background: var(--glass); padding: 30px; border-radius: 20px; border: 1px solid var(--glass-border); position: sticky; top: 120px;">
                    <h3 style="margin-bottom: 20px; font-size: 1.2rem;">Recent Posts</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php foreach ($recent as $p): ?>
                            <li style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <a href="single_blog.php?slug=<?php echo $p['slug']; ?>" style="text-decoration: none; color: white; display: block;">
                                    <h4 style="font-size: 1rem; margin-bottom: 5px; font-weight: 500;"><?php echo htmlspecialchars($p['title']); ?></h4>
                                    <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($p['created_at'])); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
