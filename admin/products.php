<?php
include '../db_connect.php';
include './header.php';

// === FUNGSI UNTUK UPLOAD GAMBAR KE IMGBB ===
function upload_to_imgbb($tmp_name) {
    $api_key = 'dc307ddb69cafa807175633a7cf4d439';
    $url = 'https://api.imgbb.com/1/upload';

    // Encode gambar ke base64
    $img_data = base64_encode(file_get_contents($tmp_name));

    // Persiapkan data untuk POST request
    $data = http_build_query([
        'key' => $api_key,
        'image' => $img_data
    ]);

    // Gunakan cURL untuk mengirim gambar
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return null; // Gagal upload
    } else {
        $result = json_decode($response, true);
        if (isset($result['data']['url'])) {
            return $result['data']['url']; // Berhasil, kembalikan URL gambar
        }
    }
    return null;
}

// === TAMBAH PRODUK BARU ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $stock = $_POST['stock'];
    $image_url = ""; // Default URL kosong

    // Jika ada file gambar diupload, kirim ke ImgBB
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_url = upload_to_imgbb($_FILES['image']['tmp_name']);
    }

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image, stock) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsi", $name, $desc, $price, $image_url, $stock);
    $stmt->execute();
    header("Location: ./products.php");
    exit;
}

// === EDIT PRODUK ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $stock = $_POST['stock'];
    $image_url = $_POST['old_image']; // Ambil URL gambar yang lama

    // Jika ada gambar baru diupload, ganti dengan yang baru
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $new_image_url = upload_to_imgbb($_FILES['image']['tmp_name']);
        if ($new_image_url) {
            $image_url = $new_image_url;
        }
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=?, stock=? WHERE id=?");
    $stmt->bind_param("ssdsii", $name, $desc, $price, $image_url, $stock, $id);
    $stmt->execute();

    header("Location: ./products.php");
    exit;
}

// (Sisa logika seperti hapus dan ambil data edit tetap sama)
// ...

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- ... (head content) ... -->
    <style>
        /* PERBAIKAN: Pastikan gambar di tabel admin tidak pecah */
        table img {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<main class="admin-container">
    <!-- ... (Form dan tabel) ... -->
    <h2>Product List</h2>
    <table>
        <!-- ... (header tabel) ... -->
        <?php
        $res = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
        while ($row = $res->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td>
                <?php if (!empty($row['image'])): ?>
                    <!-- PERBAIKAN: Langsung gunakan URL dari database -->
                    <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['name']) ?>" height="50">
                <?php else: ?>
                    <em>No image</em>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td>Rp<?= number_format($row['price'], 0, ',', '.') ?></td>
            <td><?= $row['stock'] ?></td>
            <td>
                 <a href="?edit=<?= $row['id'] ?>#productFormContainer" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                 <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus produk ini?')" class="btn-delete"><i class="fas fa-trash"></i> Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</main>
<!-- ... (sisa body) ... -->
</body>
</html>
