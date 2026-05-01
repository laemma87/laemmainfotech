<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'] ?? 'General Inquiry';
    $message = $_POST['message'];
    $interest = $_POST['interest'] ?? 'General';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO messages (name, email, subject, message, interest, status) VALUES (?, ?, ?, ?, ?, 'new')");
        $stmt->execute([$name, $email, $subject, $message, $interest]);
        
        header('Location: index.php?msg=sent#contact');
        exit;
    } catch (PDOException $e) {
        header('Location: index.php?msg=error#contact');
        exit;
    }
} else {
    header('Location: index.php');
    exit;
}
?>
