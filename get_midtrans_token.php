<?php
include 'midtrans-config.php';

// Mengambil data dari permintaan
$json_str = file_get_contents('php://input');
$json_obj = json_decode($json_str);

// Set parameter transaksi
$params = array(
    'transaction_details' => array(
        'order_id' => $json_obj->order_id,
        'gross_amount' => $json_obj->gross_amount,
    ),
);

// URL untuk mendapatkan token
$url = ($is_production) ? 
    'https://app.midtrans.com/snap/v1/transactions' : 
    'https://app.sandbox.midtrans.com/snap/v1/transactions';

// Pengaturan cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Accept: application/json',
    'Authorization: Basic ' . base64_encode($server_key . ':')
));

// Eksekusi cURL dan dapatkan token
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    echo $response; // Token dikembalikan ke klien
}

curl_close($ch);
?>