<?php
include 'includes/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = '';
$error = '';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $new_pass = $_POST['new_password'];
    
    // Handle Profile Picture Upload
    $profile_picture = $user['profile_picture']; // Keep existing by default
    
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory for user
            $uploadFileDir = 'uploads/profile_pictures/';
            if (!file_exists($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            $newFileName = 'user_' . $user_id . '_' . time() . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;
            
            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_picture = $dest_path;
            } else {
                $error = "There was an error moving the uploaded file.";
            }
        } else {
            $error = "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
        }
    }

    if (!$error) {
        try {
            if (!empty($new_pass)) {
                $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, profile_picture = ? WHERE id = ?");
                $stmt->execute([$name, $email, $hashed_password, $profile_picture, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?");
                $stmt->execute([$name, $email, $profile_picture, $user_id]);
            }
            
            $_SESSION['name'] = $name;
            $msg = "Profile updated successfully!";
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch();
            
        } catch (PDOException $e) {
            $error = "Update failed. Email might already be in use.";
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<main style="padding-top: 120px; padding-bottom: 80px;">
    <div class="container" style="max-width: 600px;">
        <div style="background: var(--glass); border: 1px solid var(--glass-border); padding: 40px; border-radius: 30px; backdrop-filter: blur(15px);">
            <h2 style="text-align: center; margin-bottom: 30px;">Profile <span>Settings</span></h2>
            
            <?php if ($msg): ?>
                <div style="background: rgba(0, 255, 136, 0.1); border: 1px solid #00ff88; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #00ff88; text-align: center;">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="background: rgba(255, 71, 87, 0.1); border: 1px solid #ff4757; padding: 15px; border-radius: 10px; margin-bottom: 20px; color: #ff4757; text-align: center;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div style="text-align: center; margin-bottom: 30px;">
                <?php if (!empty($user['profile_picture'])): ?>
                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid var(--primary); margin-bottom: 15px;">
                <?php else: ?>
                    <div style="width: 120px; height: 120px; border-radius: 50%; background: var(--gradient); display: inline-flex; align-items: center; justify-content: center; font-size: 40px; font-weight: bold; color: white; margin-bottom: 15px; border: 4px solid rgba(255,255,255,0.1);">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Profile Picture</label>
                    <input type="file" name="profile_picture" accept="image/png, image/jpeg" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                    <small style="color: var(--text-muted);">Max size: 2MB. Formats: JPG, PNG.</small>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Full Name</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($user['name']); ?>" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label>Email Address</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email']); ?>" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>

                <div class="form-group" style="margin-bottom: 30px;">
                    <label>New Password (leave blank to keep current)</label>
                    <input type="password" name="new_password" placeholder="••••••••" style="width: 100%; padding: 12px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); border-radius: 10px; color: white;">
                </div>

                <button type="submit" class="btn-submit" style="width: 100%; padding: 15px; border-radius: 50px; font-size: 1rem;">
                    Update Profile
                </button>
            </form>
            
            <hr style="border: 0; border-top: 1px solid var(--glass-border); margin: 30px 0;">
            
            <div style="text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 10px;">Role: <span style="color: var(--primary); text-transform: capitalize; font-weight: bold;"><?php echo $user['role']; ?></span></p>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Member since: <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
