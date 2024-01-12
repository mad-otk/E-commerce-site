<?php

include 'config.php';

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpassword = mysqli_real_escape_string($conn, md5($_POST['cpassword']));

    $select = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email' AND password = '$password'") or die('query failed');

    if (mysqli_num_rows($select) > 0) {
        $message[] = 'user already exist!';
    } else {
        mysqli_query($conn, "INSERT INTO user_form(name, email, password) VALUES('$name','$email', '$password')") or die('query failed');
        $message[] = 'registered successfully';
        header('location:login.php');
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
        }
    }

    ?>

    <div class="form-container">
        <form action="" method="post">
            <h3>Register now</h3>
            <input type="text" name="name" required placeholder="Enter your name" class="box">
            <input type="email" name="email" required placeholder="Enter email" class="box">
            <input type="password" name="password" required placeholder="Enter password" class="box">
            <input type="password" name="cpassword" required placeholder="Confirm password" class="box">
            <input type="submit" name="submit" class="btn" value="Register now">
            <p>Already registered? <a href="login.php">Login now</a></p>
        </form>

        <script src="" async defer></script>
</div>
</body>

</html>