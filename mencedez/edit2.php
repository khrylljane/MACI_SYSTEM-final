<?php
// edit.php
// Mobile Accessories Inventory System

include 'db.php';

$message = "";

// Check if ID exists
if (!isset($_GET['id'])) {
    die("Invalid Request.");
}

$id = intval($_GET['id']);

// Fetch existing data
$sql = "SELECT * FROM accessories WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    die("Accessory not found.");
}

$row = mysqli_fetch_assoc($result);

// Update data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_name = trim($_POST['product_name']);
    $category = trim($_POST['category']);
    $brand = trim($_POST['brand']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $supplier = trim($_POST['supplier']);

    // Validation
    if (
        empty($product_name) ||
        empty($category) ||
        empty($brand) ||
        empty($supplier) ||
        $quantity < 0 ||
        $price < 0
    ) {
        $message = "Please fill in all fields correctly.";
    } else {

        // Update query
        $update = "UPDATE accessories 
                   SET product_name = ?, 
                       category = ?, 
                       brand = ?, 
                       quantity = ?, 
                       price = ?, 
                       supplier = ?
                   WHERE id = ?";

        $stmt_update = mysqli_prepare($conn, $update);

        mysqli_stmt_bind_param(
            $stmt_update,
            "sssidsi",
            $product_name,
            $category,
            $brand,
            $quantity,
            $price,
            $supplier,
            $id
        );

        if (mysqli_stmt_execute($stmt_update)) {
            $message = "Accessory updated successfully!";

            // Refresh data
            $sql = "SELECT * FROM accessories WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

        } else {
            $message = "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt_update);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Accessory</title>

    <style>
        body{
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container{
            width: 450px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }

        h2{
            text-align: center;
            margin-bottom: 20px;
        }

        input, select{
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button{
            width: 100%;
            padding: 12px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover{
            background: #218838;
        }

        .message{
            text-align: center;
            margin-bottom: 15px;
            color: green;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Edit Mobile Accessory</h2>

    <?php if($message != ""): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">

        <label>Product Name</label>
        <input 
            type="text" 
            name="product_name" 
            value="<?php echo htmlspecialchars($row['product_name']); ?>" 
            required
        >

        <label>Category</label>
        <select name="category" required>

            <option value="Charger"
                <?php if($row['category'] == 'Charger') echo 'selected'; ?>>
                Charger
            </option>

            <option value="Earphones"
                <?php if($row['category'] == 'Earphones') echo 'selected'; ?>>
                Earphones
            </option>

            <option value="Power Bank"
                <?php if($row['category'] == 'Power Bank') echo 'selected'; ?>>
                Power Bank
            </option>

            <option value="Phone Case"
                <?php if($row['category'] == 'Phone Case') echo 'selected'; ?>>
                Phone Case
            </option>

            <option value="USB Cable"
                <?php if($row['category'] == 'USB Cable') echo 'selected'; ?>>
                USB Cable
            </option>

            <option value="Bluetooth Speaker"
                <?php if($row['category'] == 'Bluetooth Speaker') echo 'selected'; ?>>
                Bluetooth Speaker
            </option>

        </select>

        <label>Brand</label>
        <input 
            type="text" 
            name="brand" 
            value="<?php echo htmlspecialchars($row['brand']); ?>" 
            required
        >

        <label>Quantity</label>
        <input 
            type="number" 
            name="quantity" 
            min="0"
            value="<?php echo $row['quantity']; ?>" 
            required
        >

        <label>Price</label>
        <input 
            type="number" 
            step="0.01"
            name="price" 
            min="0"
            value="<?php echo $row['price']; ?>" 
            required
        >

        <label>Supplier</label>
        <input 
            type="text" 
            name="supplier" 
            value="<?php echo htmlspecialchars($row['supplier']); ?>" 
            required
        >

        <button type="submit">Update Accessory</button>

    </form>

</div>

</body>
</html>