<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$courier = $data['courier'] ?? 'N/A';
$shipping_cost = $data['shipping_cost'] ?? 0;

// 1. Mulai transaksi
$conn->begin_transaction();

try {
    // 2. Ambil item dari keranjang
    $cart_query = $conn->prepare("SELECT p.id, p.price, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
    $cart_query->bind_param("i", $user_id);
    $cart_query->execute();
    $cart_items = $cart_query->get_result();

    if ($cart_items->num_rows === 0) {
        throw new Exception("Keranjang belanja kosong.");
    }

    $subtotal = 0;
    $items_to_insert = [];
    while ($item = $cart_items->fetch_assoc()) {
        $subtotal += $item['price'] * $item['quantity'];
        $items_to_insert[] = $item;
    }

    $total_amount = $subtotal + $shipping_cost;

    // 3. Buat pesanan baru di tabel `orders`
    $order_stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, courier) VALUES (?, ?, 'Menunggu Pembayaran', ?)");
    $order_stmt->bind_param("ids", $user_id, $total_amount, $courier);
    $order_stmt->execute();
    $order_id = $conn->insert_id;

    // 4. Masukkan item ke `order_items`
    $items_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($items_to_insert as $item) {
        $items_stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $items_stmt->execute();
    }

    // 5. Kosongkan keranjang pengguna
    $clear_cart_stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $clear_cart_stmt->bind_param("i", $user_id);
    $clear_cart_stmt->execute();

    // 6. Commit transaksi
    $conn->commit();

    // 7. Kembalikan ID pesanan dan jumlah total untuk Midtrans
    echo json_encode([
        'success' => true,
        'order_id' => 'ORDER-' . $order_id, // Kirim ID dengan format yang dikenali Midtrans
        'gross_amount' => $total_amount
    ]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
