<?php 
include 'includes/db.php';
include 'includes/header.php'; 

$q = $_GET['q'] ?? '';
$results = [];
$count = 0;

if ($q) {
    $search_term = "%$q%";
    
    // Search Blogs
    $stmt = $pdo->prepare("SELECT id, title, slug, content, 'blog' as type FROM blogs WHERE status='published' AND (title LIKE ? OR content LIKE ?)");
    $stmt->execute([$search_term, $search_term]);
    $blog_results = $stmt->fetchAll();
    
    // Search Products
    $stmt = $pdo->prepare("SELECT id, name as title, id as slug, description as content, 'product' as type FROM products WHERE name LIKE ? OR description LIKE ?");
    $stmt->execute([$search_term, $search_term]);
    $product_results = $stmt->fetchAll();
    
    // Search Partners
    $stmt = $pdo->prepare("SELECT id, name as title, website_url as slug, description as content, 'partner' as type FROM partners WHERE name LIKE ? OR description LIKE ?");
    $stmt->execute([$search_term, $search_term]);
    $partner_results = $stmt->fetchAll();

    // Merge Results
    $results = array_merge($blog_results, $product_results, $partner_results);
    $count = count($results);
}
?>

<main style="padding-top: 150px; padding-bottom: 80px;">
    <div class="container" style="max-width: 800px;">
        <div style="text-align: center; margin-bottom: 50px;">
            <h1>Search <span>Results</span></h1>
            <p style="color: var(--text-muted); margin-bottom: 30px;">
                <?php if ($q): ?>
                    Found <?php echo $count; ?> results for "<strong><?php echo htmlspecialchars($q); ?></strong>"
                <?php else: ?>
                    Enter a keyword to search across our site.
                <?php endif; ?>
            </p>
            
            <form action="search.php" method="GET" style="max-width: 500px; margin: 0 auto; display: flex; gap: 10px;">
                <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="Search products, blogs, services..." style="flex: 1; padding: 15px; border-radius: 50px; border: 1px solid var(--glass-border); background: var(--glass); color: white;">
                <button type="submit" class="btn-submit" style="border-radius: 50px; padding: 0 30px;"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <?php if ($count > 0): ?>
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <?php foreach ($results as $item): ?>
                    <a href="<?php 
                        if($item['type'] == 'blog') echo 'single_blog.php?slug=' . $item['slug'];
                        elseif($item['type'] == 'product') echo 'shop/product.php?id=' . $item['id']; // Assuming shop structure
                        elseif($item['type'] == 'partner') echo $item['slug'];
                        ?>" 
                       style="text-decoration: none; color: inherit; display: block;">
                        <div style="background: var(--glass); padding: 25px; border-radius: 20px; border: 1px solid var(--glass-border); transition: transform 0.2s;" onmouseover="this.style.transform='translateX(10px)'" onmouseout="this.style.transform='translateX(0)'">
                            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                                <span style="background: rgba(102, 126, 234, 0.1); color: var(--primary); padding: 3px 10px; border-radius: 5px; font-size: 0.75rem; text-transform: uppercase; font-weight: bold; margin-right: 15px;">
                                    <?php echo $item['type']; ?>
                                </span>
                                <h3 style="margin: 0; font-size: 1.1rem;"><?php echo htmlspecialchars($item['title']); ?></h3>
                            </div>
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo htmlspecialchars(strip_tags($item['content'])); ?>
                            </p>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php elseif ($q): ?>
            <div style="text-align: center; padding: 50px; background: rgba(255, 255, 255, 0.05); border-radius: 20px;">
                <i class="fas fa-search" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 20px;"></i>
                <h3>No results found</h3>
                <p style="color: var(--text-muted);">Try different keywords or check your spelling.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
