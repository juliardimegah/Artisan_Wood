<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Unauthorized
    echo '<p class="no-orders">Sesi Anda telah berakhir. Silakan <a href="/signin.php">login kembali</a>.</p>';
    exit;
}

$user_id = $_SESSION['user_id'];
$status_filter = $_GET['status'] ?? 'Semua';

$sql = "
    SELECT 
        o.id AS order_id, 
        o.order_date, 
        o.status, 
        o.total_amount,
        p.name AS product_name, 
        p.image AS product_image, 
        oi.quantity, 
        oi.price
    FROM orders AS o
    JOIN order_items AS oi ON o.id = oi.order_id
    JOIN products AS p ON oi.product_id = p.id
    WHERE o.user_id = ?";

if ($status_filter != 'Semua') {
    $sql .= " AND o.status = ?";
}

$sql .= " ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);

if ($status_filter != 'Semua') {
    $stmt->bind_param("is", $user_id, $status_filter);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[$row['order_id']]['details'] = [
            'order_date' => $row['order_date'],
            'status' => $row['status'],
            'total_amount' => $row['total_amount']
        ];
        $orders[$row['order_id']]['items'][] = [
            'product_name' => $row['product_name'],
            // Prepend absolute path to the product image
            'product_image' => '/' . ltrim($row['product_image'], '/'),
            'quantity' => $row['quantity'],
            'price' => $row['price']
        ];
    }

    foreach ($orders as $order_id => $order) {
        echo '<div class="order-group">';
        echo '<h4>Pesanan #' . $order_id . ' &bull; ' . htmlspecialchars($order['details']['status']) . '</h4>';
        foreach ($order['items'] as $item) {
            $image_path = htmlspecialchars($item['product_image']);
            echo '
            <div class="order-item">
                <img src="' . $image_path . '" alt="' . htmlspecialchars($item['product_name']) . '" class="item-image">
                <div class="item-details">
                    <p class="item-name"><strong>'. htmlspecialchars($item['product_name']) .'</strong></p>
                    <p class="item-quantity">'. $item['quantity'] .' x Rp'. number_format($item['price'], 0, ',', '.') .'</p>
                </div>
                <div class="item-price">
                    Rp'. number_format($item['price'] * $item['quantity'], 0, ',', '.') .'
                </div>
            </div>';
        }
        echo '<div class="order-total"><strong>Total Pesanan: Rp'. number_format($order['details']['total_amount'], 0, ',', '.') .'</strong></div>';
        echo '</div>';
    }
} else {
    echo '<div class="no-orders-container">';
    echo '<i class="fas fa-box-open"></i>';
    echo '<p>Anda belum memiliki pesanan dengan status ini.</p>';
    echo '<a href="/index.php" class="btn">Mulai Belanja</a>';
    echo '</div>';
}

$stmt->close();
$conn->close();
?>
