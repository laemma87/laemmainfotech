<?php
include '../includes/db.php';
include '../includes/email_helper.php';

$is_logged_in = isset($_SESSION['user_id']);
if (!$is_logged_in) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_names = $_POST['full_names'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $level = $_POST['level'];
    $school = $_POST['school'];
    $equipment = $_POST['equipment'];
    $field = $_POST['field'];

    try {
        // Insert application with 'under_review' status and timestamp
        $stmt = $pdo->prepare("INSERT INTO internships (user_id, full_names, email, phone, level, school, equipment, field, status, status_updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'under_review', NOW())");
        $stmt->execute([$_SESSION['user_id'], $full_names, $email, $phone, $level, $school, $equipment, $field]);
        
        // Send confirmation email
        sendApplicationConfirmation($email, $full_names, $field);
        
        // Redirect to dashboard instead of payment (payment comes after acceptance)
        header('Location: dashboard.php?msg=submitted');
        exit;
    } catch (PDOException $e) {
        $error = "An error occurred. Please try again.";
    }
}
?>
<?php include '../includes/header.php'; ?>

<main style="padding-top: 120px; padding-bottom: 80px;">
    <div class="container" style="max-width: 700px;">
        <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 50px; border-radius: 30px; backdrop-filter: blur(15px);">
            <h2 style="text-align: center; margin-bottom: 10px;">Apply for <span>Internship</span></h2>
            <p style="text-align: center; color: var(--text-muted); margin-bottom: 40px;">Fill out the form below to submit your application.</p>

            <?php if ($error): ?>
                <p style="color: #ff4757; text-align: center; margin-bottom: 20px;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="form-group">
                        <label>Full Names *</label>
                        <input type="text" name="full_names" required placeholder="John Doe" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    </div>
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required placeholder="john@example.com" value="<?php echo $_SESSION['email'] ?? ''; ?>" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label>Phone Number *</label>
                    <input type="text" name="phone" required placeholder="+250 7XX XXX XXX" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label>Education Level *</label>
                    <select name="level" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                        <option value="">Select education level</option>
                        <option value="Secondary">Secondary</option>
                        <option value="University">University</option>
                        <option value="Graduate">Graduate</option>
                    </select>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label>School Name *</label>
                    <input type="text" name="school" required placeholder="Your School/University Name" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label>Equipment Status *</label>
                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
                            <input type="radio" name="equipment" value="Yes, I have a laptop" required> Yes, I have a laptop
                        </label>
                        <label style="display: flex; align-items: center; gap: 10px; font-size: 0.9rem;">
                            <input type="radio" name="equipment" value="No, I don't have a laptop" required> No, I don't have a laptop
                        </label>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 20px;">
                    <label>Internship Field *</label>
                    <select name="field" required style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                        <option value="">Select internship field</option>
                        <option value="Software Development">Software Development</option>
                        <option value="Web Development">Web Development</option>
                        <option value="Database Analysis">Database Analysis</option>
                        <option value="Computer Maintenance">Computer Maintenance</option>
                        <option value="Cyber Security">Cyber Security</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit" style="margin-top: 40px; width: 100%; padding: 15px; border-radius: 50px; font-size: 1.1rem;">Submit Application</button>
            </form>
            <p style="text-align: center; margin-top: 20px; color: var(--text-muted); font-size: 0.8rem;">By submitting this form, you agree to our terms and conditions.</p>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
