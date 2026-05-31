-- ============================================
-- PATANI Database SQL Dump
-- Database Platform: MySQL 8.0+
-- ============================================

CREATE DATABASE IF NOT EXISTS patani_db;
USE patani_db;

-- Drop existing tables
DROP TABLE IF EXISTS forum_likes;
DROP TABLE IF EXISTS forum_replies;
DROP TABLE IF EXISTS forum_topics;
DROP TABLE IF EXISTS chatbot_conversations;
DROP TABLE IF EXISTS cuaca;
DROP TABLE IF EXISTS prediksi_panen;
DROP TABLE IF EXISTS riwayat_panen;
DROP TABLE IF EXISTS perawatan;
DROP TABLE IF EXISTS sawah;
DROP TABLE IF EXISTS pengaturan;
DROP TABLE IF EXISTS users;

-- Users Table
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'petani') DEFAULT 'petani',
    nik VARCHAR(16) UNIQUE,
    desa VARCHAR(255),
    kecamatan VARCHAR(255),
    alamat TEXT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_role (role),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sawah Table
CREATE TABLE sawah (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    nama_sawah VARCHAR(255) NOT NULL,
    lokasi VARCHAR(255) NOT NULL,
    desa VARCHAR(255) NOT NULL,
    kecamatan VARCHAR(255) NOT NULL,
    luas DECIMAL(10,2) NOT NULL COMMENT 'dalam hektar',
    jenis_padi VARCHAR(255) NOT NULL,
    tanggal_tanam DATE,
    estimasi_panen DATE,
    kondisi_tanah ENUM('subur', 'sedang', 'kurang') DEFAULT 'sedang',
    kondisi_air ENUM('baik', 'cukup', 'kurang') DEFAULT 'baik',
    fase_tanam ENUM('persiapan', 'vegetatif', 'generatif', 'pematangan', 'panen') DEFAULT 'persiapan',
    status ENUM('aktif', 'panen', 'istirahat') DEFAULT 'aktif',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Perawatan Table
CREATE TABLE perawatan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sawah_id BIGINT UNSIGNED NOT NULL,
    tanggal DATE NOT NULL,
    jenis_perawatan ENUM('pemupukan', 'penyemprotan', 'pengairan', 'penyiangan', 'lainnya') NOT NULL,
    nama_kegiatan VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    bahan_digunakan VARCHAR(255),
    jumlah DECIMAL(10,2),
    satuan VARCHAR(50),
    biaya DECIMAL(12,2) DEFAULT 0,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sawah_id) REFERENCES sawah(id) ON DELETE CASCADE,
    INDEX idx_sawah (sawah_id),
    INDEX idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Riwayat Panen Table
CREATE TABLE riwayat_panen (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sawah_id BIGINT UNSIGNED NOT NULL,
    tanggal_panen DATE NOT NULL,
    hasil_panen DECIMAL(10,2) NOT NULL COMMENT 'dalam kg',
    hasil_per_hektar DECIMAL(10,2) COMMENT 'ton/ha',
    kualitas ENUM('sangat_baik', 'baik', 'sedang', 'kurang') DEFAULT 'baik',
    harga_jual DECIMAL(12,2),
    total_pendapatan DECIMAL(12,2),
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sawah_id) REFERENCES sawah(id) ON DELETE CASCADE,
    INDEX idx_sawah (sawah_id),
    INDEX idx_tanggal (tanggal_panen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Prediksi Panen Table
CREATE TABLE prediksi_panen (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sawah_id BIGINT UNSIGNED NOT NULL,
    tanggal_prediksi DATE NOT NULL,
    prediksi_hasil DECIMAL(10,2) NOT NULL COMMENT 'dalam ton',
    confidence_level DECIMAL(5,2) DEFAULT 0 COMMENT 'persentase akurasi',
    faktor_prediksi JSON,
    rekomendasi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sawah_id) REFERENCES sawah(id) ON DELETE CASCADE,
    INDEX idx_sawah (sawah_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Forum Topics Table
CREATE TABLE forum_topics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category ENUM('hama_penyakit', 'varietas_padi', 'teknik_budidaya', 'pemupukan', 'pengairan', 'umum') DEFAULT 'umum',
    views INT DEFAULT 0,
    likes INT DEFAULT 0,
    is_hot BOOLEAN DEFAULT FALSE,
    is_pinned BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_category (category),
    FULLTEXT idx_search (title, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Forum Replies Table
CREATE TABLE forum_replies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    topic_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    likes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_topic (topic_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Forum Likes Table
CREATE TABLE forum_likes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    likeable_type VARCHAR(255) NOT NULL,
    likeable_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (user_id, likeable_type, likeable_id),
    INDEX idx_likeable (likeable_type, likeable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cuaca Table
CREATE TABLE cuaca (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lokasi VARCHAR(255) NOT NULL,
    tanggal DATE NOT NULL,
    suhu DECIMAL(5,2) COMMENT 'celsius',
    kelembaban DECIMAL(5,2) COMMENT 'persen',
    curah_hujan DECIMAL(8,2) COMMENT 'mm',
    kecepatan_angin DECIMAL(5,2) COMMENT 'km/h',
    kondisi VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_lokasi_tanggal (lokasi, tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chatbot Conversations Table
CREATE TABLE chatbot_conversations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    response TEXT NOT NULL,
    context VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Pengaturan Table
CREATE TABLE pengaturan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(255) NOT NULL UNIQUE,
    `value` TEXT,
    type VARCHAR(50) DEFAULT 'string',
    `group` VARCHAR(50) DEFAULT 'general',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_group (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAMPLE DATA
-- ============================================

-- Insert Admin
INSERT INTO users (name, email, password, role, phone, status) VALUES
('Admin PATANI', 'admin@patani.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '081234567890', 'aktif');
-- Password: password

-- Insert Petani Users
INSERT INTO users (name, email, password, role, phone, nik, desa, kecamatan, alamat, status) VALUES
('Ahmad Sudirman', 'ahmad@patani.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petani', '081234567891', '3212345678901234', 'Jatibarang', 'Jatibarang', 'Jl. Raya Jatibarang No. 123', 'aktif'),
('Budi Santoso', 'budi@patani.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petani', '081234567892', '3212345678901235', 'Lohbener', 'Lohbener', 'Jl. Raya Lohbener No. 45', 'aktif'),
('Siti Aminah', 'siti@patani.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'petani', '081234567893', '3212345678901236', 'Karangampel', 'Karangampel', 'Jl. Raya Karangampel No. 67', 'aktif');

-- Insert Sawah
INSERT INTO sawah (user_id, nama_sawah, lokasi, desa, kecamatan, luas, jenis_padi, tanggal_tanam, estimasi_panen, kondisi_tanah, kondisi_air, fase_tanam, status) VALUES
(2, 'Sawah Utara', 'Blok A Desa Jatibarang', 'Jatibarang', 'Jatibarang', 2.50, 'IR64', '2025-12-25', '2026-04-25', 'subur', 'baik', 'vegetatif', 'aktif'),
(3, 'Sawah Timur', 'Blok B Desa Lohbener', 'Lohbener', 'Lohbener', 1.80, 'Ciherang', '2025-12-10', '2026-04-10', 'sedang', 'cukup', 'generatif', 'aktif'),
(4, 'Sawah Selatan', 'Blok C Desa Karangampel', 'Karangampel', 'Karangampel', 3.20, 'Inpari 32', '2025-11-15', '2026-03-15', 'subur', 'baik', 'pematangan', 'aktif');

-- Insert Forum Topics
INSERT INTO forum_topics (user_id, title, content, category, views, likes, is_hot) VALUES
(2, 'Tips mengatasi wereng coklat di musim hujan', 'Musim hujan ini serangan wereng coklat semakin meningkat. Ada yang punya tips ampuh untuk mengatasinya?', 'hama_penyakit', 320, 45, TRUE),
(3, 'Rekomendasi varietas padi tahan kekeringan', 'Saya mencari varietas padi yang tahan kekeringan untuk musim kemarau mendatang. Ada saran?', 'varietas_padi', 185, 32, FALSE),
(4, 'Cara pemupukan yang efektif untuk padi sawah', 'Berapa dosis pupuk yang ideal untuk padi sawah? Dan kapan waktu yang tepat untuk pemupukan?', 'pemupukan', 240, 38, TRUE);

-- Insert Pengaturan
INSERT INTO pengaturan (`key`, `value`, type, `group`, description) VALUES
('app_name', 'PATANI', 'string', 'general', 'Nama aplikasi'),
('contact_email', 'info@patani.com', 'string', 'general', 'Email kontak'),
('contact_phone', '0812345678990', 'string', 'general', 'Nomor telepon kontak');
