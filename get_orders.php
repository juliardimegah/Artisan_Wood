<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    // Return an error message if the user is not logged in
    echo '<p class="no-orders">Anda harus login untuk melihat pesanan.</p>';
    exit;
}

$user_id = $_SESSION['user_id'];
$status_filter = $_GET['status'] ?? 'Semua';

// Base query
$sql = "
    SELECT 
        o.id AS order_id, o.order_date, o.status, o.total_amount,
        p.name AS product_name, p.image AS product_image, 
        oi.quantity, oi.price
    FROM orders AS o
    JOIN order_items AS oi ON o.id = oi.order_id
    JOIN products AS p ON oi.product_id = p.id
    WHERE o.user_id = ?";

// Add status filter if not 'Semua'
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
            'product_image' => $row['product_image'],
            'quantity' => $row['quantity'],
            'price' => $row['price']
        ];
    }

    foreach ($orders as $order_id => $order) {
        echo '<div class="order-group">';
        echo '<h4>Order ID: ' . $order_id . ' | Status: ' . htmlspecialchars($order['details']['status']) . '</h4>';
        foreach ($order['items'] as $item) {
            echo '
            <div class="order-item">
                <div class="item-main-info">
                    <span class="item-qty">'. $item['quantity'] .'x</span>
                    <img src="'. htmlspecialchars($item['product_image']) .'" alt="Product">
                    <div class="item-details">
                        <p><strong>'. htmlspecialchars($item['product_name']) .'</strong></p>
                    </div>
                </div>
                <div class="item-price">
                    Rp'. number_format($item['price'] * $item['quantity'], 0, ',', '.') .'
                </div>
            </div>';
        }
        echo '<div class="order-total"><strong>Total: Rp'. number_format($order['details']['total_amount'], 0, ',', '.') .'</strong></div>';
        echo '</div>';
    }
} else {
    echo '<p class="no-orders">Belum ada pesanan dengan status ini.</p>';
}

$stmt->close();
$conn->close();
?>
