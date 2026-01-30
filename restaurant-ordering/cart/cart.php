<?php
session_start();
require '../includes/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

// Update quantities
if(isset($_POST['update_cart'])){
    foreach($_POST['quantities'] as $id => $qty){
        if($qty <= 0){
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['quantity'] = $qty;
        }
    }
}

// Remove item
if(isset($_GET['remove'])){
    unset($_SESSION['cart'][$_GET['remove']]);
}

// Redirect if empty
if(empty($_SESSION['cart'])){
    header("Location: ../index.php");
    exit();
}

$total = 0;
foreach($_SESSION['cart'] as $item){
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cart</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container my-5 glass p-4">
    <h2>Your Cart</h2>
    <form method="POST">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Price (₱)</th>
                    <th>Quantity</th>
                    <th>Subtotal (₱)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($_SESSION['cart'] as $id => $item): ?>
                <tr>
                    <td><?= $item['name'] ?></td>
                    <td><?= number_format($item['price'],2) ?></td>
                    <td><input type="number" name="quantities[<?= $id ?>]" value="<?= $item['quantity'] ?>" class="form-control" min="0"></td>
                    <td><?= number_format($item['price'] * $item['quantity'],2) ?></td>
                    <td><a href="cart.php?remove=<?= $id ?>" class="btn btn-danger btn-sm">Remove</a></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                    <td colspan="2"><strong>₱<?= number_format($total,2) ?></strong></td>
                </tr>
            </tbody>
        </table>
        <div class="d-flex justify-content-between">
            <a href="../index.php" class="btn btn-secondary">Back to Home</a>
            <div>
                <button type="submit" name="update_cart" class="btn btn-primary">Update Cart</button>
                <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
            </div>
        </div>
    </form>
</div>
</body>
</html>
