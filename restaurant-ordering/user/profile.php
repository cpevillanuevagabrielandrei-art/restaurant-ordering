<?php
session_start();
require '../includes/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch();

// Handle form submission
$success = '';
$error = '';

if(isset($_POST['update_profile'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // optional
    $confirm_password = $_POST['confirm_password'];

    // Validate basic inputs
    if(empty($name) || empty($email)){
        $error = "Name and Email cannot be empty.";
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif(!empty($password) && $password !== $confirm_password){
        $error = "Passwords do not match.";
    } else {
        // Update query
        if(!empty($password)){
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET name=:name, email=:email, password=:password WHERE id=:id");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashed_password,
                ':id' => $user_id
            ]);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=:name, email=:email WHERE id=:id");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':id' => $user_id
            ]);
        }

        $_SESSION['user_name'] = $name; // update session display name
        $success = "Profile updated successfully!";
        
        // Refresh user data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile - MONTÉ</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container my-5">
    <div class="glass p-4 shadow-sm">
        <h2 class="mb-4">Edit Profile</h2>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <hr>
            <h5>Change Password (Optional)</h5>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Leave blank to keep current password">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Leave blank to keep current password">
            </div>

            <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
            <a href="../index.php" class="btn btn-secondary ms-2">← Back to Home</a>
            <a href="orders.php" class="btn btn-primary ms-2">View My Orders</a>
        </form>
    </div>
</div>

</body>
</html>
