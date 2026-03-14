<?php
session_start();
include 'db.php';


$login_error = '';
if(isset($_POST['login'])) {
    $uname = $_POST['username'];
    $pwd = $_POST['pwd'];

    $res = $conn->query("SELECT * FROM users WHERE username = '". $conn->real_escape_string($uname) ."'");
    if($res && $res->num_rows==1) {
        $row = $res->fetch_assoc();
        if(password_verify($pwd, $row['password'])) {
            $_SESSION['id'] = $row['id'];
            header('Location: index.php'); exit;
        }
    }
    $login_error = 'Invalid username or password.';
}


$reg_error = '';
if(isset($_POST['register'])) {
    $first = $conn->real_escape_string($_POST['first_name']);
    $last = $conn->real_escape_string($_POST['last_name']);
    $dob = $conn->real_escape_string($_POST['dob']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $pwd = $_POST['upassword'];
    $pwd2 = $_POST['upassword2'];

    if(strlen($pwd) < 7) {
        $reg_error = 'Password must be at least 7 characters.';
    } elseif($pwd !== $pwd2) {
        $reg_error = 'Passwords do not match.';
    } else {
        
        $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if($check && $check->num_rows > 0) {
            $reg_error = 'Username already taken.';
        } else {
            
            $hashed = password_hash($pwd, PASSWORD_DEFAULT);
            $conn->query("INSERT INTO users (username, password) VALUES ('$username', '$hashed')");
            $uid = $conn->insert_id;
            $name = $first . ' ' . $last;
            
            $conn->query("INSERT INTO customer (CustomerName, CustomerPhone, CustomerEmail, UserID, CustomerDOB) VALUES ('$name', '', '$email', $uid, '$dob')");
            
            $_SESSION['id'] = $uid;
            header('Location: shop.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Inkspired Book Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <div class="brand">
                <span class="name">Inkspired Book Shop</span>
            </div>
            <h1>Create an Account</h1>
            <p class="tagline">Welcome to Inkspired Book Shop</p>
        </div>
        <div class="auth-tabs">
            <button class="tab login-tab">Login</button>
            <button class="tab register-tab active">Register</button>
        </div>
        <form id="loginForm" method="post" action="signin.php" class="auth-form" style="display:none;">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="pwd">Password</label>
                <input type="password" id="pwd" name="pwd" required>
            </div>
            <button type="submit" name="login" class="button button-primary full-width">Login</button>
            <p style="text-align:center; margin-top:1rem; font-size:0.9rem;"><a href="forgot.php">Forgot Password?</a></p>
        </form>
        
        <form id="registerForm" method="post" action="signin.php" class="auth-form">
            <?php if($reg_error): ?>
                <p class="error-message" style="display:block; text-align:center;"><?php echo $reg_error; ?></p>
            <?php endif; ?>
            <div class="row">
                <div class="form-group half">
                    <label for="reg-first">First Name</label>
                    <input type="text" id="reg-first" name="first_name" required>
                </div>
                <div class="form-group half">
                    <label for="reg-last">Last Name</label>
                    <input type="text" id="reg-last" name="last_name" required>
                </div>
            </div>
            <div class="form-group">
                <label for="reg-dob">Date of Birth</label>
                <input type="date" id="reg-dob" name="dob" required>
            </div>
            <div class="form-group">
                <label for="reg-email">Email</label>
                <input type="email" id="reg-email" name="email" required>
            </div>
            <div class="form-group">
                <label for="reg-username">Username</label>
                <input type="text" id="reg-username" name="username" required>
            </div>
            <div class="form-group">
                <label for="reg-password">Password</label>
                <input type="password" id="reg-password" name="upassword" required>
            </div>
            <div class="form-group">
                <label for="reg-password2">Confirm Password</label>
                <input type="password" id="reg-password2" name="upassword2" required>
            </div>
            <button type="submit" name="register" class="button button-primary full-width">Create Account</button>
        </form>
    </div>

<script>
    const loginTab = document.querySelector('.login-tab');
    const registerTab = document.querySelector('.register-tab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    function showLogin() {
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
    }

    function showRegister() {
        registerTab.classList.add('active');
        loginTab.classList.remove('active');
        registerForm.style.display = 'block';
        loginForm.style.display = 'none';
    }

    loginTab.addEventListener('click', showLogin);
    registerTab.addEventListener('click', showRegister);

    
    showRegister();
</script>
</body>
</html>