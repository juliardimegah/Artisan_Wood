<?php
// Semua logika PHP (koneksi DB, upload, CRUD) diletakkan di bagian atas.
include '../db_connect.php';

$error_message = '';
$success_message = '';

// Fungsi untuk upload gambar ke ImgBB
function upload_to_imgbb($tmp_name) {
    $api_key = 'dc307ddb69cafa807175633a7cf4d439'; // Harap ganti dengan API key Anda jika perlu
    if (empty($api_key)) {
        return null; // Kembalikan null jika API key tidak ada
    }
    $url = 'https://api.imgbb.com/1/upload';
    $img_data = base64_encode(file_get_contents($tmp_name));
    $data = http_build_query(['key' => $api_key, 'image' => $img_data]);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FAILONERROR, true); 

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($http_code >= 400 || $err) { 
        // Log error jika perlu, tapi kembalikan null ke user
        return null; 
    }
    
    $result = json_decode($response, true);
    return $result['data']['url'] ?? null;
}

// FUNGSI UPDATE ATAU TAMBAH PRODUK
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $is_update = isset($_POST['id']) && !empty($_POST['id']);
    
    // Ambil data dari form
    $id = $is_update ? $_POST['id'] : null;
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $stock = $_POST['stock'];
    $image_url = $_POST['old_image'] ?? ''; // Ambil gambar lama sebagai default

    // Proses upload gambar baru jika ada
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $new_image_url = upload_to_imgbb($_FILES['image']['tmp_name']);
        if ($new_image_url) {
            $image_url = $new_image_url;
        } else {
            $error_message = "Gagal mengupload gambar. Pastikan API Key ImgBB valid.";
        }
    }

    if (empty($error_message)) {
        if ($is_update) {
            // LOGIKA UPDATE
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=?, stock=? WHERE id=?");
            $stmt->bind_param("ssdsii", $name, $desc, $price, $image_url, $stock, $id);
            if ($stmt->execute()) {
                $success_message = "Produk berhasil diperbarui!";
            } else {
                $error_message = "Gagal memperbarui produk.";
            }
        } else {
            // LOGIKA TAMBAH
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsi", $name, $desc, $price, $image_url, $stock);
            if ($stmt->execute()) {
                $success_message = "Produk baru berhasil ditambahkan!";
            } else {
                $error_message = "Gagal menambahkan produk.";
            }
        }
    }
    // Redirect untuk menghindari resubmit, namun dengan pesan, lebih baik tampilkan saja di halaman
    // header("Location: products.php"); exit;
}

// FUNGSI HAPUS PRODUK
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: products.php?status=deleted"); exit;
    }
}

// Ambil data produk untuk form edit
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT id, name, description, price, stock, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_product = $result->fetch_assoc();
}

// Logika untuk Search, Filter, dan Sorting
$search = $_GET['search'] ?? '';
$sort_column = $_GET['sort'] ?? 'id';
$sort_order = $_GET['order'] ?? 'asc';

$sql = "SELECT id, name, price, stock, image, created_at FROM products";
$params = [];
$types = '';

if ($search) {
    $sql .= " WHERE name LIKE ? OR id = ?";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search;
    $types .= 'si';
}

$allowed_columns = ['id', 'name', 'price', 'stock'];
if (in_array($sort_column, $allowed_columns)) {
    $sql .= " ORDER BY $sort_column";
    if ($sort_order === 'desc') {
        $sql .= " DESC";
    } else {
        $sql .= " ASC";
    }
} else {
	$sql .= " ORDER BY id ASC";
}

$stmt_products = $conn->prepare($sql);
if ($search) {
    $stmt_products->bind_param($types, ...$params);
}
$stmt_products->execute();
$products_result = $stmt_products->get_result();


// Tentukan judul form berdasarkan mode (edit atau tambah)
$form_title = $edit_product ? 'Edit Product' : 'Add New Product';
$form_button_text = $edit_product ? 'Update Product' : 'Add Product';

function sort_url($column, $current_column, $current_order) {
    $order = ($current_column == $column && $current_order == 'asc') ? 'desc' : 'asc';
    $search_query = '';
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_query = '&search=' . urlencode($_GET['search']);
    }
    return "?sort=$column&order=$order" . $search_query;
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

    <?php if ($success_message): ?><div class="alert success"><?= $success_message ?></div><?php endif; ?>
    <?php if ($error_message): ?><div class="alert error"><?= $error_message ?></div><?php endif; ?>

    <div id="productFormContainer" class="product-form">
        <h2><?= $form_title ?></h2>
        <form method="POST" action="products.php" enctype="multipart/form-data">
            
            <!-- Input tersembunyi untuk ID, ini kuncinya! -->
            <input type="hidden" name="id" value="<?= htmlspecialchars($edit_product['id'] ?? '') ?>">
            <input type="hidden" name="old_image" value="<?= htmlspecialchars($edit_product['image'] ?? '') ?>">

            <input type="text" name="name" placeholder="Product Name" value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>" required>
            <textarea name="description" placeholder="Product Description" rows="4" required><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea>
            <input type="number" name="price" placeholder="Price (Rp)" value="<?= htmlspecialchars($edit_product['price'] ?? '') ?>" required>
            <input type="number" name="stock" placeholder="Stock" value="<?= htmlspecialchars($edit_product['stock'] ?? '') ?>" required>
            
            <label>Product Image:</label>
            <input type="file" name="image" accept="image/*">
            <?php if ($edit_product && !empty($edit_product['image'])): ?>
                <p style="margin-top: 10px;">Current Image: <img src="<?= htmlspecialchars($edit_product['image']) ?>" height="80" style="vertical-align: middle; border-radius: 5px;"></p>
            <?php endif; ?>
            
            <button type="submit"><?= $form_button_text ?></button>
            <?php if ($edit_product): ?>
                <a href="products.php" class="btn-cancel">Cancel Edit</a>
            <?php endif; ?>
        </form>
    </div>

    <hr style="margin: 30px 0;">

    <h2>Product List</h2>

    <!-- Search Form -->
    <form method="GET" action="products.php" class="search-form">
        <input type="text" name="search" placeholder="Search by ID or Name..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit"><i class="fas fa-search"></i> Search</button>
    </form>


    <table>
        <thead>
            <tr>
                <th><a href="<?= sort_url('id', $sort_column, $sort_order) ?>">ID <i class="fas fa-sort"></i></a></th>
                <th>Image</th>
                <th><a href="<?= sort_url('name', $sort_column, $sort_order) ?>">Name <i class="fas fa-sort"></i></a></th>
                <th><a href="<?= sort_url('price', $sort_column, $sort_order) ?>">Price <i class="fas fa-sort"></i></a></th>
                <th><a href="<?= sort_url('stock', $sort_column, $sort_order) ?>">Stock <i class="fas fa-sort"></i></a></th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $products_result->fetch_assoc()): ?>
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
