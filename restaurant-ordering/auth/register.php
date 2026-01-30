<?php
session_start();
require '../includes/db.php';

$errors = [];

if(isset($_POST['register'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Basic validation
    if(empty($name) || empty($email) || empty($password)){
        $errors[] = "All fields are required.";
    }
    if($password !== $confirm){
        $errors[] = "Passwords do not match.";
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=:email");
    $stmt->execute([':email'=>$email]);
    if($stmt->rowCount() > 0){
        $errors[] = "Email already registered.";
    }

    if(empty($errors)){
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (:name,:email,:password)");
        $stmt->execute([
            ':name'=>$name,
            ':email'=>$email,
            ':password'=>$hashed
        ]);
        $_SESSION['user_id'] = $conn->lastInsertId();
        $_SESSION['user_name'] = $name;
        header("Location: ../index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Register</h2>

    <?php if($errors): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $e) echo $e."<br>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
        <a href="login.php" class="btn btn-link">Already have an account? Login</a>
    </form>
</div>
</body>
</html>
