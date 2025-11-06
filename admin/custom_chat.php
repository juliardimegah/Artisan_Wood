<?php
include '../db_connect.php';

// Logika untuk Kirim Balasan Admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_id']) && !empty($_POST['message'])) {
    $user_id = intval($_POST['user_id']);
    $message = trim($_POST['message']);
    
    $stmt = $conn->prepare("INSERT INTO custom_chat (user_id, sender, message) VALUES (?, 'admin', ?)");
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
    
    // Redirect kembali ke halaman chat user yang sama untuk refresh
    header("Location: custom_chat.php?user_id=" . $user_id);
    exit;
}

// Dapatkan user ID yang dipilih dari GET request
$selected_user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Chat - Artisan Wood CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include './header.php'; ?>

<main class="admin-container">
    <h1><i class="fas fa-comments"></i> Custom Chats</h1>

    <form method="GET" action="custom_chat.php" class="chat-user-selector">
        <label for="user_id"><i class="fas fa-user"></i> Select User to Chat With:</label>
        <select name="user_id" id="user_id" onchange="this.form.submit()">
            <option value="">-- Select a User --</option>
            <?php
            // Ambil hanya user yang pernah memulai percakapan
            $users = $conn->query("SELECT DISTINCT u.id, u.name FROM users u JOIN custom_chat cc ON u.id = cc.user_id ORDER BY u.name ASC");
            while ($u = $users->fetch_assoc()):
            ?>
            <option value="<?= $u['id'] ?>" <?= ($selected_user_id == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_user_id): // Tampilkan box chat hanya jika user dipilih ?>
        <div class="chat-container">
            <div class="chat-box" id="chatBox">
                <?php
                $chats = $conn->prepare("SELECT cc.*, u.name FROM custom_chat cc JOIN users u ON cc.user_id = u.id WHERE cc.user_id = ? ORDER BY sent_at ASC");
                $chats->bind_param("i", $selected_user_id);
                $chats->execute();
                $result = $chats->get_result();

                if($result->num_rows > 0):
                    while ($chat = $result->fetch_assoc()):
                        $sender_class = $chat['sender'] === 'admin' ? 'chat-admin' : 'chat-user';
                ?>
                <div class="chat-message <?= $sender_class ?>">
                    <strong><?= $chat['sender'] === 'admin' ? 'Admin' : htmlspecialchars($chat['name']) ?>:</strong>
                    <p><?= nl2br(htmlspecialchars($chat['message'])) ?></p>
                    <span class="chat-timestamp"><?= date('d M Y, H:i', strtotime($chat['sent_at'])) ?></span>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <p style="text-align:center; color: #888;">No chat history found for this user.</p>
                <?php endif; ?>
            </div>

            <form method="POST" action="custom_chat.php" class="reply-form">
                <input type="hidden" name="user_id" value="<?= $selected_user_id ?>">
                <textarea name="message" placeholder="Type your reply here..." required></textarea>
                <button type="submit"><i class="fas fa-paper-plane"></i> Send Reply</button>
            </form>
        </div>
        <script>
            // Auto-scroll ke pesan terakhir
            const chatBox = document.getElementById('chatBox');
            if(chatBox) { chatBox.scrollTop = chatBox.scrollHeight; }
        </script>
    <?php else: ?>
        <div class="chat-placeholder">
            <i class="fas fa-inbox"></i>
            <p>Please select a user from the dropdown to view the conversation.</p>
        </div>
    <?php endif; ?>
</main>

<?php include './footer.php'; ?>
</body>
</html>
