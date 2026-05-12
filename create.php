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
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .card {
            border-radius: 20px;
            border: none;
        }

        .card-header {
            background: transparent;
            font-size: 22px;
            font-weight: bold;
            text-align: center;
        }

        .form-control {
            border-radius: 10px;
        }

        .btn-primary {
            border-radius: 30px;
            font-weight: 500;
        }

        .btn-secondary {
            border-radius: 30px;
        }

        .preview-img {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>

<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg p-4">

                <div class="card-header">
                    ➕ Add New Product
                </div>

                <?php echo $message; ?>

                <form method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" class="form-control" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(event)" required>
                        <img id="preview" class="preview-img">
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary w-100">
                        💾 Save Product
                    </button>

                    <a href="index.php" class="btn btn-secondary w-100 mt-3">
                        ⬅ Back to Inventory
                    </a>

                </form>

            </div>
        </div>
    </div>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function(){
        const output = document.getElementById('preview');
        output.src = reader.result;
        output.style.display = "block";
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>