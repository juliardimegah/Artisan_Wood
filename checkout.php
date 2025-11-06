<?php
session_start();
include 'db_connect.php';
include 'midtrans-config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: /signin.php"); 
    exit;
}
$user_id = $_SESSION['user_id'];

// ... (logika proses order dan pengambilan alamat) ...

// Ambil item keranjang belanja
$cart_query = $conn->prepare("SELECT p.name, p.price, p.image, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_items = $cart_query->get_result();

$subtotal = 0;
$total_items = 0;

$order_id = 'ORDER-' . time(); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <script 
        type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="YOUR_CLIENT_KEY"
    ></script>
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <h1>Checkout</h1>
    <form class="checkout-layout" method="POST">
        <div class="checkout-details">
            <!-- ... (payment and shipping) ... -->
            <hr>
            <div class="review-order-ship-to">
                <div class="review-order">
                    <h2>Review order</h2>
                     <?php while($item = $cart_items->fetch_assoc()):
                        $subtotal += $item['price'] * $item['quantity'];
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
                    <?php endwhile; $cart_items->data_seek(0); ?>
                </div>
                <!-- ... (ship-to form) ... -->
            </div>
        </div>

        <div class="order-summary">
            <button type="button" id="pay-button">Bayar dengan Midtrans</button>
        </div>
    </form>
</main>

<?php include 'footer.php'; ?>

<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        // Token akan kita dapatkan dari server
        fetch('/get_midtrans_token.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order_id: "<?php echo $order_id; ?>",
                gross_amount: <?php echo $subtotal; ?>
            })
        })
        .then(response => response.json())
        .then(data => {
            snap.pay(data.token, {
                onSuccess: function(result){
                    alert("Pembayaran berhasil!"); 
                    console.log(result);
                },
                onPending: function(result){
                    alert("Menunggu pembayaran!");
                    console.log(result);
                },
                onError: function(result){
                    alert("Pembayaran gagal!");
                    console.log(result);
                }
            });
        });
    };
</script>

</body>
</html>