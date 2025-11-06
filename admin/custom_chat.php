<?php
include '../db_connect.php';
include './header.php';

// Kirim balasan admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $message = $_POST['message'];
    $stmt = $conn->prepare("INSERT INTO custom_chat (user_id, sender, message) VALUES (?, 'admin', ?)");
    $stmt->bind_param("is", $user_id, $message);
    $stmt->execute();
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
    <h1>Custom Chat</h1>

    <form method="GET">
        <label for="user_id">Select User:</label>
        <select name="user_id" id="user_id" onchange="this.form.submit()">
            <option value="">-- Select --</option>
            <?php
            $users = $conn->query("SELECT DISTINCT u.id, u.name FROM users u JOIN custom_chat cc ON u.id = cc.user_id");
            while ($u = $users->fetch_assoc()):
            ?>
            <option value="<?= $u['id'] ?>" <?= (isset($_GET['user_id']) && $_GET['user_id'] == $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if (isset($_GET['user_id']) && $_GET['user_id'] != ''):
        $uid = intval($_GET['user_id']);
        $chats = $conn->query("SELECT cc.*, u.name FROM custom_chat cc JOIN users u ON cc.user_id = u.id WHERE cc.user_id=$uid ORDER BY sent_at ASC");
    ?>
    <div class="chat-box">
        <?php while ($chat = $chats->fetch_assoc()): ?>
            <div class="chat-msg <?= $chat['sender'] ?>">
                <strong><?= $chat['sender'] == 'user' ? htmlspecialchars($chat['name']) : 'Admin' ?>:</strong> <?= htmlspecialchars($chat['message']) ?><br>
                <small><?= $chat['sent_at'] ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <form method="POST" class="reply-form">
        <input type="hidden" name="user_id" value="<?= $uid ?>">
        <textarea name="message" placeholder="Type your reply..." required></textarea>
        <button type="submit">Send Reply</button>
    </form>
    <?php endif; ?>
</main>
<?php include './footer.php'; ?>
