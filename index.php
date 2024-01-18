<?php
include "config.php";

session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit();
}

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location:login.php');
    exit();
}

if (isset($_GET['my_cart'])) {
    header('location:cart.php');
}

// Record order
if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];
    $product_id = $_POST['product_id'];
    

    // Product already existing for a particular user
    $select_cart = mysqli_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

    if (mysqli_num_rows($select_cart) > 0) {
        $message[] = 'product added already';
    } else {
        mysqli_query($conn, "INSERT INTO cart(id, user_id, name, price, quantity) VALUES('$product_id','$user_id','$product_name','$product_price','$product_quantity')") or die('query failed');
        $message[] = 'product added to cart';
    }
}

// Display latest products and cart images
$select_product = mysqli_query($conn, "SELECT * FROM products") or die('query failed');
$select_photo = mysqli_query($conn, "SELECT products.image AS product_image FROM products JOIN cart ON products.id = cart.id WHERE cart.user_id = '$user_id'") or die('query failed');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping cart</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
    <?php
    if (isset($message)) {
        foreach ($message as $message) {
            echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
        }
    }
    ?>

    <div class="container">

        <div class="user-profile">
            <?php
            $select_user = mysqli_query($conn, "SELECT * FROM user_form WHERE id = '$user_id'") or die('query failed');
            if (mysqli_num_rows($select_user) > 0) {
                $fetch_user = mysqli_fetch_assoc($select_user);
            }
            ?>

            <p> username: <span>
                    <?php echo $fetch_user['name']; ?>
                </span> </p>

            <p> email: <span>
                    <?php echo $fetch_user['email']; ?>
                </span> </p>

            <div class="flex">
                <a href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('are you sure you want to logout');" class="delete-btn">logout </a>
            </div>
        </div>

        <div class="products">
            <h1 class="heading">Latest Products</h1>
            <div class="box-container">
                <?php
                while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                ?>
                    <form method="post" class="box" action="">
                        <?php
                        $imageData = base64_encode($fetch_product['image']);
                        $src = 'data:image/png;base64,' . $imageData;
                        ?>
                        <img src="<?php echo $src; ?>" alt="Product Image">
                        <div class="name"><?php echo $fetch_product['name']; ?></div>
                        <div class="price"><?php echo "Rs " . $fetch_product['price']; ?></div>
                        <input type="number" min="1" name="product_quantity" value="1">
                        <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
                        <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
                        <input type="hidden" name="product_id" value="<?php echo $fetch_product['id']; ?>">
                        <input type="submit" value="add_to_cart" name="add_to_cart" class="btn">
                        
                    </form>
                <?php } ?>
                
                
            </div>
        </div>

                </div>
                <a href="cart.php" class="btn" id="mc">My Cart</a>

</body>

</html>