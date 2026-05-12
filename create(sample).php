<?php
// create.php
// Mobile Accessories Inventory System
// Make sure your database connection file is named db.php

include 'db.php';

$message = "";

// Create item when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $product_name = trim($_POST['product_name']);
    $category = trim($_POST['category']);
    $brand = trim($_POST['brand']);
    $quantity = intval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $supplier = trim($_POST['supplier']);

    // Basic validation
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

        // Insert data into database
        $sql = "INSERT INTO accessories 
                (product_name, category, brand, quantity, price, supplier)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sssids",
            $product_name,
            $category,
            $brand,
            $quantity,
            $price,
            $supplier
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Accessory added successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Accessory</title>

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
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover{
            background: #0056b3;
        }

        .message{
            text-align: center;
            margin-bottom: 15px;
            color: green;
        }

        .error{
            color: red;
        }
    </style>
</head>
<body>

<div class="container">

    <h2>Add Mobile Accessory</h2>

    <?php if($message != ""): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">

        <label>Product Name</label>
        <input type="text" name="product_name" required>

        <label>Category</label>
        <select name="category" required>
            <option value="">-- Select Category --</option>
            <option value="Charger">Charger</option>
            <option value="Earphones">Earphones</option>
            <option value="Power Bank">Power Bank</option>
            <option value="Phone Case">Phone Case</option>
            <option value="USB Cable">USB Cable</option>
            <option value="Bluetooth Speaker">Bluetooth Speaker</option>
        </select>

        <label>Brand</label>
        <input type="text" name="brand" required>

        <label>Quantity</label>
        <input type="number" name="quantity" min="0" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" min="0" required>

        <label>Supplier</label>
        <input type="text" name="supplier" required>

        <button type="submit">Save Accessory</button>

    </form>

</div>

</body>
</html>