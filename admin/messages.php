<?php include 'admin_header.php'; ?>

<?php
// Handle Actions
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header('Location: messages.php?msg=deleted');
    exit;
}
if (isset($_GET['read'])) {
    $stmt = $pdo->prepare("UPDATE messages SET status = 'read' WHERE id = ?");
    $stmt->execute([$_GET['read']]);
    header('Location: messages.php?msg=marked');
    exit;
}

// Fetch messages
$messages = [];
try {
    $messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
} catch (PDOException $e) {
    $error = "The 'messages' table is missing. Please run <a href='../setup.php'>setup.php</a> to fix this.";
}
?>

<div style="margin-bottom: 30px;">
    <h1>Inquiries & <span>Messages</span></h1>
    <p style="color: var(--text-muted);">Manage contact form submissions and service inquiries.</p>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88;">
        Message action completed.
    </div>
<?php endif; ?>

<div style="display: grid; gap: 20px;">
    <?php foreach ($messages as $m): ?>
        <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 25px; border-radius: 20px; position: relative;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                <div>
                    <h4 style="margin-bottom: 5px;"><?php echo htmlspecialchars($m['name']); ?></h4>
                    <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($m['email']); ?></span>
                    <span style="margin: 0 15px; color: var(--glass-border);">|</span>
                    <span class="badge" style="background: rgba(0, 132, 255, 0.1); color: #0084ff; border: 1px solid rgba(0, 132, 255, 0.3);">
                        Interest: <?php echo htmlspecialchars($m['interest']); ?>
                    </span>
                </div>
                <div style="text-align: right; font-size: 0.8rem; color: var(--text-muted);">
                    <?php echo date('M d, Y - H:i', strtotime($m['created_at'])); ?>
                    <div style="margin-top: 10px;">
                        <span class="badge <?php echo ($m['status'] == 'new') ? 'badge-pending' : 'badge-paid'; ?>" style="font-size: 0.7rem;">
                            <?php echo strtoupper($m['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <p style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 10px; font-size: 0.95rem; line-height: 1.6; margin-bottom: 20px;">
                <?php echo nl2br(htmlspecialchars($m['message'])); ?>
            </p>

            <div style="display: flex; gap: 15px;">
                <?php if ($m['status'] == 'new'): ?>
                    <a href="?read=<?php echo $m['id']; ?>" class="btn-sm" style="background: var(--primary); color: white; text-decoration: none; font-weight: bold; padding: 8px 15px;">Mark as Read</a>
                <?php endif; ?>
                <a href="mailto:<?php echo $m['email']; ?>?subject=Reply to your inquiry at LAEMMA" class="btn-sm" style="background: rgba(255,255,255,0.1); color: white; text-decoration: none; font-weight: bold; border: 1px solid var(--glass-border); padding: 8px 15px;">Reply via Email</a>
                <a href="?delete=<?php echo $m['id']; ?>" onclick="return confirm('Delete this message?')" style="color: #ff4757; text-decoration: none; font-size: 0.9rem; align-self: center; margin-left: auto;"><i class="fas fa-trash"></i> Delete</a>
            </div>
        </div>
    <?php endforeach; ?>

    <?php if (empty($messages)): ?>
        <div style="text-align: center; padding: 50px; color: var(--text-muted);">
            <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 20px;"></i>
            <p>No messages yet.</p>
        </div>
    <?php endif; ?>
</div>

</div><!-- end main-content -->
</body>
</html>
