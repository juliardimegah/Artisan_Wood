<?php
include './db_connect.php';

echo "<p>Memulai perbaikan path gambar...</p>";

// 1. Perbaiki path untuk produk ID 1
$conn->query("UPDATE products SET image = 'assets/products/Gambar-1.png' WHERE id = 1");
echo "<p>Produk ID 1 diperbarui.</p>";

// 2. Perbaiki path untuk produk ID 2
$conn->query("UPDATE products SET image = 'assets/products/Gambar-2.png' WHERE id = 2");
echo "<p>Produk ID 2 diperbarui.</p>";

// 3. Perbaiki path untuk produk ID 3
$conn->query("UPDATE products SET image = 'assets/products/Gambar-3.png' WHERE id = 3");
echo "<p>Produk ID 3 diperbarui.</p>";

echo "<p><b>Perbaikan selesai!</b> File ini akan dihapus.</p>";

// Hapus file ini setelah selesai
unlink(__FILE__);

?>
