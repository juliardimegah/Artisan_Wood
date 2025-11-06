<?php
include '../db_connect.php';
include './header.php';

$update_message = '';
// Update status order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    if ($stmt->execute()) {
        $update_message = '<div class="notice success">Status pesanan berhasil diperbarui.</div>';
    } else {
        $update_message = '<div class="notice error">Gagal memperbarui status.</div>';
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
<main class="admin-container">
    <h1>Manage Orders</h1>
    <?= $update_message ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Total</th>
                <th>Status</th>
                <th>Courier</th>
                <th>Delivery Est.</th>
                <th>Action</th>
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
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['courier']) ?></td>
                <td><?= htmlspecialchars($row['delivery_est']) ?></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($row['id']) ?>">
                        <select name="status">
                            <option <?= $row['status'] == 'Belum Dibayar' ? 'selected' : '' ?>>Belum Dibayar</option>
                            <option <?= $row['status'] == 'Sedang Dikemas' ? 'selected' : '' ?>>Sedang Dikemas</option>
                            <option <?= $row['status'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                            <option <?= $row['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            <option <?= $row['status'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                        <button type="submit">Update</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>
<?php include './footer.php'; ?>
