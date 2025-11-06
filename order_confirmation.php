<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: /index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
// Ambil ID pesanan numerik dari string 'ORDER-XXX'
$order_id_str = $_GET['order_id'];
$order_id = intval(str_replace('ORDER-', '', $order_id_str));

// Ambil detail pesanan
$order_query = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$order_query->bind_param("ii", $order_id, $user_id);
$order_query->execute();
$order = $order_query->get_result()->fetch_assoc();

if (!$order) {
    // Jika pesanan tidak ditemukan atau bukan milik pengguna, redirect
    header("Location: /orders.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan - Artisan Wood</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        .confirmation-container {
            text-align: center;
            padding: 50px 20px;
            max-width: 600px;
            margin: 40px auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .confirmation-container h1 {
            color: #28a745;
            margin-bottom: 15px;
        }
        .confirmation-container p {
            font-size: 1.1em;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #2c3e50;
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <div class="confirmation-container">
        <h1>Pembayaran Berhasil!</h1>
        <p>Terima kasih atas pesanan Anda. Pesanan Anda #<?= htmlspecialchars($order_id) ?> telah kami terima dan sedang diproses.</p>
        <p>Total Tagihan: <strong>Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></strong></p>
        <p>Status Pesanan: <strong><?= htmlspecialchars($order['status']) ?></strong></p>
        <a href="/orders.php" class="btn btn-primary">Lihat Riwayat Pesanan</a>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
