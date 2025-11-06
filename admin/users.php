<?php
include '../db_connect.php';
include './header.php';

// Hapus user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id=$id");
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
    <h1>Manage Users</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Address</th>
            <th>Created At</th>
            <th>Action</th>
        </tr>
        <?php
        $res = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
        while ($row = $res->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= nl2br(htmlspecialchars($row['address'])) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td><a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus user ini?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</main>
<?php include './footer.php'; ?>
