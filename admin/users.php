<?php
include '../db_connect.php';

// Logika untuk Hapus User
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: users.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Artisan Wood CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php include './header.php'; ?>

<main class="admin-container">
    <h1><i class="fas fa-users"></i> Manage Users</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Joined At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $res = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
            while ($row = $res->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['address'])) ?></td>
                <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                <td>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus user ini? Semua data order terkait user ini mungkin akan terpengaruh.')" class="btn-delete">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<?php include './footer.php'; ?>
</body>
</html>
