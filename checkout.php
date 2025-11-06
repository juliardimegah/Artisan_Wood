<?php
session_start();
include 'db_connect.php';
include 'midtrans-config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: /signin.php"); 
    exit;
}
$user_id = $_SESSION['user_id'];

$address_query = $conn->prepare("SELECT recipient_name, phone_number, address, city, postal_code FROM shipping_address WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$address_query->bind_param("i", $user_id);
$address_query->execute();
$shipping_address = $address_query->get_result()->fetch_assoc();

$cart_query = $conn->prepare("SELECT p.id, p.name, p.price, p.image, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_items = $cart_query->get_result();

$subtotal = 0;
$total_items = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Artisan Wood</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="YOUR_CLIENT_KEY"></script>
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <h1>Checkout</h1>
    <div class="checkout-layout">
        <div class="checkout-details">
            <div class="ship-to">
                <h2>Alamat Pengiriman</h2>
                <?php if ($shipping_address): ?>
                    <p><strong><?= htmlspecialchars($shipping_address['recipient_name']) ?></strong></p>
                    <p><?= htmlspecialchars($shipping_address['phone_number']) ?></p>
                    <p><?= htmlspecialchars($shipping_address['address']) ?>, <?= htmlspecialchars($shipping_address['city']) ?>, <?= htmlspecialchars($shipping_address['postal_code']) ?></p>
                    <a href="/profile.php#address" class="btn-link">Ubah Alamat</a>
                <?php else: ?>
                    <p>Alamat pengiriman belum diatur. <a href="/profile.php#address">Tambahkan alamat</a></p>
                <?php endif; ?>
            </div>

            <hr>
            
            <div class="shipping-method">
                <h2>Jasa Pengiriman</h2>
                <select name="courier" class="form-input" required>
                    <option value="">Pilih Kurir</option>
                    <option value="JNE - REG">JNE - REG (Rp15.000)</option>
                    <option value="J&T - Express">J&T - Express (Rp16.000)</option>
                    <option value="Sicepat - REG">Sicepat - REG (Rp14.000)</option>
                </select>
            </div>

            <hr>

            <div class="review-order">
                <h2>Tinjau Pesanan</h2>
                <?php while($item = $cart_items->fetch_assoc()):
                    $item_total = $item['price'] * $item['quantity'];
                    $subtotal += $item_total;
                    $total_items += $item['quantity'];
                ?>
                <div class="order-item">
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                    <div class="item-info">
                        <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
                        <p><strong>Rp<?= number_format($item['price'], 0, ',', '.') ?></strong></p>
                        <div class="quantity">QTY <?= $item['quantity'] ?></div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="order-summary">
            <h2>Ringkasan Pesanan</h2>
            <div class="summary-row">
                <span>Subtotal (<?= $total_items ?> barang)</span>
                <span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
            </div>
            <div class="summary-row">
                <span>Biaya Pengiriman</span>
                <span id="shipping-cost">Rp0</span>
            </div>
            <hr>
            <div class="summary-row total">
                <span>Total</span>
                <span id="total-amount">Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
            </div>
            <button type="button" id="pay-button" class="btn btn-primary btn-full">Bayar dengan Midtrans</button>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script type="text/javascript">
    const courierSelect = document.querySelector('select[name="courier"]');
    const shippingCostEl = document.getElementById('shipping-cost');
    const totalAmountEl = document.getElementById('total-amount');
    const subtotal = <?= $subtotal ?>;

    const shippingOptions = {
        "JNE - REG": 15000,
        "J&T - Express": 16000,
        "Sicepat - REG": 14000
    };
    let currentShippingCost = 0;

    courierSelect.addEventListener('change', function() {
        const selectedCourier = this.value;
        currentShippingCost = shippingOptions[selectedCourier] || 0;
        const total = subtotal + currentShippingCost;

        shippingCostEl.textContent = 'Rp' + currentShippingCost.toLocaleString('id-ID');
        totalAmountEl.textContent = 'Rp' + total.toLocaleString('id-ID');
    });

    document.getElementById('pay-button').onclick = function(){
        const selectedCourier = courierSelect.value;
        if (!selectedCourier) {
            alert('Silakan pilih jasa pengiriman terlebih dahulu.');
            return;
        }

        // 1. Buat pesanan di server terlebih dahulu
        fetch('/create_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                courier: selectedCourier,
                shipping_cost: currentShippingCost
            })
        })
        .then(response => response.json())
        .then(orderData => {
            if (!orderData.success) {
                throw new Error(orderData.message || 'Gagal membuat pesanan.');
            }

            // 2. Dapatkan token Midtrans menggunakan data pesanan
            return fetch('/get_midtrans_token.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderData.order_id,
                    gross_amount: orderData.gross_amount
                })
            })
            .then(response => response.json())
            .then(paymentData => {
                if (!paymentData.token) {
                    throw new Error('Gagal mendapatkan token pembayaran.');
                }
                return { token: paymentData.token, orderId: orderData.order_id };
            });
        })
        .then(result => {
            // 3. Tampilkan popup pembayaran Snap
            snap.pay(result.token, {
                onSuccess: function(){
                    window.location.href = '/order_confirmation.php?order_id=' + result.orderId;
                },
                onPending: function(){
                    alert("Menunggu pembayaran Anda. Anda bisa melihat status pesanan di halaman Riwayat Pesanan.");
                    window.location.href = '/orders.php';
                },
                onError: function(){
                    alert("Pembayaran gagal! Silakan coba lagi atau hubungi dukungan.");
                }
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
        });
    };
</script>

</body>
</html>
