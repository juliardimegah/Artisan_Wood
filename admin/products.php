<?php
include '../db_connect.php';
include './header.php';

// === FUNGSI UNTUK UPLOAD GAMBAR KE IMGBB ===
function upload_to_imgbb($tmp_name) {
    $api_key = 'dc307ddb69cafa807175633a7cf4d439';
    $url = 'https://api.imgbb.com/1/upload';

    $img_data = base64_encode(file_get_contents($tmp_name));

    $data = http_build_query([
        'key' => $api_key,
        'image' => $img_data
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return null;
    } else {
        $result = json_decode($response, true);
        if (isset($result['data']['url'])) {
            return $result['data']['url'];
        }
    }
    return null;
}

// ... (Logika PHP untuk add, update, delete) ...

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Artisan Wood CMS</title>
    <!-- PERBAIKAN: Memanggil kembali file CSS admin yang hilang -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        table img {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<main class="admin-container">
    <h1><i class="fas fa-cube"></i> Manage Products</h1>

    <!-- ... (Sisa dari konten HTML seperti form dan tabel produk) ... -->

</main>

</body>
</html>
