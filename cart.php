
<?php
session_start();
include "config.php";

// Validate session
$user_id = $_SESSION['user_id'];

// Handle invalid session
if (!isset($user_id)) {
    session_destroy();
    header('location: login.php');
    exit();
}

// Logout function
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('location: login.php');
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Add'])) {
        $modify_quantity = $_POST['modify_quantity'];
        $cart_name = $_POST['cart_name'];

        // Validate and update cart
        if (!empty($user_id) && !empty($cart_name) && is_numeric($modify_quantity) && $modify_quantity > 0) {
            // Prepare and bind the SQL statement
            $update_query = "UPDATE cart SET quantity = quantity + ? WHERE name = ? AND id = ?";
            $stmt = mysqli_prepare($conn, $update_query);

            // Bind parameters
            mysqli_stmt_bind_param($stmt, "iss", $modify_quantity, $cart_name, $user_id);

            // Execute the statement
            $update_result = mysqli_stmt_execute($stmt);

            // Check for errors
            if (!$update_result) {
                echo "Error updating cart: " . mysqli_error($conn);
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            echo "Invalid data submitted";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CART</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
        crossorigin="anonymous">
</head>

<body>

    <!-- Trying to fetch user name that is logged in -->
    <?php
    $select = mysqli_query($conn, "SELECT * FROM user_form WHERE id = '$user_id'") or die("connection failed");
    if (mysqli_num_rows($select) > 0) {
        $row = mysqli_fetch_assoc($select);
    }
    ?>

    <h1>
        <?php echo $row['name'] ?>'s cart
    </h1>
    <h1>
        <?php echo $user_id; ?>
        <a href="login.php" class="btn">Logout</a> <h1>

        <?php $select_product_details = mysqli_query($conn, "SELECT cart.name as cart_name, cart.price as cart_price, cart.quantity as cart_quantity, products.image as product_image FROM products JOIN cart ON products.name = cart.name WHERE cart.user_id = '$user_id'") or die('query failed'); ?>
        <!-- TABLE DESCRIPTION -->

       
        <form method="post" action="">
    <table class="table table-sm table-light">
        <thead>
            <tr>
                <th scope="col">Product Name</th>
                <th scope="col">Image</th>
                <th scope="col">Price</th>
                <th scope="col">Qty</th>
                <th scope="col">Modify Qty</th>
                <th scope="col">Total Price</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>

        <!-- ITEMIZATION STARTS FROM HERE -->

        <tbody>
            <?php
            if (mysqli_num_rows($select_product_details) > 0) {
                while ($fetch_product_details = mysqli_fetch_assoc($select_product_details)) {
                    $imageData = base64_encode($fetch_product_details['product_image']);
                    $src = 'data:image/png;base64,' . $imageData;
                    ?>
                    <tr>
                        <td><?php echo $fetch_product_details['cart_name']; ?></td>
                        <td><img src="<?php echo $src; ?>" alt="Product Image"></td>
                        <td><?php echo $fetch_product_details['cart_price']; ?></td>
                        <td><?php echo $fetch_product_details['cart_quantity']; ?></td>
                        <td>
                            <input type="number" min="1" name="modify_quantity[<?php echo $fetch_product_details['cart_name']; ?>]" value="1">
                        </td>
                        <td><?php echo $fetch_product_details['cart_price'] * $fetch_product_details['cart_quantity']; ?></td>
                        <td>
                            <input type="hidden" name="cart_name[]" value="<?php echo $fetch_product_details['cart_name']; ?>">
                        </td>
                    </tr>
                    <?php
                }
                ?>
                <!-- Hidden input field for user_id -->
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                <!-- Submit buttons -->
                <tr>
                    <td colspan="6">
                        <input type="submit" value="Add" name="Add" class="btn">
                        <input type="submit" value="Remove" name="Remove" class="btn">
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <td colspan="6">
                        <h1>No items in Cart</h1>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</form>
            </body>