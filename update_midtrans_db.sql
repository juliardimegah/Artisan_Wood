-- Perintah SQL untuk memperbarui tabel `orders` untuk integrasi dengan Midtrans

-- Menambahkan kolom untuk menyimpan ID transaksi unik dari Midtrans.
-- Kolom ini akan menjadi kunci unik untuk mencegah duplikasi notifikasi.
ALTER TABLE `orders` 
ADD `midtrans_transaction_id` VARCHAR(255) NULL DEFAULT NULL AFTER `courier`,
ADD UNIQUE (`midtrans_transaction_id`);

-- Menambahkan kolom untuk status pembayaran dari Midtrans (misalnya, 'pending', 'settlement', 'expire').
-- Ini memisahkan status logistik (seperti 'Dikirim') dari status pembayaran.
ALTER TABLE `orders` 
ADD `payment_status` VARCHAR(50) NOT NULL DEFAULT 'pending' AFTER `midtrans_transaction_id`;

-- Menambahkan kolom untuk menyimpan jenis pembayaran yang digunakan (misalnya, 'credit_card', 'gopay', 'bank_transfer').
ALTER TABLE `orders` 
ADD `payment_type` VARCHAR(50) NULL DEFAULT NULL AFTER `payment_status`;

-- Menambahkan kolom untuk menyimpan payload notifikasi lengkap dari Midtrans dalam format JSON.
-- Ini sangat berguna untuk debugging dan audit.
ALTER TABLE `orders` 
ADD `midtrans_payload` TEXT NULL DEFAULT NULL AFTER `payment_type`;

-- Ubah status enum untuk menyertakan 'Menunggu Pembayaran'
ALTER TABLE `orders` 
MODIFY `status` enum('Menunggu Pembayaran','Belum Dibayar','Sedang Dikemas','Dikirim','Selesai','Dibatalkan') 
DEFAULT 'Menunggu Pembayaran';
