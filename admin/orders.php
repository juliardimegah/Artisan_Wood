<?php
include '../db_connect.php';

$update_message = '';
// Update status order jika ada POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        // Pesan sukses sementara, bisa diganti dengan notifikasi yang lebih baik
        $update_message = '<div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">Status pesanan #'.$order_id.' berhasil diperbarui.</div>';
    } else {
        $update_message = '<div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">Gagal memperbarui status.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders - Artisan Wood CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include './header.php'; ?>

<main class="admin-container">
    <h1><i class="fas fa-box"></i> Manage Orders</h1>
    
    <?= $update_message; // Tampilkan pesan update di sini ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Total</th>
                <th>Status</th>
                <th>Kurir</th>
                <th>Tgl. Pesan</th>
                <th style="width: 250px;">Update Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
            while ($row = $result->fetch_assoc()):
            ?>
            <tr>
                <td><?= htmlspecialchars($row['id']) ?></td>
                <td><?= htmlspecialchars($row['user_id']) ?></td>
                <td>Rp<?= number_format($row['total_amount'], 0, ',', '.') ?></td>
                <td><span class="status <?= strtolower(str_replace(' ', '-', $row['status'])) ?>"><?= htmlspecialchars($row['status']) ?></span></td>
                <td><?= htmlspecialchars($row['courier']) ?></td>
                <td><?= date('d M Y', strtotime($row['order_date'])) ?></td>
                <td>
                    <form method="POST" style="display:flex; align-items:center; gap: 8px;">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id']) ?>">
                        <select name="status" style="padding: 5px; border-radius: 5px;">
                            <option value="Belum Dibayar" <?= $row['status'] == 'Belum Dibayar' ? 'selected' : '' ?>>Belum Dibayar</option>
                            <option value="Sedang Dikemas" <?= $row['status'] == 'Sedang Dikemas' ? 'selected' : '' ?>>Sedang Dikemas</option>
                            <option value="Dikirim" <?= $row['status'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option value="Selesai" <?= $row['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option value="Dibatalkan" <?= $row['status'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                        <button type="submit" style="padding: 6px 12px; font-weight: bold;">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<?php include './footer.php'; ?>
</body>
</html>
