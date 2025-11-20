# Sistem Inventaris dan Pengelolaan Barang

Sistem web CRUD sederhana untuk manajemen inventaris barang dengan fitur login admin menggunakan PHP, MySQL, HTML, dan CSS.

## 📋 Fitur Utama

- ✅ **Sistem Login** - Autentikasi admin dengan session
- ✅ **Dashboard** - Ringkasan statistik barang dan transaksi
- ✅ **Manajemen Kategori** - CRUD kategori barang
- ✅ **Manajemen Status** - CRUD status ketersediaan barang
- ✅ **Manajemen Barang** - CRUD barang dengan kategori, harga, dan stok
- ✅ **Manajemen Transaksi** - CRUD transaksi peminjaman barang dengan detail items
- ✅ **Responsive Design** - Tampilan yang rapi dan mobile-friendly

---

## 🗂️ Struktur Folder

```
BDL/
├── index.php                    # Halaman login
├── logout.php                   # Logout
├── setup.php                    # Setup database
│
├── config/
│   └── database.php            # Koneksi database
│
├── pages/
│   ├── dashboard.php           # Dashboard
│   ├── kategori/
│   │   ├── index.php           # List kategori
│   │   ├── add.php             # Tambah kategori
│   │   └── edit.php            # Edit kategori
│   ├── status/
│   │   ├── index.php           # List status
│   │   ├── add.php             # Tambah status
│   │   └── edit.php            # Edit status
│   ├── barang/
│   │   ├── index.php           # List barang
│   │   ├── add.php             # Tambah barang
│   │   └── edit.php            # Edit barang
│   └── transaksi/
│       ├── index.php           # List transaksi
│       ├── add.php             # Tambah transaksi
│       ├── view.php            # Lihat detail transaksi
│       └── edit.php            # Edit transaksi
│
└── assets/
    └── css/
        └── style.css           # Stylesheet
```

---

## 🚀 Cara Install & Menjalankan

### 1. **Setup Database**
```sql
-- Jalankan query SQL yang telah disediakan di awal untuk membuat:
-- - Database: db_penyewaan_event
-- - Tables: users, status_barang, kategori, barang, transaksi, detail_transaksi
```

### 2. **Buka URL Setup**
Akses di browser:
```
http://localhost/BDL/setup.php
```
Script ini akan membuat:
- Admin account dengan username: `admin` dan password: `admin123`
- Status barang default: Tersedia, Rusak, Dipinjam

### 3. **Login**
Akses halaman login:
```
http://localhost/BDL/index.php
```

Gunakan kredensial:
- **Username**: admin
- **Password**: admin123

---

## 📱 Menu & Fitur

### Dashboard
- Menampilkan statistik:
  - Total Barang
  - Total Kategori
  - Total Stok
  - Total Transaksi

### Kategori
- Lihat semua kategori barang
- Tambah kategori baru
- Edit kategori
- Hapus kategori (jika tidak digunakan oleh barang)

### Status Barang
- Lihat semua status barang
- Tambah status baru
- Edit status
- Hapus status (jika tidak digunakan oleh barang)

### Barang
- Lihat semua barang dengan kategori, stok, dan harga
- Tambah barang dengan:
  - Nama barang
  - Kategori
  - Harga sewa
  - Status
  - Stok
- Edit barang
- Hapus barang (jika tidak digunakan dalam transaksi)

### Transaksi
- Lihat semua transaksi dengan status
- Tambah transaksi dengan:
  - Pilih user/admin
  - Tanggal pinjam
  - Tanggal kembali (opsional)
  - Multiple items (barang) dengan jumlah
  - Hitung otomatis total harga
- Lihat detail transaksi
- Edit transaksi (ubah tanggal kembali dan status)
- Hapus transaksi

---

## 🔐 Keamanan

- Password di-hash menggunakan `password_hash()` (BCRYPT)
- Session-based authentication
- Prepared statements untuk mencegah SQL injection
- Input validation dan sanitization
- HTML escape untuk mencegah XSS

---

## 💾 Database Schema

### Users
```
id_user (PRIMARY KEY)
username (UNIQUE)
password (hashed)
nama_lengkap
email (UNIQUE)
```

### Kategori
```
id_kategori (PRIMARY KEY)
nama_kategori
```

### Status Barang
```
id_status (PRIMARY KEY)
nama_status
```

### Barang
```
id_barang (PRIMARY KEY)
nama_barang
id_kategori (FOREIGN KEY)
harga_sewa
id_status (FOREIGN KEY)
stok
```

### Transaksi
```
id_transaksi (PRIMARY KEY)
id_user (FOREIGN KEY)
tanggal_pinjam
tanggal_kembali
total_harga
status_transaksi (proses/selesai/batal)
```

### Detail Transaksi
```
id_detail (PRIMARY KEY)
id_transaksi (FOREIGN KEY)
id_barang (FOREIGN KEY)
jumlah
harga_satuan
subtotal (GENERATED)
```

---

## 🎨 Desain UI

- **Color Scheme**: Modern dengan gradien purple (#667eea ke #764ba2)
- **Responsive**: Mobile-first design dengan grid layout
- **Icons**: Menggunakan emoji untuk visual yang menarik
- **Typography**: Segoe UI dan font system yang clean

---

## 📝 Catatan Penting

1. **Setup.php hanya dijalankan sekali** untuk inisialisasi data default
2. Setelah login, admin dapat membuat user tambahan melalui database
3. Transaksi yang sudah digunakan di detail_transaksi tidak bisa dihapus
4. Kategori dan Status tidak bisa dihapus jika masih digunakan oleh barang
5. Total harga transaksi dihitung otomatis berdasarkan jumlah × harga satuan

---

## 🔧 Teknologi yang Digunakan

- **Backend**: PHP (procedural)
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (vanilla)
- **Security**: BCRYPT password hashing, prepared statements

---

## 📧 Support

Jika ada pertanyaan atau issue, silahkan hubungi tim IT.

---

**Dibuat dengan ❤️ untuk sistem manajemen inventaris yang sederhana dan efektif.**
