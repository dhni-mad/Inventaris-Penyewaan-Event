show DATABASES;
USE db_penyewaan_event;

CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE
);

CREATE TABLE status_barang (
    id_status INT AUTO_INCREMENT PRIMARY KEY,
    nama_status VARCHAR(50) NOT NULL
);

CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
);

CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    id_kategori INT NOT NULL,
    harga_sewa DECIMAL(12,2) NOT NULL,
    id_status INT NOT NULL,
    stok INT DEFAULT 1,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (id_status) REFERENCES status_barang(id_status)
        ON UPDATE CASCADE ON DELETE RESTRICT
);


CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE,
    total_harga DECIMAL(12,2),
    status_transaksi ENUM('proses', 'selesai', 'batal') DEFAULT 'proses',
    FOREIGN KEY (id_user) REFERENCES users(id_user)
        ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE detail_transaksi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_transaksi INT NOT NULL,
    id_barang INT NOT NULL,
    jumlah INT DEFAULT 1,
    harga_satuan DECIMAL(12,2) NOT NULL,
    subtotal DECIMAL(12,2) GENERATED ALWAYS AS (jumlah * harga_satuan) STORED,
    FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
        ON UPDATE CASCADE ON DELETE RESTRICT
);

INSERT INTO kategori (nama_kategori) 
VALUES 
('Sound System'),
('Tenda'),
('Dekorasi'),
('Panggung'),
('Pencahayaan'),
('Kursi & Meja');

INSERT INTO status_barang (nama_status) 
VALUES 
('Tersedia'),
('Disewa'),
('Perawatan'),
('Rusak');