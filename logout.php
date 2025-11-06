<?php
// Mulai sesi untuk mengakses dan mengaturnya
session_start();

// Hapus semua variabel sesi
$_SESSION = array();

// Hapus cookie sesi jika digunakan
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan sesi
session_destroy();

// Header untuk mencegah caching halaman
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Arahkan ke halaman sign-in dengan notifikasi
header("Location: signin.php?status=logout_success");
exit;
?>
