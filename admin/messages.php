<?php
include '../db_connect.php';

// Logika untuk Hapus Pesan
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("DELETE FROM customer_messages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: messages.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Messages - Artisan Wood CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include './header.php'; ?>

<main class="admin-container">
    <h1><i class="fas fa-envelope"></i> Customer Messages</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Message</th>
                <th>Received At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conn->query("SELECT * FROM customer_messages ORDER BY created_at DESC");
            if ($res->num_rows > 0):
                while ($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td style="max-width: 400px;"><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus pesan ini?')" class="btn-delete">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </td>
            </tr>
            <?php 
                endwhile;
            else:
            ?>
            <tr>
                <td colspan="6" style="text-align: center;"><em>No messages yet.</em></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>

<?php include './footer.php'; ?>
</body>
</html>
