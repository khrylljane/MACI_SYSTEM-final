<?php
require 'include/db.php';

$message = "";

if (isset($_POST['submit'])) {

    // Sanitize inputs
    $name = htmlspecialchars($_POST['name']);
    $category = htmlspecialchars($_POST['category']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $brand = htmlspecialchars($_POST['brand']);

    $image = "";

    // Handle Image Upload
    if (!empty($_FILES['image']['name'])) {

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $fileTmp = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {

            if (!is_dir("uploads")) {
                mkdir("uploads", 0777, true);
            }

            $image = time() . "_" . $filename;
            move_uploaded_file($fileTmp, "uploads/" . $image);

        } else {
            $message = "<div class='alert alert-danger'>Invalid image format. Only JPG, PNG, GIF allowed.</div>";
        }
    }

    // Insert using prepared statement
    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO products (name, category, price, quantity, brand, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdiss", $name, $category, $price, $quantity, $brand, $image);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            $message = "<div class='alert alert-danger'>Error saving product.</div>";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Inventory Table</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            padding: 40px;
            font-family: Arial, sans-serif;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0px 15px 40px rgba(0,0,0,0.4);
            border: 1px solid rgba(255,255,255,0.2);
        }

        h2 {
            color: white;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.5);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
        }

        .table thead {
            background: rgba(0, 0, 0, 0.3);
            color: white;
            font-size: 16px;
        }

        .table tbody tr {
            background: rgba(255, 255, 255, 0.85);
            transition: 0.3s ease-in-out;
        }

        /* 3D Hover Effect */
        .table tbody tr:hover {
            transform: scale(1.02);
            box-shadow: 0px 10px 25px rgba(0,0,0,0.3);
            background: white;
        }

        .table td, .table th {
            padding: 15px;
            text-align: center;
            vertical-align: middle;
        }

        /* 3D Buttons */
        .btn {
            border-radius: 20px;
            padding: 6px 14px;
            font-weight: bold;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.25);
            transition: 0.3s;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0px 10px 20px rgba(0,0,0,0.35);
        }

        /* Product Image */
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0px 6px 15px rgba(0,0,0,0.3);
        }

    </style>
</head>

<body>

<div class="container">
    <h2>Mobile Inventory Management</h2>

    <div class="table-container">

        <div class="d-flex justify-content-between mb-3">
            <a href="add_product.php" class="btn btn-success">➕ Add Product</a>
        </div>

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Brand</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php
                require 'include/db.php';

                $result = $conn->query("SELECT * FROM products ORDER BY id DESC");

                while ($row = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?= $row['id']; ?></td>

                        <td>
                            <?php if (!empty($row['image'])) { ?>
                                <img src="uploads/<?= $row['image']; ?>" class="product-img">
                            <?php } else { ?>
                                <span>No Image</span>
                            <?php } ?>
                        </td>

                        <td><?= $row['name']; ?></td>
                        <td><?= $row['category']; ?></td>
                        <td>₱<?= number_format($row['price'], 2); ?></td>
                        <td><?= $row['quantity']; ?></td>
                        <td><?= $row['brand']; ?></td>

                        <td>
                            <a href="create.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">✏ Edit</a>
                            <a href="delete.php?id=<?= $row['id']; ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Are you sure you want to delete this product?');">
                               🗑 Delete
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

    </div>
</div>

</body>
</html>