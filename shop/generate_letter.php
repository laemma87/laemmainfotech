<?php
include '../includes/db.php';

// Simulate generating a PDF/DOCX as a confirmation letter
// For the prototype, we'll just output a formatted HTML page that can be printed.

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirmation Letter | LAEMMA INFO TECH</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; padding: 50px; line-height: 1.6; }
        .letter-head { text-align: center; border-bottom: 2px solid #0084ff; padding-bottom: 20px; margin-bottom: 40px; }
        .logo { font-size: 24px; font-weight: bold; color: #0084ff; }
        .details { margin-bottom: 30px; }
        .footer { margin-top: 100px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #eee; padding-top: 20px; }
        .stamp { border: 3px solid #ff4757; color: #ff4757; display: inline-block; padding: 10px 20px; font-weight: bold; border-radius: 10px; transform: rotate(-15deg); margin-top: 20px; }
        @media print {
            .btn-print { display: none; }
        }
    </style>
</head>
<body>
    <div style="max-width: 800px; margin: 0 auto; background: white; padding: 40px; border: 1px solid #ddd; box-shadow: 0 0 20px rgba(0,0,0,0.1);">
        <div class="letter-head">
            <div class="logo">LAEMMA INFO TECH</div>
            <p>Rusizi District, Rwanda | +250 789 011 738 | laemma50@gmail.com</p>
        </div>

        <div style="text-align: right; margin-bottom: 40px;">
            <p>Date: <?php echo date('F d, Y'); ?></p>
            <p>Ref: LIT-CONF-<?php echo rand(1000, 9999); ?></p>
        </div>

        <div class="details">
            <h2 style="text-align: center; text-decoration: underline;">CONFIRMATION LETTER & PROOF OF PURCHASE</h2>
            <p>To Whom It May Concern,</p>
            <p>This letter serves as official confirmation that a purchase has been made at LAEMMA INFO TECH. Our system has successfully verified the payment through our integrated payment systems.</p>
        </div>

        <div style="margin: 40px 0; background: #f9f9f9; padding: 25px; border-radius: 5px;">
            <h4 style="margin-top: 0;">Order Details:</h4>
            <table style="width: 100%;">
                <tr><td><strong>Customer Name:</strong></td><td>Valued Customer</td></tr>
                <tr><td><strong>Transaction ID:</strong></td><td><?php echo strtoupper(bin2hex(random_bytes(4))); ?></td></tr>
                <tr><td><strong>Payment Method:</strong></td><td>Integrated Mobile/Card</td></tr>
                <tr><td><strong>Total Paid:</strong></td><td>Verified in System</td></tr>
            </table>
        </div>

        <p>The purchaser is now authorized to receive the items/services mentioned in the order records. Please facilitate delivery/access accordingly.</p>

        <div style="margin-top: 60px;">
            <p>Authorized by,</p>
            <div style="margin: 20px 0;">
                <img src="https://via.placeholder.com/150x50?text=SIGNATURE" alt="Signature">
            </div>
            <p><strong>HAKIZIMANA Emmanuel</strong><br>CEO, LAEMMA INFO TECH</p>
            
            <div class="stamp">OFFICIALLY PAID</div>
        </div>

        <div class="footer">
            <p>This is a computer-generated confirmation letter and is valid with the system-stored transaction record.</p>
        </div>
        
        <div style="text-align: center; margin-top: 40px;" class="btn-print">
            <button onclick="window.print()" style="padding: 10px 25px; background: #0084ff; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Print or Save as PDF</button>
        </div>
    </div>
</body>
</html>
