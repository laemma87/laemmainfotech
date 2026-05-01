<?php
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch internship application status
$stmt = $pdo->prepare("SELECT * FROM internships WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$user_id]);
$internship = $stmt->fetch();

?>
<?php include '../includes/header.php'; ?>

<main style="padding-top: 150px; padding-bottom: 80px;">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
            <h1>My <span>Student Dashboard</span></h1>
            <div style="background: var(--glass); padding: 10px 20px; border-radius: 50px; border: 1px solid var(--glass-border);">
                <i class="fas fa-user-graduate" style="color: var(--primary); margin-right: 10px;"></i>
                <span>Student Account</span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Application Status -->
            <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 40px; border-radius: 25px;">
                <h3>Internship Application Status</h3>
                
                <?php if ($internship): ?>
                    <div style="margin-top: 30px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border);">
                            <span style="color: var(--text-muted);">Field:</span>
                            <span style="font-weight: 600;"><?php echo htmlspecialchars($internship['field']); ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border);">
                            <span style="color: var(--text-muted);">Payment Status:</span>
                            <?php if ($internship['payment_status'] === 'paid'): ?>
                                <span style="color: #00ff88; font-weight: 600;"><i class="fas fa-check-circle"></i> Paid</span>
                            <?php else: ?>
                                <span style="color: #ffa502; font-weight: 600;"><i class="fas fa-clock"></i> Pending Payment</span>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border);">
                            <span style="color: var(--text-muted);">Application Status:</span>
                            <?php 
                            $status_color = '#ffa502';
                            $status_icon = 'clock';
                            if ($internship['status'] === 'accepted') { $status_color = '#00ff88'; $status_icon = 'check-circle'; }
                            if ($internship['status'] === 'rejected') { $status_color = '#ff4757'; $status_icon = 'times-circle'; }
                            if ($internship['status'] === 'under_review') { $status_color = '#667eea'; $status_icon = 'search'; }
                            ?>
                            <span style="color: <?php echo $status_color; ?>; font-weight: 600; text-transform: capitalize;">
                                <i class="fas fa-<?php echo $status_icon; ?>"></i> <?php echo str_replace('_', ' ', $internship['status']); ?>
                            </span>
                        </div>
                        
                        <?php if ($internship['status_updated_at']): ?>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid var(--glass-border);">
                            <span style="color: var(--text-muted);">Last Updated:</span>
                            <span style="font-size: 0.9rem;"><?php echo date('F j, Y - g:i A', strtotime($internship['status_updated_at'])); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Status-specific messages -->
                        <?php if ($internship['status'] === 'under_review'): ?>
                            <div style="margin-top: 30px; padding: 20px; background: rgba(102, 126, 234, 0.1); border-radius: 15px; border: 1px solid rgba(102, 126, 234, 0.3);">
                                <h4 style="color: #667eea;"><i class="fas fa-hourglass-half"></i> Under Review</h4>
                                <p style="font-size: 0.9rem; margin-top: 10px;">Your application is currently being evaluated by our team. We will notify you via email once a decision has been made. This typically takes 2-3 business days.</p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($internship['payment_status'] !== 'paid' && $internship['status'] === 'accepted'): ?>
                            <a href="payment.php?type=internship" class="btn-submit" style="display: block; text-align: center; margin-top: 30px; text-decoration: none;">Complete Payment (20,000 RWF)</a>
                        <?php endif; ?>

                        <?php if ($internship['status'] === 'accepted'): ?>
                            <div style="margin-top: 30px; padding: 20px; background: rgba(0, 255, 136, 0.1); border-radius: 15px; border: 1px solid rgba(0, 255, 136, 0.3);">
                                <h4 style="color: #00ff88;"><i class="fas fa-bullhorn"></i> Congratulations!</h4>
                                <p style="font-size: 0.9rem; margin-top: 10px;">Your application has been accepted! 
                                <?php if ($internship['payment_status'] === 'paid'): ?>
                                    Your payment has been confirmed. Check your email for the onboarding link and scheduled interview date.
                                <?php else: ?>
                                    Please complete your payment to confirm your spot in the program.
                                <?php endif; ?>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($internship['status'] === 'rejected'): ?>
                            <div style="margin-top: 30px; padding: 20px; background: rgba(255, 71, 87, 0.1); border-radius: 15px; border: 1px solid rgba(255, 71, 87, 0.3);">
                                <h4 style="color: #ff4757;"><i class="fas fa-info-circle"></i> Application Not Successful</h4>
                                <p style="font-size: 0.9rem; margin-top: 10px;">Unfortunately, your application was not successful at this time.</p>
                                <?php if (!empty($internship['status_notes'])): ?>
                                    <div style="margin-top: 15px; padding: 15px; background: rgba(0,0,0,0.2); border-radius: 10px;">
                                        <strong>Feedback:</strong>
                                        <p style="margin-top: 5px;"><?php echo htmlspecialchars($internship['status_notes']); ?></p>
                                    </div>
                                <?php endif; ?>
                                <p style="font-size: 0.85rem; margin-top: 15px; color: var(--text-muted);">We encourage you to apply again in the future. Keep developing your skills!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 60px 0;">
                        <i class="fas fa-folder-open" style="font-size: 3rem; color: var(--glass-border); margin-bottom: 20px;"></i>
                        <p style="color: var(--text-muted);">You haven't applied for any internship yet.</p>
                        <a href="apply.php" style="color: var(--primary); display: block; margin-top: 15px; font-weight: 600;">Apply Now</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Profile Info -->
            <div style="display: flex; flex-direction: column; gap: 30px;">
                <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 30px; border-radius: 25px;">
                    <h3>Profile Management</h3>
                    <div style="text-align: center; margin: 30px 0;">
                        <div style="width: 100px; height: 100px; border-radius: 50%; background: var(--gradient); margin: 0 auto 20px; overflow: hidden; border: 3px solid var(--glass-border);">
                            <img src="https://via.placeholder.com/100" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h4><?php echo htmlspecialchars($_SESSION['name']); ?></h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem;"><?php echo htmlspecialchars($_SESSION['role']); ?></p>
                    </div>
                    <a href="../settings.php" style="display: block; text-align: center; color: var(--primary); font-size: 0.9rem;">Change Name or Photo</a>
                </div>

                <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 30px; border-radius: 25px;">
                    <h3>Need Help?</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin: 15px 0;">If you have any questions regarding your application or payment, please contact our support.</p>
                    <a href="../index.php#contact" style="color: var(--primary); font-size: 0.9rem; font-weight: 600;">Contact Administrator</a>
                </div>
                <?php if ($internship['payment_status'] === 'paid'): ?>
                    <!-- Internship Materials -->
                    <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 30px; border-radius: 25px; margin-bottom: 30px;">
                        <h3 style="margin-bottom: 20px;"><i class="fas fa-book" style="color: var(--primary);"></i> Program Materials</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 15px; border: 1px solid var(--glass-border);">
                                <h5 style="margin-bottom: 10px;">Curriculum PDF</h5>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 15px;">Full 2-month training roadmap.</p>
                                <a href="#" class="btn-sm" style="background: var(--primary); color: white; text-decoration: none; padding: 5px 15px; border-radius: 5px; font-size: 0.8rem;">Download</a>
                            </div>
                            <div style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: 15px; border: 1px solid var(--glass-border);">
                                <h5 style="margin-bottom: 10px;">Onboarding Guide</h5>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 15px;">Software and tools setup guide.</p>
                                <a href="#" class="btn-sm" style="background: var(--primary); color: white; text-decoration: none; padding: 5px 15px; border-radius: 5px; font-size: 0.8rem;">Download</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Professional Gallery -->
                <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 30px; border-radius: 25px;">
                    <h3 style="margin-bottom: 20px;"><i class="fas fa-images" style="color: var(--primary);"></i> Internship Life</h3>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                        <img src="https://via.placeholder.com/300x200?text=Workshop" style="width: 100%; border-radius: 10px;">
                        <img src="https://via.placeholder.com/300x200?text=Development" style="width: 100%; border-radius: 10px;">
                        <img src="https://via.placeholder.com/300x200?text=Collaboration" style="width: 100%; border-radius: 10px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
