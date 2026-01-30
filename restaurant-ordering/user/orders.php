<?php
session_start();
require '../includes/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders of this user
$stmt = $conn->prepare("
    SELECT * 
    FROM orders 
    WHERE user_id = :user_id 
    ORDER BY created_at DESC
");
$stmt->execute([':user_id' => $user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - MONTÉ</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container my-5">
    <h2 class="mb-4 text-center">My Orders</h2>

    <a href="../index.php" class="btn btn-secondary mb-3">
    ← Back to Home
</a>


    <?php if(empty($orders)): ?>
        <div class="glass p-4 text-center">
            <p>You have no orders yet.</p>
            <a href="../index.php" class="btn btn-primary">Start Ordering</a>
        </div>
    <?php else: ?>

        <?php foreach($orders as $order): ?>

        <?php
        // Fetch items for this order
        $stmt_items = $conn->prepare("
            SELECT oi.*, p.name 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $stmt_items->execute([':order_id' => $order['id']]);
        $items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

        $total = 0;
        foreach($items as $item){
            $total += $item['subtotal'];
        }
        ?>

        <div class="glass p-4 mb-4 shadow-sm">
            <div class="d-flex justify-content-between flex-wrap">
                <h5>
                    Order #<?= $order['id'] ?>
                </h5>
                <span>
                    <?= date("M d, Y h:i A", strtotime($order['created_at'])) ?>
                </span>
            </div>

            <p>
                Status: 
                <span class="badge bg-<?= strtolower($order['status']) === 'pending' ? 'warning' : 'success' ?>">
                    <?= ucfirst($order['status']) ?>
                </span>
            </p>

            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Subtotal (₱)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['subtotal'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="2" class="text-end fw-bold">Total</td>
                        <td class="fw-bold">₱<?= number_format($total, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php endforeach; ?>

    <?php endif; ?>
</div>

</body>
</html>
