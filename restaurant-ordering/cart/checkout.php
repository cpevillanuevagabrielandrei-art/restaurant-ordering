<?php
session_start();
require '../includes/db.php';

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
    header("Location: ../index.php");
    exit();
}

// Place Order
if(isset($_POST['place_order'])){
    if(!isset($_SESSION['user_id'])){
        header("Location: ../auth/login.php");
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $status = 'Pending';

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, status, created_at) VALUES (:user_id, :status, NOW())");
    $stmt->execute([':user_id'=>$user_id, ':status'=>$status]);
    $order_id = $conn->lastInsertId();

    // Insert order items
    foreach($_SESSION['cart'] as $product_id => $item){
        $subtotal = $item['price'] * $item['quantity'];
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, subtotal) VALUES (:order_id, :product_id, :quantity, :subtotal)");
        $stmt->execute([
            ':order_id'=>$order_id,
            ':product_id'=>$product_id,
            ':quantity'=>$item['quantity'],
            ':subtotal'=>$subtotal
        ]);
    }

    unset($_SESSION['cart']);

    // Fetch order details to display
    $stmt_order = $conn->prepare("SELECT * FROM orders WHERE id=:order_id");
    $stmt_order->execute([':order_id'=>$order_id]);
    $order = $stmt_order->fetch();

    $stmt_items = $conn->prepare("
        SELECT oi.*, p.name 
        FROM order_items oi 
        JOIN products p ON oi.product_id=p.id 
        WHERE order_id=:order_id
    ");
    $stmt_items->execute([':order_id'=>$order_id]);
    $items = $stmt_items->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - MONTÉ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<div class="container my-5">

<?php if(isset($order)): ?>
    <div class="glass p-4 shadow-sm">
        <h2 class="mb-4 text-center">Order Placed!</h2>
        <h5>
            Order #<?= $order['id'] ?> - <?= date("M d, Y H:i", strtotime($order['created_at'])) ?> - 
            <span class="status-badge <?= strtolower($order['status'])=='pending' ? 'pending' : 'completed' ?>">
                <?= ucfirst($order['status']) ?>
            </span>
        </h5>
        <table class="table table-bordered mt-3 mb-0">
            <thead class="table-light">
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Subtotal (₱)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach($items as $item): 
                    $total += $item['subtotal'];
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['subtotal'],2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2" class="text-end fw-bold">Total:</td>
                    <td class="fw-bold">₱<?= number_format($total,2) ?></td>
                </tr>
            </tbody>
        </table>
        <div class="mt-3 text-center">
            <a href="../index.php" class="btn btn-primary me-2">Back to Home</a>
            <a href="checkout.php" class="btn btn-success">Place Another Order</a>
        </div>
    </div>
<?php else: ?>
    <div class="glass p-4 shadow-sm">
        <h2 class="mb-4 text-center">Checkout</h2>
        <ul class="list-group mb-3">
            <?php 
            $total = 0;
            foreach($_SESSION['cart'] as $id => $item):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= $item['name'] ?> x <?= $item['quantity'] ?>
                <span>₱<?= number_format($subtotal,2) ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
        <p class="text-end fw-bold fs-5">Total: ₱<?= number_format($total,2) ?></p>
        <form method="POST">
            <button type="submit" name="place_order" class="btn btn-success w-100">Place Order</button>
        </form>
        <a href="../index.php" class="btn btn-secondary w-100 mt-2">Back to Home</a>
    </div>
<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
