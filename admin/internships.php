<?php include 'admin_header.php'; ?>

<?php
include '../includes/email_helper.php';

// Handle Actions
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    
    // Get applicant details
    $stmt = $pdo->prepare("SELECT * FROM internships WHERE id = ?");
    $stmt->execute([$id]);
    $applicant = $stmt->fetch();
    
    // Update status
    $stmt = $pdo->prepare("UPDATE internships SET status = 'accepted', status_updated_at = NOW() WHERE id = ?");
    $stmt->execute([$id]);
    
    // Send email notification
    if ($applicant) {
        sendStatusUpdate($applicant['email'], $applicant['full_names'], 'accepted');
    }
    
    header('Location: internships.php?msg=approved');
    exit;
}

if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $notes = $_GET['notes'] ?? '';
    
    // Get applicant details
    $stmt = $pdo->prepare("SELECT * FROM internships WHERE id = ?");
    $stmt->execute([$id]);
    $applicant = $stmt->fetch();
    
    // Update status with notes
    $stmt = $pdo->prepare("UPDATE internships SET status = 'rejected', status_updated_at = NOW(), status_notes = ? WHERE id = ?");
    $stmt->execute([$notes, $id]);
    
    // Send email notification
    if ($applicant) {
        sendStatusUpdate($applicant['email'], $applicant['full_names'], 'rejected', $notes);
    }
    
    header('Location: internships.php?msg=rejected');
    exit;
}

// Fetch applications
$apps = $pdo->query("SELECT * FROM internships ORDER BY created_at DESC")->fetchAll();
?>

<div style="margin-bottom: 30px;">
    <h1>Internship <span>Applications</span></h1>
    <p style="color: var(--text-muted);">Review and approve student internship requests.</p>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88;">
        Application marked as <?php echo $_GET['msg']; ?>.
    </div>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>Applicant</th>
            <th>Details</th>
            <th>Payment</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($apps as $a): ?>
            <tr>
                <td>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($a['full_names']); ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($a['email']); ?></div>
                </td>
                <td>
                    <div style="font-size: 0.85rem;"><i class="fas fa-university"></i> <?php echo htmlspecialchars($a['school']); ?></div>
                    <div style="font-size: 0.85rem; color: var(--primary);"><i class="fas fa-laptop-code"></i> <?php echo $a['field']; ?></div>
                </td>
                <td>
                    <span class="badge <?php echo ($a['payment_status'] === 'paid') ? 'badge-paid' : 'badge-pending'; ?>">
                        <?php echo ($a['payment_status'] === 'paid') ? '<i class="fas fa-check"></i> Paid' : '<i class="fas fa-clock"></i> Pending'; ?>
                    </span>
                </td>
                <td>
                    <?php 
                    $s = $a['status'];
                    $c = 'badge-pending';
                    if ($s === 'accepted') $c = 'badge-paid';
                    if ($s === 'rejected') $c = 'badge-rejected';
                    if ($s === 'under_review') $c = 'badge-pending';
                    ?>
                    <span class="badge <?php echo $c; ?>" style="text-transform: capitalize;">
                        <?php echo str_replace('_', ' ', $s); ?>
                    </span>
                </td>
                <td>
                    <?php if ($s === 'pending' || $s === 'under_review'): ?>
                        <a href="internships.php?approve=<?php echo $a['id']; ?>" class="btn-sm" style="background: #00ff88; color: var(--darker); text-decoration: none; font-weight: bold; margin-right: 5px; padding: 5px 10px; border-radius: 5px;">Approve</a>
                        
                        <!-- Simple Reject with Notes Form Trigger -->
                        <button onclick="document.getElementById('reject-form-<?php echo $a['id']; ?>').style.display='block'" class="btn-sm" style="background: #ff4757; color: white; text-decoration: none; font-weight: bold; border:none; cursor:pointer; padding: 5px 10px; border-radius: 5px;">Reject</button>
                        
                        <div id="reject-form-<?php echo $a['id']; ?>" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; align-items: center; justify-content: center;">
                            <div style="background: var(--glass); padding: 30px; border-radius: 15px; width: 400px; margin: 100px auto; border: 1px solid var(--glass-border);">
                                <h3 style="margin-bottom: 20px;">Reject Application</h3>
                                <form method="GET" action="internships.php">
                                    <input type="hidden" name="reject" value="<?php echo $a['id']; ?>">
                                    <div style="margin-bottom: 15px;">
                                        <label>Reason / Feedback:</label>
                                        <textarea name="notes" required style="width: 100%; padding: 10px; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); color: white; border-radius: 5px;" rows="4"></textarea>
                                    </div>
                                    <div style="display: flex; gap: 10px;">
                                        <button type="submit" style="background: #ff4757; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Confirm Reject</button>
                                        <button type="button" onclick="document.getElementById('reject-form-<?php echo $a['id']; ?>').style.display='none'" style="background: #555; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    <?php else: ?>
                        <span style="color: var(--text-muted); font-size: 0.8rem;">
                            <?php if($s==='accepted') echo '<i class="fas fa-check" style="color:#00ff88"></i> Approved'; ?>
                            <?php if($s==='rejected') echo '<i class="fas fa-times" style="color:#ff4757"></i> Rejected'; ?>
                        </span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div><!-- end main-content -->
</body>
</html>
