<?php
session_start();
// Add include 'db.php'; here if you need to validate the ID against the database first

if(isset($_POST['ac'])) {
    $book_id = $_POST['ac'];
    $quantity = intval($_POST['quantity']);

    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Add to session
    $_SESSION['cart'][$book_id] = ($_SESSION['cart'][$book_id] ?? 0) + $quantity;

    // Redirect back to shop or cart
    header("Location: shop.php?added=true");
    exit();
}
?>