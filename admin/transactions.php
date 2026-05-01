<?php include 'admin_header.php'; ?>

<?php
// Fetch transactions (Orders and Internship payments)
$ordersQuery = "SELECT o.*, p.name as product_name FROM orders o LEFT JOIN products p ON o.product_id = p.id ORDER BY o.created_at DESC";
$orders = $pdo->query($ordersQuery)->fetchAll();
?>

<div style="margin-bottom: 30px;">
    <h1>Financial <span>Transactions</span></h1>
    <p style="color: var(--text-muted);">Track all product sales and service payments.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
    <div style="background: var(--card-gradient); border: 1px solid var(--border); padding: 25px; border-radius: 20px; box-shadow: var(--shadow);">
        <h4 style="color: var(--text-muted); margin-bottom: 10px; font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">Total Revenue</h4>
        <h2 style="color: var(--secondary); font-weight: 900;">
            <?php 
            $total = 0;
            foreach($orders as $o) if($o['payment_status'] == 'paid') $total += $pdo->query("SELECT price FROM products WHERE id = ".$o['product_id'])->fetchColumn();
            echo number_format($total);
            ?> RWF
        </h2>
    </div>
    <div style="background: var(--card-gradient); border: 1px solid var(--border); padding: 25px; border-radius: 20px; box-shadow: var(--shadow);">
        <h4 style="color: var(--text-muted); margin-bottom: 10px; font-size: 0.8rem; text-transform: uppercase; font-weight: 700;">Pending Payments</h4>
        <h2 style="color: #ffa502; font-weight: 900;">
            <?php 
            echo count(array_filter($orders, function($o){ return $o['payment_status'] == 'pending'; }));
            ?> Orders
        </h2>
    </div>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Product / Service</th>
            <th>Amount</th>
            <th>Payment Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($orders as $o): ?>
            <tr>
                <td>#<?php echo $o['id']; ?></td>
                <td>
                    <div style="font-weight: 600;"><?php echo htmlspecialchars($o['name']); ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo $o['phone']; ?></div>
                </td>
                <td><?php echo htmlspecialchars($o['product_name']); ?></td>
                <td>
                    <?php 
                    $price = $pdo->query("SELECT price FROM products WHERE id = ".$o['product_id'])->fetchColumn();
                    echo number_format($price); 
                    ?> RWF
                </td>
                <td>
                    <span class="badge <?php echo ($o['payment_status'] === 'paid') ? 'badge-paid' : 'badge-pending'; ?>">
                        <?php echo $o['payment_status']; ?>
                    </span>
                </td>
                <td style="font-size: 0.85rem; color: var(--text-muted);"><?php echo date('M d, Y', strtotime($o['created_at'])); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div><!-- end main-content -->
</body>
</html>
