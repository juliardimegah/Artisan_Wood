<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Design - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <div class="chat-header">
        <h1>Custom Design</h1>
        <div class="chat-actions">
            <button class="btn-secondary">Custom Confirm</button>
            <button class="btn-secondary">Close Chat</button>
        </div>
    </div>

    <div class="chat-container">
        <div class="chat-messages">
            <div class="message received">
                <div class="message-bubble">
                    <p>Baik, boleh deskripsikan produknya?</p>
                </div>
                <span class="timestamp">19.01</span>
            </div>
            <div class="message sent">
                <div class="message-bubble">
                    <p>Halo, saya mau pesan produk custom</p>
                </div>
                <span class="timestamp">19.00</span>
            </div>
        </div>
        <div class="chat-input">
            <button class="attach-btn"><i class="fas fa-paperclip"></i></button>
            <input type="text" placeholder="Message here...">
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
