<?php
session_start();
include 'db.php';

$message = '';

// Handle quantity updates
if(isset($_POST['update_quantity'])) {
    $book_id = intval($_POST['book_id']);
    $quantity = intval($_POST['quantity']);
    
    if($quantity <= 0) {
        unset($_SESSION['cart'][$book_id]);
        $message = "Item removed from cart.";
    } else {
        $_SESSION['cart'][$book_id] = $quantity;
        $message = "Cart updated.";
    }
}

// Handle item removal
if(isset($_GET['remove'])) {
    $book_id = intval($_GET['remove']);
    unset($_SESSION['cart'][$book_id]);
    $message = "Item removed from cart.";
}

// Handle checkout
if(isset($_POST['checkout']) && isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $total_price = 0;
    $order_items = array();
    
    foreach($_SESSION['cart'] as $book_id => $quantity) {
        // SECURE QUERY
        $stmt = $conn->prepare("SELECT BookID, BookTitle, Price FROM book WHERE BookID = ?");
        $stmt->bind_param("i", $book_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result && $result->num_rows > 0) {
            $book = $result->fetch_assoc();
            $item_total = $book['Price'] * $quantity;
            $total_price += $item_total;
            $order_items[] = array(
                'book_id' => $book_id,
                'book_title' => $book['BookTitle'],
                'quantity' => $quantity,
                'price' => $book['Price'],
                'subtotal' => $item_total
            );
        }
        $stmt->close();
    }
    
    if(!empty($order_items)) {
        $order_date = date('Y-m-d H:i:s');
        $stmt_order = $conn->prepare("INSERT INTO orders (UserID, OrderDate, TotalPrice, Status) VALUES (?, ?, ?, 'Pending')");
        $stmt_order->bind_param("isd", $user_id, $order_date, $total_price);
        
        if($stmt_order->execute()) {
            $order_id = $conn->insert_id;
            
            foreach($order_items as $item) {
                $stmt_items = $conn->prepare("INSERT INTO order_items (OrderID, BookID, Quantity, Price) VALUES (?, ?, ?, ?)");
                $stmt_items->bind_param("iiid", $order_id, $item['book_id'], $item['quantity'], $item['price']);
                $stmt_items->execute();
            }
            
            unset($_SESSION['cart']);
            $message = "✓ Order placed successfully! Order #" . $order_id;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - Inkspired Book Shop</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="modern-bg">

<header class="main-header">
    <div class="logo"><a href="index.php" style="color: white; text-decoration: none;">Inkspired Book Shop</a></div>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="contact.php">Contact</a>
    </nav>
    <div class="user-actions">
        <?php if(isset($_SESSION['id'])): ?>
            <a href="logout.php" class="btn-login">Logout</a>
        <?php else: ?>
            <a href="signin.php" class="btn-login">Sign In</a>
        <?php endif; ?>
        <a href="cart.php" class="cart-link"><i class="fa fa-shopping-cart"></i> Cart (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)</a>
    </div>
</header>

<div style="padding: 3rem 2rem;">
    <h1>Shopping Cart</h1>
    
    <?php if(!empty($message)): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 1rem; border-radius: 5px;"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <?php if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
        <p>Your cart is empty.</p>
        <a href="shop.php">Continue Shopping</a>
    <?php else: ?>
        <table style="width: 100%;">
            <thead>
                <tr>
                    <th>Book Title</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach($_SESSION['cart'] as $book_id => $quantity):
                    $stmt = $conn->prepare("SELECT BookID, BookTitle, Price FROM book WHERE BookID = ?");
                    $stmt->bind_param("i", $book_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $book = $result->fetch_assoc();
                    if($book):
                        $subtotal = $book['Price'] * $quantity;
                        $total += $subtotal;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($book['BookTitle']); ?></td>
                    <td>₱<?php echo number_format($book['Price'], 2); ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="book_id" value="<?php echo $book['BookID']; ?>">
                            <input type="number" name="quantity" value="<?php echo $quantity; ?>" min="1" style="width: 50px;">
                            <button type="submit" name="update_quantity">Update</button>
                        </form>
                    </td>
                    <td>₱<?php echo number_format($subtotal, 2); ?></td>
                    <td><a href="cart.php?remove=<?php echo $book['BookID']; ?>">Remove</a></td>
                </tr>
                <?php 
                    endif;
                    $stmt->close();
                endforeach; 
                ?>
                <tr>
                    <td colspan="3"><strong>Total:</strong></td>
                    <td><strong>₱<?php echo number_format($total, 2); ?></strong></td>
                </tr>
            </tbody>
        </table>
        <form method="post"><button type="submit" name="checkout">Proceed to Checkout</button></form>
    <?php endif; ?>
</div>
</body>
</html>