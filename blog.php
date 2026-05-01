<?php 
include 'includes/db.php';
include 'includes/header.php'; 

// Fetch Blogs
$category_id = $_GET['category'] ?? null;
$where = "WHERE b.status = 'published'";
$params = [];

if ($category_id) {
    $where .= " AND b.category_id = ?";
    $params[] = $category_id;
}

$stmt = $pdo->prepare("SELECT b.*, u.name as author_name, c.name as category_name 
                       FROM blogs b 
                       JOIN users u ON b.author_id = u.id 
                       LEFT JOIN blog_categories c ON b.category_id = c.id 
                       $where 
                       ORDER BY b.created_at DESC");
$stmt->execute($params);
$blogs = $stmt->fetchAll();

// Fetch Categories for Sidebar
$categories = $pdo->query("SELECT * FROM blog_categories ORDER BY name ASC")->fetchAll();
?>

<main style="padding-top: 150px; padding-bottom: 80px;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 60px;">
            <h1 class="section-title">Latest <span>News & Insights</span></h1>
            <p style="color: var(--text-muted); max-width: 600px; margin: 20px auto;">Stay updated with the latest technology trends, company news, and expert tutorials.</p>
        </div>

        <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 50px;">
            <!-- Blog Grid -->
            <div>
                <?php if (count($blogs) > 0): ?>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
                        <?php foreach ($blogs as $b): ?>
                            <article style="background: var(--glass); border: 1px solid var(--glass-border); border-radius: 20px; overflow: hidden; transition: transform 0.3s;">
                                <a href="single_blog.php?slug=<?php echo $b['slug']; ?>" style="text-decoration: none; color: inherit;">
                                    <div style="height: 200px; overflow: hidden;">
                                        <?php if ($b['image']): ?>
                                            <img src="<?php echo htmlspecialchars($b['image']); ?>" alt="<?php echo htmlspecialchars($b['title']); ?>" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s;">
                                        <?php else: ?>
                                            <div style="width: 100%; height: 100%; background: var(--glass-border); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-image" style="font-size: 3rem; color: var(--text-muted);"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div style="padding: 25px;">
                                        <div style="display: flex; gap: 15px; font-size: 0.8rem; color: var(--text-muted); margin-bottom: 15px;">
                                            <span><i class="far fa-calendar"></i> <?php echo date('M d, Y', strtotime($b['created_at'])); ?></span>
                                            <span><i class="far fa-folder"></i> <?php echo htmlspecialchars($b['category_name'] ?? 'General'); ?></span>
                                        </div>
                                        <h3 style="font-size: 1.2rem; margin-bottom: 15px; line-height: 1.4;"><?php echo htmlspecialchars($b['title']); ?></h3>
                                        <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.6; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?php echo htmlspecialchars(substr(strip_tags($b['content']), 0, 150)) . '...'; ?>
                                        </p>
                                        <span style="display: inline-block; margin-top: 20px; color: var(--primary); font-size: 0.9rem; font-weight: 600;">Read More <i class="fas fa-arrow-right"></i></span>
                                    </div>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 50px; background: var(--glass); border-radius: 20px; border: 1px solid var(--glass-border);">
                        <i class="far fa-newspaper" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 20px;"></i>
                        <h3>No posts found</h3>
                        <p style="color: var(--text-muted);">Check back later for updates.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside>
                <!-- Categories -->
                <div style="background: var(--glass); padding: 30px; border-radius: 20px; border: 1px solid var(--glass-border); margin-bottom: 30px;">
                    <h3 style="margin-bottom: 20px; font-size: 1.2rem;">Categories</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li>
                            <a href="blog.php" style="display: block; padding: 10px 0; color: <?php echo !$category_id ? 'var(--primary)' : 'var(--text-muted)'; ?>; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                All Posts
                            </a>
                        </li>
                        <?php foreach ($categories as $c): ?>
                            <li>
                                <a href="blog.php?category=<?php echo $c['id']; ?>" style="display: block; padding: 10px 0; color: <?php echo $category_id == $c['id'] ? 'var(--primary)' : 'var(--text-muted)'; ?>; text-decoration: none; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <?php echo htmlspecialchars($c['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Newsletter (Placeholder) -->
                <div style="background: var(--gradient); padding: 30px; border-radius: 20px; color: white;">
                    <h3 style="margin-bottom: 15px; font-size: 1.2rem;">Subscribe</h3>
                    <p style="font-size: 0.9rem; margin-bottom: 20px; opacity: 0.9;">Get the latest updates directly in your inbox.</p>
                    <form onsubmit="event.preventDefault(); alert('Subscribed!');">
                        <input type="email" placeholder="Your email address" style="width: 100%; padding: 12px; border: none; border-radius: 5px; margin-bottom: 10px; background: rgba(255,255,255,0.9);">
                        <button type="submit" style="width: 100%; padding: 12px; background: var(--darker); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Subscribe</button>
                    </form>
                </div>
            </aside>
        </div>
    </div>
</main>

<style>
article:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
article:hover img {
    transform: scale(1.1);
}
</style>

<?php include 'includes/footer.php'; ?>
