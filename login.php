<?php
session_start();
require('connection.php');

if (isset($_POST['username']) and isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' and password='$password' ";
    $result = mysqli_query($connection, $query) or die(mysqli_error($connection));
    $count = mysqli_num_rows($result);

    if ($count == 1) {
        $_SESSION['username'] = $username;
        header('location: index.php');
    } else {
        $fmsg = "Error !";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/4/flatly/bootstrap.min.css" />
    <link rel="stylesheet" href="style.css">
    <title>Login In</title>
</head>

<body>

    <header class="bg-primary">
        <ul>
            <li class="logo"><a href="/index.php">List App</a></li>
            <?php if (isset($_SESSION['username'])) { ?><li class="login"><a href="/logout.php"><?php echo "Sign Out" ?></a></li><?php } else { ?>
                <li class="login"><a href="/login.php"><?php echo "Sign In" ?></a></li><?php } ?>
        </ul>
    </header>

    <div class="sign-in">
        <h2>Sign In</h2>
        <form method="POST" action="login.php">
            <input type="text" class="form-control" name="username" placeholder="Enter name" required>
            <input type="password" class="form-control" name="password" placeholder="Password" required>
            <button type="submit" class="btn btn-outline-success">Sign In</button>
        </form>
    </div>

</body>

</html>