<?php
// Semua logika PHP (koneksi DB, upload, CRUD) diletakkan di bagian atas.
include '../db_connect.php';

// Fungsi untuk upload gambar ke ImgBB
function upload_to_imgbb($tmp_name) {
    $api_key = 'dc307ddb69cafa807175633a7cf4d439'; // Harap ganti dengan API key Anda jika perlu
    $url = 'https://api.imgbb.com/1/upload';
    $img_data = base64_encode(file_get_contents($tmp_name));
    $data = http_build_query(['key' => $api_key, 'image' => $img_data]);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) { return null; }
    else { 
        $result = json_decode($response, true);
        return $result['data']['url'] ?? null;
    }
}

// FUNGSI TAMBAH PRODUK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name']; $price = $_POST['price']; $desc = $_POST['description']; $stock = $_POST['stock'];
    $image_url = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_url = upload_to_imgbb($_FILES['image']['tmp_name']);
    }
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsi", $name, $desc, $price, $image_url, $stock);
    $stmt->execute();
    header("Location: products.php"); exit;
}

// FUNGSI UPDATE PRODUK
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['id']; $name = $_POST['name']; $price = $_POST['price']; $desc = $_POST['description']; $stock = $_POST['stock'];
    $image_url = $_POST['old_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $new_image_url = upload_to_imgbb($_FILES['image']['tmp_name']);
        if ($new_image_url) { $image_url = $new_image_url; }
    }
    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=?, stock=? WHERE id=?");
    $stmt->bind_param("ssdsii", $name, $desc, $price, $image_url, $stock, $id);
    $stmt->execute();
    header("Location: products.php"); exit;
}

// FUNGSI HAPUS PRODUK
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: products.php"); exit;
}

// Ambil data produk untuk form edit
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_product = $result->fetch_assoc();
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
</head>
<body>

<?php include './header.php'; ?>

<main class="admin-container">
    <h1><i class="fas fa-cube"></i> Manage Products</h1>

    <div id="productFormContainer" class="product-form">
        <h2><?= $edit_product ? 'Edit Product' : 'Add New Product' ?></h2>
        <form method="POST" action="products.php" enctype="multipart/form-data">
            <?php if ($edit_product): ?><input type="hidden" name="id" value="<?= $edit_product['id'] ?>"><?php endif; ?>
            <input type="text" name="name" placeholder="Product Name" value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>" required>
            <textarea name="description" placeholder="Product Description" rows="4" required><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea>
            <input type="number" name="price" placeholder="Price (Rp)" value="<?= $edit_product['price'] ?? '' ?>" required>
            <input type="number" name="stock" placeholder="Stock" value="<?= $edit_product['stock'] ?? '' ?>" required>
            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*">
            <input type="hidden" name="old_image" value="<?= htmlspecialchars($edit_product['image'] ?? '') ?>">
            <?php if ($edit_product && !empty($edit_product['image'])): ?>
                <p style="margin-top: 10px;">Current Image: <img src="<?= htmlspecialchars($edit_product['image']) ?>" height="80" style="vertical-align: middle; border-radius: 5px;"></p>
            <?php endif; ?>
            <button type="submit" name="<?= $edit_product ? 'update_product' : 'add_product' ?>"><?= $edit_product ? 'Update Product' : 'Add Product' ?></button>
        </form>
    </div>

    <hr style="margin: 30px 0;">

    <h2>Product List</h2>
    <table>
        <thead>
            <tr><th>ID</th><th>Image</th><th>Name</th><th>Price</th><th>Stock</th><th>Action</th></tr>
        </thead>
        <tbody>
            <?php
            $res = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" style="width: 80px; height: auto; border-radius: 5px;"></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td>Rp<?= number_format($row['price'], 0, ',', '.') ?></td>
                <td><?= $row['stock'] ?></td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>#productFormContainer" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus produk ini?')" class="btn-delete"><i class="fas fa-trash"></i> Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<?php include './footer.php'; ?>
</body>
</html>
