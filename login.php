<html>
<head>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <blockquote>
        <a href="index.php"><img src="image/logo.png" width="50px"></a>
    </blockquote>
</header>

<blockquote>
    <div class="container">
        <center><h1>Login</h1></center>
        <form action="checklogin.php" method="post">
            Username:<br><input type="text" name="username" required/>
            <br><br>
            Password:<br><input type="password" name="pwd" required/>
            <br><br>
            Role:<br>
            <select name="role" required style="padding:0.5rem; border:1px solid #ccc; border-radius:4px;">
                <option value="user" selected>User</option>
                <option value="admin">Admin</option>
            </select>
            <br><br>
            <input class="button" type="submit" value="Login"/>
            <input class="button" type="button" value="Cancel" onClick="window.location='index.php';" />
        </form>

        <?php
        if (isset($_GET['errcode'])) {
            $errorMessages = [
                1 => 'Invalid username or password.',
                2 => 'Please login.'
            ];
            $errcode = intval($_GET['errcode']);
            if (isset($errorMessages[$errcode])) {
                echo '<p class="error-message" style="text-align: center;">' . $errorMessages[$errcode] . '</p>';
            }
        }
        ?>

        <a href="#" class="secondary-link">Forgot Password?</a>
    </div>
</blockquote> </body>
</html>