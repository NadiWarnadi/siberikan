-- SIBERIKAN Database Schema
-- Sistem Informasi Distribusi dan Logistik Perikanan
-- Laravel 11.x + MySQL

-- Buat Database
CREATE DATABASE IF NOT EXISTS siberikan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE siberikan;

-- Tabel Pengguna
CREATE TABLE pengguna (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    peran ENUM('nelayan', 'tengkulak', 'sopir', 'pembeli') NOT NULL,
    alamat TEXT,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- Tabel Sessions
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
);

-- Tabel Master Jenis Ikan
CREATE TABLE master_jenis_ikan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_ikan VARCHAR(255) NOT NULL,
    nama_latin VARCHAR(255) NULL,
    deskripsi TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
);

-- Tabel Hasil Tangkapan
CREATE TABLE hasil_tangkapan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nelayan_id BIGINT UNSIGNED NOT NULL,
    jenis_ikan_id BIGINT UNSIGNED NOT NULL,
    berat DECIMAL(10,2) NOT NULL,
    grade ENUM('A', 'B', 'C') NOT NULL,
    harga_per_kg DECIMAL(10,2) NOT NULL,
    tanggal_tangkap DATE NOT NULL,
    status ENUM('tersedia', 'terjual', 'rusak') DEFAULT 'tersedia',
    catatan TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (nelayan_id) REFERENCES pengguna(id) ON DELETE CASCADE,
    FOREIGN KEY (jenis_ikan_id) REFERENCES master_jenis_ikan(id) ON DELETE CASCADE
);

-- Tabel Transaksi
CREATE TABLE transaksi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi VARCHAR(255) NOT NULL UNIQUE,
    tengkulak_id BIGINT UNSIGNED NOT NULL,
    pembeli_id BIGINT UNSIGNED NOT NULL,
    tanggal_transaksi DATE NOT NULL,
    total_harga DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'dikemas', 'dikirim', 'selesai', 'dibatalkan') DEFAULT 'pending',
    catatan TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (tengkulak_id) REFERENCES pengguna(id) ON DELETE CASCADE,
    FOREIGN KEY (pembeli_id) REFERENCES pengguna(id) ON DELETE CASCADE
);

-- Tabel Detail Transaksi
CREATE TABLE detail_transaksi (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaksi_id BIGINT UNSIGNED NOT NULL,
    hasil_tangkapan_id BIGINT UNSIGNED NOT NULL,
    jumlah_kg DECIMAL(10,2) NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (hasil_tangkapan_id) REFERENCES hasil_tangkapan(id) ON DELETE CASCADE
);

-- Tabel Pengiriman
CREATE TABLE pengiriman (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaksi_id BIGINT UNSIGNED NOT NULL,
    sopir_id BIGINT UNSIGNED NOT NULL,
    nomor_resi VARCHAR(255) NOT NULL UNIQUE,
    tanggal_kirim DATE NOT NULL,
    tanggal_estimasi DATE NOT NULL,
    tanggal_diterima DATE NULL,
    alamat_tujuan TEXT NOT NULL,
    status ENUM('menunggu', 'dalam_perjalanan', 'terkirim', 'gagal') DEFAULT 'menunggu',
    catatan TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (sopir_id) REFERENCES pengguna(id) ON DELETE CASCADE
);

-- Tabel Bukti Serah Terima
CREATE TABLE bukti_serah_terima (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pengiriman_id BIGINT UNSIGNED NOT NULL,
    nama_penerima VARCHAR(255) NOT NULL,
    waktu_terima DATETIME NOT NULL,
    foto_bukti VARCHAR(255) NULL,
    catatan TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (pengiriman_id) REFERENCES pengiriman(id) ON DELETE CASCADE
);

-- Tabel Retur Barang
CREATE TABLE retur_barang (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaksi_id BIGINT UNSIGNED NOT NULL,
    pembeli_id BIGINT UNSIGNED NOT NULL,
    tanggal_retur DATE NOT NULL,
    alasan ENUM('rusak', 'tidak_sesuai', 'kadaluarsa', 'lainnya') NOT NULL,
    keterangan TEXT NULL,
    jumlah_pengembalian DECIMAL(12,2) NOT NULL,
    status ENUM('diajukan', 'disetujui', 'ditolak', 'selesai') DEFAULT 'diajukan',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (pembeli_id) REFERENCES pengguna(id) ON DELETE CASCADE
);

-- Tabel Cache
CREATE TABLE cache (
    `key` VARCHAR(255) PRIMARY KEY,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
);

CREATE TABLE cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL
);

-- Tabel Migrations (Laravel)
CREATE TABLE migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
);

-- Sample Data: User untuk setiap role
INSERT INTO pengguna (nama, email, password, peran, alamat, created_at, updated_at) VALUES
('Budi Nelayan', 'nelayan@siberikan.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'nelayan', 'Pelabuhan Muara Baru, Jakarta Utara', NOW(), NOW()),
('Siti Tengkulak', 'tengkulak@siberikan.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tengkulak', 'Pasar Ikan Modern, Jakarta Utara', NOW(), NOW()),
('Ahmad Sopir', 'sopir@siberikan.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sopir', 'Jl. Raya Cilincing, Jakarta Utara', NOW(), NOW()),
('Dewi Pembeli', 'pembeli@siberikan.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pembeli', 'Jl. Raya Bogor, Jakarta Timur', NOW(), NOW());

-- Sample Data: Master Jenis Ikan
INSERT INTO master_jenis_ikan (nama_ikan, nama_latin, deskripsi, created_at, updated_at) VALUES
('Tuna', 'Thunnus', 'Ikan tuna segar untuk sashimi dan steak', NOW(), NOW()),
('Kakap', 'Lutjanus', 'Ikan kakap merah premium', NOW(), NOW()),
('Kembung', 'Rastrelliger', 'Ikan kembung untuk konsumsi harian', NOW(), NOW()),
('Bandeng', 'Chanos chanos', 'Ikan bandeng tanpa duri', NOW(), NOW()),
('Tongkol', 'Euthynnus', 'Ikan tongkol segar', NOW(), NOW()),
('Teri', 'Stolephorus', 'Ikan teri medan premium', NOW(), NOW()),
('Cakalang', 'Katsuwonus pelamis', 'Ikan cakalang fufu', NOW(), NOW()),
('Gurame', 'Osphronemus goramy', 'Ikan gurame budidaya', NOW(), NOW());

-- Password untuk semua akun demo: "password"
-- Hash: $2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
