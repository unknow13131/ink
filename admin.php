<?php
session_start();

// require login and admin role
if (!isset($_SESSION['id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php?errcode=2');
    exit;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Inkspired Book Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="main-header">
        <div class="logo">Inkspired Book Shop</div>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="shop.php">Shop</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p>Welcome, administrator! Use the links below to manage the site.</p>
        <ul>
            <li><a href="#">Manage Books</a></li>
            <li><a href="#">View Orders</a></li>
            <li><a href="#">Users</a></li>
        </ul>
    </div>
</body>
</html>