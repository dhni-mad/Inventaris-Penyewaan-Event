<?php
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../../index.php");
    exit;
}

require_once dirname(dirname(dirname(__FILE__))) . '/config/database.php';

$error = '';
$success = '';

// Ambil list users, barang
$users = [];
$query = "SELECT id_user, nama_lengkap FROM users ORDER BY nama_lengkap";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$barangs = [];
$query = "SELECT id_barang, nama_barang, harga_sewa, stok FROM barang ORDER BY nama_barang";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $barangs[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_user = intval($_POST['id_user']);
    $tanggal_pinjam = htmlspecialchars($_POST['tanggal_pinjam']);
    $tanggal_kembali = htmlspecialchars($_POST['tanggal_kembali']);
    $status_transaksi = htmlspecialchars($_POST['status_transaksi']);
    
    // Array barang
    $barang_ids = $_POST['barang_id'] ?? [];
    $jumlah = $_POST['jumlah'] ?? [];

    if ($id_user == 0 || empty($tanggal_pinjam) || count($barang_ids) == 0) {
        $error = "Semua field harus diisi dan minimal ada 1 barang!";
    } else {
        // Hitung total harga
        $total_harga = 0;
        $detail_data = [];

        for ($i = 0; $i < count($barang_ids); $i++) {
            if (!empty($barang_ids[$i]) && !empty($jumlah[$i])) {
                $b_id = intval($barang_ids[$i]);
                $b_jumlah = intval($jumlah[$i]);
                
                // Cari harga barang
                foreach ($barangs as $b) {
                    if ($b['id_barang'] == $b_id) {
                        $b_harga = $b['harga_sewa'];
                        $subtotal = $b_harga * $b_jumlah;
                        $total_harga += $subtotal;
                        $detail_data[] = [
                            'id_barang' => $b_id,
                            'jumlah' => $b_jumlah,
                            'harga_satuan' => $b_harga,
                            'subtotal' => $subtotal
                        ];
                        break;
                    }
                }
            }
        }

        // Insert transaksi
        $query = "INSERT INTO transaksi (id_user, tanggal_pinjam, tanggal_kembali, total_harga, status_transaksi) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        $tanggal_kembali = !empty($tanggal_kembali) ? $tanggal_kembali : null;
        $stmt->bind_param("issds", $id_user, $tanggal_pinjam, $tanggal_kembali, $total_harga, $status_transaksi);

        if ($stmt->execute()) {
            $id_transaksi = $conn->insert_id;

            // Insert detail transaksi
            $detail_query = "INSERT INTO detail_transaksi (id_transaksi, id_barang, jumlah, harga_satuan) VALUES (?, ?, ?, ?)";
            $detail_stmt = $conn->prepare($detail_query);

            foreach ($detail_data as $detail) {
                $detail_stmt->bind_param("iiii", $id_transaksi, $detail['id_barang'], $detail['jumlah'], $detail['harga_satuan']);
                $detail_stmt->execute();
            }
            $detail_stmt->close();

            $success = "Transaksi berhasil dibuat! (ID: " . $id_transaksi . ")";
        } else {
            $error = "Gagal membuat transaksi!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - Sistem Inventaris Barang</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .item-row {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
        }
        .item-row button {
            margin-top: 10px;
        }
        .harga-display {
            padding: 10px;
            background: white;
            border-radius: 3px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <h2>Sistem Inventaris Barang</h2>
        <ul class="navbar-menu">
            <li><a href="../dashboard.php">Dashboard</a></li>
            <li><a href="../kategori/index.php">Kategori</a></li>
            <li><a href="../status/index.php">Status</a></li>
            <li><a href="../barang/index.php">Barang</a></li>
            <li><a href="index.php" class="active">Transaksi</a></li>
        </ul>
        <div class="navbar-user">
            <span>Halo, <?php echo $_SESSION['nama_lengkap']; ?>!</span>
            <a href="../../logout.php">Logout</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1 class="page-title">Tambah Transaksi</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" id="transactionForm">
                <div class="form-group">
                    <label for="id_user">User/Admin</label>
                    <select id="id_user" name="id_user" required>
                        <option value="">-- Pilih User --</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user['id_user']; ?>">
                                <?php echo htmlspecialchars($user['nama_lengkap']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tanggal_pinjam">Tanggal Pinjam</label>
                    <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_kembali">Tanggal Kembali</label>
                    <input type="date" id="tanggal_kembali" name="tanggal_kembali">
                </div>

                <div class="form-group">
                    <label for="status_transaksi">Status Transaksi</label>
                    <select id="status_transaksi" name="status_transaksi" required>
                        <option value="proses">Proses</option>
                        <option value="selesai">Selesai</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>

                <hr style="margin: 30px 0; border: 1px solid #ddd;">
                <h3 style="margin-bottom: 20px;">Detail Barang</h3>

                <div id="itemsContainer">
                    <div class="item-row">
                        <div class="form-group">
                            <label for="barang_0">Barang</label>
                            <select name="barang_id[]" class="barang-select" data-index="0" required>
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach ($barangs as $barang): ?>
                                    <option value="<?php echo $barang['id_barang']; ?>" data-harga="<?php echo $barang['harga_sewa']; ?>" data-stok="<?php echo $barang['stok']; ?>">
                                        <?php echo htmlspecialchars($barang['nama_barang']); ?> (Stok: <?php echo $barang['stok']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jumlah</label>
                            <input type="number" name="jumlah[]" min="1" value="1" class="jumlah-input" data-index="0">
                        </div>

                        <div class="harga-display">
                            <strong>Harga Satuan:</strong> <span class="harga-satuan">Rp 0</span><br>
                            <strong>Subtotal:</strong> <span class="subtotal">Rp 0</span>
                        </div>

                        <button type="button" class="btn btn-danger btn-remove" onclick="removeItem(0)">Hapus Item</button>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary" onclick="addItem()" style="margin: 20px 0;">Tambah Item</button>

                <div style="background: #f9f9f9; padding: 20px; border-radius: 5px; margin: 30px 0;">
                    <h3 style="margin-bottom: 10px;">Total Harga: <span id="totalHarga">Rp 0</span></h3>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-success">Buat Transaksi</button>
                    <a href="index.php" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        let itemCount = 1;

        function addItem() {
            const container = document.getElementById('itemsContainer');
            const barangs = <?php echo json_encode($barangs); ?>;
            
            const itemHtml = `
                <div class="item-row">
                    <div class="form-group">
                        <label for="barang_${itemCount}">Barang</label>
                        <select name="barang_id[]" class="barang-select" data-index="${itemCount}" required>
                            <option value="">-- Pilih Barang --</option>
                            ${barangs.map(b => `<option value="${b.id_barang}" data-harga="${b.harga_sewa}" data-stok="${b.stok}">
                                ${b.nama_barang} (Stok: ${b.stok})
                            </option>`).join('')}
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah[]" min="1" value="1" class="jumlah-input" data-index="${itemCount}">
                    </div>

                    <div class="harga-display">
                        <strong>Harga Satuan:</strong> <span class="harga-satuan">Rp 0</span><br>
                        <strong>Subtotal:</strong> <span class="subtotal">Rp 0</span>
                    </div>

                    <button type="button" class="btn btn-danger btn-remove" onclick="removeItem(${itemCount})">Hapus Item</button>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', itemHtml);
            setupSelectListeners(itemCount);
            itemCount++;
        }

        function removeItem(index) {
            const items = document.querySelectorAll('.item-row');
            if (items.length > 1) {
                items[index].remove();
                updateTotal();
            } else {
                alert('Minimal harus ada 1 item!');
            }
        }

        function setupSelectListeners(index) {
            const select = document.querySelector(`select[data-index="${index}"]`);
            const jumlahInput = document.querySelector(`input[data-index="${index}"].jumlah-input`);
            
            select.addEventListener('change', updateTotal);
            jumlahInput.addEventListener('input', updateTotal);
        }

        function updateTotal() {
            let total = 0;
            const items = document.querySelectorAll('.item-row');
            
            items.forEach((item, index) => {
                const select = item.querySelector('select');
                const jumlahInput = item.querySelector('.jumlah-input');
                const hargaSpan = item.querySelector('.harga-satuan');
                const subtotalSpan = item.querySelector('.subtotal');
                
                const harga = parseFloat(select.selectedOptions[0].getAttribute('data-harga')) || 0;
                const jumlah = parseInt(jumlahInput.value) || 0;
                const subtotal = harga * jumlah;
                
                hargaSpan.textContent = 'Rp ' + formatRupiah(harga);
                subtotalSpan.textContent = 'Rp ' + formatRupiah(subtotal);
                
                total += subtotal;
            });
            
            document.getElementById('totalHarga').textContent = 'Rp ' + formatRupiah(total);
        }

        function formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        }

        // Setup listeners untuk item pertama
        setupSelectListeners(0);
        
        // Set tanggal hari ini sebagai default
        document.getElementById('tanggal_pinjam').valueAsDate = new Date();
    </script>
</body>
</html>
