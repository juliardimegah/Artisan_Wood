<?php
include '../db_connect.php';
include './header.php';

// === Update stok otomatis berdasarkan order_items ===
$conn->query("
    UPDATE products p
    JOIN (
        SELECT product_id, SUM(quantity) AS sold_qty
        FROM order_items
        GROUP BY product_id
    ) AS sales ON p.id = sales.product_id
    SET p.stock = GREATEST(p.stock - sales.sold_qty, 0)
");

// === Tambah produk baru ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $stock = $_POST['stock'];

    // Upload gambar
    $image = "";
    if (!empty($_FILES['image']['name'])) {
    $uploadDir = "../assets/products/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    // Biar aman, ubah nama file jadi unik
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
    $targetFile = $uploadDir . $filename;

    // Simpan path absolut dari root (bukan relatif admin)
    $image = "/artisan_wood/assets/products/" . $filename;

    move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);


}


    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsi", $name, $desc, $price, $image, $stock);
    $stmt->execute();
    header("Location: ./products.php");
    exit;
}

// === Hapus produk ===
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE id=$id");
    header("Location: products.php");
    exit;
}

// === Ambil data produk untuk edit ===
$editData = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM products WHERE id=$id");
    $editData = $result->fetch_assoc();
}

// === Simpan hasil edit produk ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $stock = $_POST['stock'];

    $image = $_POST['old_image'];
    if (!empty($_FILES['image']['name'])) {
    $uploadDir = "../assets/products/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $filename = basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $filename;

    $image = "/artisan_wood/assets/products/" . $filename;

    move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
}


    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=?, stock=? WHERE id=?");
    $stmt->bind_param("ssdsii", $name, $desc, $price, $image, $stock, $id);
    $stmt->execute();

    header("Location: ./products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Artisan Wood CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Form Popup */
        .add-form {
            display: none;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            margin-top: 20px;
        }
        .show {
            display: block !important;
        }
        .btn-add {
            background: #8B4513;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-add:hover {
            background: #6e3610;
        }
    </style>
</head>
<body>

<main class="admin-container">
    <h1><i class="fas fa-cube"></i> Manage Products</h1>

    <!-- Tombol Add Product -->
    <button class="btn-add" onclick="toggleAddForm()"><i class="fas fa-plus-circle"></i> Add Product</button>

    <!-- Form Tambah Produk -->
    <div id="addProductForm" class="add-form">
        <form method="POST" enctype="multipart/form-data" class="product-form">
            <h3>Add New Product</h3>
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="number" step="0.01" name="price" placeholder="Price" required>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="number" name="stock" placeholder="Stock" required>
            <label for="image">Product Image:</label>
            <input type="file" name="image" accept="image/*">
            <button type="submit" name="add_product">Add Product</button>
        </form>
    </div>

    <!-- Form Edit Produk -->
    <?php if ($editData): ?>
    <div class="add-form show">
        <form method="POST" enctype="multipart/form-data" class="product-form">
            <h3>Edit Product</h3>
            <input type="hidden" name="id" value="<?= $editData['id'] ?>">
            <input type="hidden" name="old_image" value="<?= htmlspecialchars($editData['image']) ?>">

            <input type="text" name="name" placeholder="Product Name" value="<?= $editData['name'] ?>" required>
            <input type="number" step="0.01" name="price" placeholder="Price" value="<?= $editData['price'] ?>" required>
            <textarea name="description"><?= $editData['description'] ?></textarea>
            <input type="number" name="stock" placeholder="Stock" value="<?= $editData['stock'] ?>" required>

            <label for="image">Product Image:</label>
            <input type="file" name="image" accept="image/*">
            <?php if (!empty($editData['image'])): ?>
                <p>Current: <img src="../assets/products/<?= htmlspecialchars($editData['image']) ?>" height="60"></p>
            <?php endif; ?>

            <button type="submit" name="update_product">Update Product</button>
        </form>
    </div>
    <?php endif; ?>

    <hr>

    <!-- TABEL PRODUK -->
    <h2>Product List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
        <?php
        $res = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
        while ($row = $res->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td>
                <?php if (!empty($row['image'])): ?>
                    <img src="../assets/products/<?= htmlspecialchars($row['image']) ?>" alt="" height="50">
                <?php else: ?>
                    <em>No image</em>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>Rp<?= number_format($row['price'], 0, ',', '.') ?></td>
            <td><?= $row['stock'] ?></td>
            <td>
                <a href="?edit=<?= $row['id'] ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus produk ini?')" class="btn-delete"><i class="fas fa-trash"></i> Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</main>

<?php include './footer.php'; ?>

<script>
function toggleAddForm() {
    const form = document.getElementById('addProductForm');
    form.classList.toggle('show');
}
</script>
</body>
</html>
