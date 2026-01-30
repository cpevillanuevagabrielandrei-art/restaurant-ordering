<?php
session_start();
require 'includes/db.php';

// Add to cart
if(isset($_POST['add_to_cart'])){
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = 1;

    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = [];
    }

    if(isset($_SESSION['cart'][$product_id])){
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$product_id] = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity
        ];
    }

    header("Location: index.php");
    exit();
}

// Fetch products
$stmt = $conn->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MONTÉ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light glass px-4 py-2 shadow-sm mb-4">
    <a class="navbar-brand d-flex align-items-center" href="index.php">
        <img src="assets/images/logo.png" alt="MONTÉ Logo" height="50" class="me-2">
        <span class="fw-bold text-dark">MONTÉ</span>
    </a>

    <!-- Mobile toggler -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
        <ul class="navbar-nav align-items-center">

            <?php if(isset($_SESSION['user_id'])): ?>

                <!-- Greeting (desktop only) -->
                <li class="nav-item me-3 d-none d-lg-block">
                    <span class="fw-bold text-dark" style="font-family: 'Poppins', sans-serif;">Hi, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
                </li>

                <!-- Profile -->
                <li class="nav-item me-2">
                    <a href="user/profile.php" class="btn btn-outline-dark btn-sm w-100 mb-2 mb-lg-0">
                        <i class="bi bi-person-circle"></i> Profile
                    </a>
                </li>

                <!-- Orders -->
                <li class="nav-item me-2">
                    <a href="user/orders.php" class="btn btn-outline-dark btn-sm w-100 mb-2 mb-lg-0">
                        <i class="bi bi-receipt"></i> My Orders
                    </a>
                </li>

                <!-- Cart -->
                <li class="nav-item me-2">
                    <button class="btn btn-outline-dark btn-sm w-100 position-relative mb-2 mb-lg-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartSidebar">
                        <i class="bi bi-cart-fill"></i> Cart
                        <?php if(!empty($_SESSION['cart'])): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                <?= array_sum(array_column($_SESSION['cart'], 'quantity')) ?>
                            </span>
                        <?php endif; ?>
                    </button>
                </li>

                <!-- Logout -->
                <li class="nav-item">
                    <a href="auth/logout.php" class="btn btn-outline-dark btn-sm w-100 mb-2 mb-lg-0">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>

            <?php else: ?>
                <li class="nav-item me-2">
                    <a href="auth/login.php" class="btn btn-outline-dark btn-sm w-100 mb-2 mb-lg-0">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                </li>
                <li class="nav-item">
                    <a href="auth/register.php" class="btn btn-outline-dark btn-sm w-100 mb-2 mb-lg-0">
                        <i class="bi bi-person-plus"></i> Register
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</nav>





<!-- Cart Sidebar -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartSidebar">
    <div class="offcanvas-header">
        <h5>My Cart</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        <?php if(!empty($_SESSION['cart'])): ?>
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
                    <a href="cart/cart.php?remove=<?= $id ?>" class="btn btn-sm btn-danger ms-2">×</a>
                </li>
                <?php endforeach; ?>
            </ul>
            <p class="text-end"><strong>Total: ₱<?= number_format($total,2) ?></strong></p>
            <a href="cart/checkout.php" class="btn btn-success w-100 <?= empty($_SESSION['cart']) ? 'disabled' : '' ?>">Checkout</a>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Hero Section -->
<div class="container hero glass text-center py-5 mb-5">
    <h1 class="display-4 fw-bold">Welcome to MONTÉ</h1>
    <p class="lead">Delicious food delivered fast! Order your favorites with ease.</p>
    <a href="#menu" class="btn btn-primary btn-lg mt-3">Explore Menu</a>
</div>



<!-- Menu Section -->
<div class="container" id="menu">
    <h2 class="mb-4 text-center">Our Menu</h2>
    <div class="row g-4">
        <?php foreach($products as $product): ?>
        <div class="col-sm-6 col-md-4 col-lg-3 d-flex">
            <div class="glass card h-100 text-center p-3 shadow-sm d-flex flex-column">
                <img src="assets/images/<?= $product['image'] ?>" class="card-img-top img-fluid mb-3" alt="<?= htmlspecialchars($product['name']) ?>">

                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                    <p class="card-text flex-grow-1"><?= htmlspecialchars($product['description']) ?></p>
                    <p class="fw-bold mb-3">₱<?= number_format($product['price'], 2) ?></p>

                    <form method="POST" class="mt-auto">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                        <input type="hidden" name="price" value="<?= $product['price'] ?>">
                        <button type="submit" name="add_to_cart" class="btn btn-success w-100">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
