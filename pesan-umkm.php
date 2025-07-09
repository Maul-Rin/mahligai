<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
    header('Location: login.php');
    exit();
}

$user = [
    'name' => $_SESSION['user_name'],
    'email' => $_SESSION['user_email']
];

// Data produk UMKM yang sudah disesuaikan dengan tiket-umkm.php
// Pastikan nama produk di 'nama' sama persis dengan yang Anda inginkan di database/konfirmasi
$produk_data = [
    'tikar_lapik_bayi' => [
        'nama' => 'Tikar Lapik Bayi',
        'harga' => 40000,
        'deskripsi' => 'Tikar lapik bayi disusun dari lapisan tikar pandan dan rumbai (pelisir), dilapisi kain merah dan biru sebagai simbol keberanian dan wawasan luas. Ukuran dan susunannya mencerminkan jenjang harapan hidup yang tinggi dan menjunjung adat.'
    ],
    'kembut_berbucu_enam' => [
        'nama' => 'Kembut Berbucu Enam',
        'harga' => 30000,
        'deskripsi' => 'Tempat sirih dan pinang berbentuk segi enam yang terbuat dari daun pandan. Fungsinya untuk menyimpan benda-benda tahan lama, mencerminkan makna ketahanan dan keluhuran budaya.'
    ],
    'tikar_pandan_bermotif' => [
        'nama' => 'Tikar Pandan Bermotif',
        'harga' => 80000,
        'deskripsi' => 'Tikar pandan memiliki berbagai macam motif yang melambangkan nilai estetika dan keterampilan tinggi pengrajinnya. Digunakan sebagai alas, dekorasi, atau simbol upacara adat.'
    ],
    'madu_mahligai' => [
        'nama' => 'Madu Mahligai',
        'harga' => 50000, // Harga per 100ml
        'deskripsi' => 'Madu Mahligai merupakan hasil peliharaan lebah lokal oleh masyarakat Danau Lamo yang dipanen secara tradisional tanpa merusak lingkungan. Proses pemanenan dilakukan secara turun-temurun dengan kearifan lokal, menjaga kemurnian madu sekaligus melestarikan habitat lebah dan keseimbangan alam sekitar.'
    ]
];

// Ambil jenis produk dari parameter URL
// Jika tidak ada di URL atau tidak valid, default ke 'tikar_lapik_bayi'
$jenis_produk_url = $_GET['produk'] ?? 'tikar_lapik_bayi';
$produk_terpilih = $produk_data[$jenis_produk_url] ?? $produk_data['tikar_lapik_bayi'];
$selected_option_value = $jenis_produk_url;

// Sertakan koneksi database
require_once 'koneksi.php';

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'] ?? '';
    $wa = $_POST['wa'] ?? '';
    $produk_form_post = $_POST['produk'] ?? '';
    $jumlah = $_POST['jumlah'] ?? '';
    $metode_pengiriman = $_POST['metode_pengiriman'] ?? '';
    $tanggal_pickup = $_POST['tanggal_pickup'] ?? '';
    $kota = $_POST['kota'] ?? '';
    $alamat_lengkap = $_POST['alamat_lengkap'] ?? '';

    // Pastikan produk_form_post ada di $produk_data sebelum mengakses harganya
    $harga_valid = $produk_data[$produk_form_post]['harga'] ?? 0;
    $jumlah_valid = (int)$jumlah;
    $subtotal_valid = $harga_valid * $jumlah_valid;

    $ongkir_valid = 0;
    $estimasi_valid = '';
    if ($metode_pengiriman === 'delivery' && !empty($kota)) {
        $kota_ongkir_data = [
            'medan' => ['ongkir' => 20000, 'estimasi' => '2 hari'],
            'palembang' => ['ongkir' => 20000, 'estimasi' => '2 hari'],
            'pekanbaru' => ['ongkir' => 20000, 'estimasi' => '2 hari'],
            'padang' => ['ongkir' => 20000, 'estimasi' => '2 hari'],
            'jambi' => ['ongkir' => 20000, 'estimasi' => '2 hari'],
            'bengkulu' => ['ongkir' => 20000, 'estimasi' => '2 hari'],
            'bandar_lampung' => ['ongkir' => 20000, 'estimasi' => '2 hari'],
            'jakarta' => ['ongkir' => 25000, 'estimasi' => '3 hari'],
            'bandung' => ['ongkir' => 25000, 'estimasi' => '3 hari'],
            'surabaya' => ['ongkir' => 25000, 'estimasi' => '3 hari'],
            'yogyakarta' => ['ongkir' => 25000, 'estimasi' => '3 hari'],
            'semarang' => ['ongkir' => 25000, 'estimasi' => '3 hari'],
            'malang' => ['ongkir' => 25000, 'estimasi' => '3 hari'],
            'solo' => ['ongkir' => 25000, 'estimasi' => '3 hari'],
        ];
        $selected_kota_data = $kota_ongkir_data[$kota] ?? ['ongkir' => 0, 'estimasi' => ''];
        $ongkir_valid = $selected_kota_data['ongkir'];
        $estimasi_valid = $selected_kota_data['estimasi'];
    }

    $total_akhir_valid = $subtotal_valid + $ongkir_valid;

    // Handle file upload
    $bukti_bayar = '';
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
        $bukti_bayar = $upload_dir . uniqid() . '_' . basename($_FILES['bukti']['name']);
        move_uploaded_file($_FILES['bukti']['tmp_name'], $bukti_bayar);
    }

    // Simpan ke database
    try {
        $stmt = $pdo->prepare("INSERT INTO pesan_umkm (user_id, nama_pemesan, wa, produk, jumlah, harga, subtotal, metode_pengiriman, tanggal_pickup, kota, alamat_lengkap, ongkir, estimasi, total_akhir, bukti_bayar, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $nama,
            $wa,
            $produk_data[$produk_form_post]['nama'] ?? 'Tidak Diketahui',
            $jumlah_valid,
            $harga_valid,
            $subtotal_valid,
            $metode_pengiriman,
            $tanggal_pickup,
            $kota,
            $alamat_lengkap,
            $ongkir_valid,
            $estimasi_valid,
            $total_akhir_valid,
            $bukti_bayar
        ]);

        // Simpan ke sesi untuk konfirmasi
        $_SESSION['pesanan_umkm'] = [
            'nama' => $nama,
            'wa' => $wa,
            'produk' => $produk_data[$produk_form_post]['nama'] ?? 'Tidak Diketahui',
            'jumlah' => $jumlah_valid,
            'harga' => $harga_valid,
            'subtotal' => $subtotal_valid,
            'metode_pengiriman' => $metode_pengiriman,
            'tanggal_pickup' => $tanggal_pickup,
            'kota' => $kota,
            'alamat_lengkap' => $alamat_lengkap,
            'ongkir' => $ongkir_valid,
            'estimasi' => $estimasi_valid,
            'total_akhir' => $total_akhir_valid,
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_name' => $_SESSION['user_name'] ?? 'Tamu',
            'user_email' => $_SESSION['user_email'] ?? 'email@contoh.com',
            'nomor_pesanan' => 'UMKM' . date('Ymd') . rand(1000, 9999),
            'bukti_bayar' => $bukti_bayar
        ];

        header("Location: konfirmasi-umkm.php");
        exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
        $error_message = urlencode("Gagal menyimpan pesanan. Terjadi masalah pada sistem. Mohon coba lagi nanti.");
        header("Location: pesan-umkm.php?error=" . $error_message . "&produk=" . urlencode($jenis_produk_url));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pesan UMKM | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style-auth.css" />
    <style>
        /* Styling CSS yang ada sebelumnya dan disesuaikan */
        .register-container {
            max-width: 580px !important;
            width: 100% !important;
            padding: 20px !important;
        }

        .register-header h1 {
            font-size: 24px !important;
            margin-bottom: 8px !important;
        }

        .register-header p {
            font-size: 14px !important;
            margin-bottom: 20px !important;
        }

        .enhanced-select {
            position: relative;
            display: block;
        }

        .enhanced-select select {
            width: 100%;
            padding: 12px 40px 12px 14px;
            font-size: 14px;
            border-radius: 6px;
            border: 2px solid #d4c4a8;
            background-color: #fdfaf7;
            color: #2b1e17;
            transition: all 0.3s ease;
            font-family: "Inter", sans-serif;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%236a5c4c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6,9 12,15 18,9"></polyline></svg>');
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 14px;
        }

        .enhanced-select select:focus {
            outline: none;
            border-color: #a43d2c;
            box-shadow: 0 0 0 3px rgba(164, 61, 44, 0.1);
            background-color: #ffffff;
        }

        .enhanced-textarea textarea {
            width: 100%;
            padding: 12px 14px;
            font-size: 14px;
            border-radius: 6px;
            border: 2px solid #d4c4a8;
            background-color: #fdfaf7;
            color: #2b1e17;
            transition: all 0.3s ease;
            font-family: "Inter", sans-serif;
            resize: vertical;
            min-height: 80px;
        }

        .enhanced-textarea textarea:focus {
            outline: none;
            border-color: #a43d2c;
            box-shadow: 0 0 0 3px rgba(164, 61, 44, 0.1);
            background-color: #ffffff;
        }

        .form-group {
            margin-bottom: 18px !important;
        }

        .form-group label {
            font-size: 14px !important;
            margin-bottom: 6px !important;
            display: block;
            /* Agar label di atas input */
        }

        .form-group input {
            padding: 12px 14px !important;
            font-size: 14px !important;
            border-radius: 6px !important;
            width: 100%;
            /* Agar input memenuhi lebar */
            box-sizing: border-box;
            /* Agar padding tidak menambah lebar */
        }

        /* Bagian .product-info ini dihapus karena permintaan Anda */
        /* .product-info { ... } */

        .review-result {
            background: linear-gradient(135deg, #fff6ee 0%, #fff1e0 100%);
            border: 2px solid #e8dcc6;
            border-radius: 8px;
            padding: 15px;
            margin-top: 16px;
            display: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .review-result h4 {
            color: #8b4513;
            margin-bottom: 12px;
            font-size: 15px;
            font-family: "Playfair Display", serif;
            font-weight: 600;
            border-bottom: 1px solid #e8dcc6;
            padding-bottom: 6px;
        }

        .review-result p {
            margin-bottom: 6px;
            color: #2b1e17;
            font-size: 13px;
            line-height: 1.3;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            border-bottom: 1px solid rgba(232, 220, 198, 0.2);
        }

        .review-result p:last-child {
            border-bottom: none;
            font-weight: 600;
            background: rgba(160, 82, 45, 0.1);
            padding: 8px;
            border-radius: 5px;
            margin-top: 8px;
        }

        .review-result p strong {
            color: #8b4513;
            font-weight: 600;
            font-size: 12px;
        }

        .delivery-section,
        .pickup-section {
            background: rgba(255, 246, 238, 0.5);
            border: 1px solid #e8dcc6;
            border-radius: 6px;
            padding: 16px;
            margin-top: 12px;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-review,
        .submit-btn {
            flex: 1;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-review {
            background: linear-gradient(135deg, #d4a574, #c19660);
            box-shadow: 0 3px 8px rgba(212, 165, 116, 0.3);
        }

        .btn-review:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(212, 165, 116, 0.4);
        }

        .submit-btn {
            background: linear-gradient(135deg, #a43d2c, #8b3326);
            box-shadow: 0 3px 8px rgba(164, 61, 44, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(164, 61, 44, 0.4);
        }


        .form-footer {
            margin-top: 20px !important;
        }

        .back-link a {
            font-size: 13px !important;
            padding: 8px 12px !important;
        }

        @media (max-width: 768px) {
            .register-container {
                max-width: 95% !important;
                padding: 16px !important;
            }

            .review-result p {
                flex-direction: column;
                align-items: flex-start;
                gap: 2px;
            }

            .product-info {
                padding: 15px;
            }

            .product-info h3 {
                font-size: 16px;
            }

            .product-info p {
                font-size: 12px;
            }

            .delivery-section,
            .pickup-section {
                padding: 12px;
            }

            .button-group {
                flex-direction: column;
                gap: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Form Pemesanan Produk UMKM</h1>
            <p>Lengkapi data di bawah untuk memesan produk UMKM</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div style="color: red; text-align: center; margin-bottom: 10px;">
                <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
            </div>
        <?php endif; ?>

        <div id="alert-container"></div>

        <form id="pesan-umkm-form" method="POST" enctype="multipart/form-data" action="">
            <div class="form-group">
                <label for="nama">Nama Pemesan</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['name']); ?>" required />
            </div>

            <div class="form-group">
                <label for="wa">WhatsApp / No HP</label>
                <input type="text" id="wa" name="wa" placeholder="Contoh: 081234567890" />
            </div>

            <div class="form-group">
                <label for="produk">Produk</label>
                <div class="enhanced-select">
                    <select id="produk" name="produk" required>
                        <?php foreach ($produk_data as $key => $data): ?>
                            <option value="<?php echo htmlspecialchars($key); ?>"
                                data-harga="<?php echo htmlspecialchars($data['harga']); ?>"
                                data-deskripsi="<?php echo htmlspecialchars($data['deskripsi']); ?>"
                                <?php echo ($selected_option_value == $key) ? 'selected' : ''; ?>>
                                <?php
                                echo htmlspecialchars($data['nama']);
                                if ($key == 'madu_mahligai') {
                                    echo ' - Rp ' . number_format($data['harga'], 0, ',', '.') . ' / 100 ml';
                                } else {
                                    echo ' - Rp ' . number_format($data['harga'], 0, ',', '.');
                                }
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input type="number" id="jumlah" name="jumlah" required min="1" value="1" />
            </div>

            <div class="form-group">
                <label for="metode_pengiriman">Metode Pengiriman</label>
                <div class="enhanced-select">
                    <select id="metode_pengiriman" name="metode_pengiriman" required>
                        <option value="">Pilih Metode Pengiriman</option>
                        <option value="pickup">Pick Up - Ambil di Lokasi</option>
                        <option value="delivery">Delivery - Kirim ke Alamat</option>
                    </select>
                </div>
            </div>

            <div id="pickup-section" class="pickup-section" style="display: none;">
                <div class="form-group">
                    <label for="tanggal_pickup">Tanggal Pengambilan</label>
                    <input type="date" id="tanggal_pickup" name="tanggal_pickup" min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" />
                </div>
            </div>

            <div id="delivery-section" class="delivery-section" style="display: none;">
                <div class="form-group">
                    <label for="kota">Kota Tujuan</label>
                    <div class="enhanced-select">
                        <select id="kota" name="kota">
                            <option value="">Pilih Kota</option>
                            <optgroup label="Pulau Sumatera (Ongkir: Rp 20.000 - Estimasi: 2 hari)">
                                <option value="medan" data-ongkir="20000" data-estimasi="2">Medan</option>
                                <option value="palembang" data-ongkir="20000" data-estimasi="2">Palembang</option>
                                <option value="pekanbaru" data-ongkir="20000" data-estimasi="2">Pekanbaru</option>
                                <option value="padang" data-ongkir="20000" data-estimasi="2">Padang</option>
                                <option value="jambi" data-ongkir="20000" data-estimasi="2">Jambi</option>
                                <option value="bengkulu" data-ongkir="20000" data-estimasi="2">Bengkulu</option>
                                <option value="bandar_lampung" data-ongkir="20000" data-estimasi="2">Bandar Lampung</option>
                            </optgroup>
                            <optgroup label="Pulau Jawa (Ongkir: Rp 25.000 - Estimasi: 3 hari)">
                                <option value="jakarta" data-ongkir="25000" data-estimasi="3">Jakarta</option>
                                <option value="bandung" data-ongkir="25000" data-estimasi="3">Bandung</option>
                                <option value="surabaya" data-ongkir="25000" data-estimasi="3">Surabaya</option>
                                <option value="yogyakarta" data-ongkir="25000" data-estimasi="3">Yogyakarta</option>
                                <option value="semarang" data-ongkir="25000" data-estimasi="3">Semarang</option>
                                <option value="malang" data-ongkir="25000" data-estimasi="3">Malang</option>
                                <option value="solo" data-ongkir="25000" data-estimasi="3">Solo</option>
                            </optgroup>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alamat_lengkap">Alamat Lengkap</label>
                    <div class="enhanced-textarea">
                        <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" placeholder="Masukkan alamat lengkap untuk pengiriman"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="ongkir">Ongkos Kirim</label>
                    <input type="text" id="ongkir" name="ongkir" readonly />
                </div>

                <div class="form-group">
                    <label for="estimasi">Estimasi Pengiriman</label>
                    <input type="text" id="estimasi" name="estimasi" readonly />
                </div>
            </div>

            <div class="form-group">
                <label for="harga">Harga per Item</label>
                <input type="text" id="harga" name="harga" readonly />
            </div>

            <div class="form-group">
                <label for="subtotal">Subtotal</label>
                <input type="text" id="subtotal" name="subtotal" readonly />
            </div>

            <div class="form-group">
                <label for="total_akhir">Total Akhir (Termasuk Ongkir)</label>
                <input type="text" id="total_akhir" name="total_akhir" readonly style="font-weight: bold; background-color: #fff6ee;" />
            </div>

            <div class="form-group">
                <label for="bukti">Bukti Pembayaran <span style="color: red;">*</span></label>
                <input type="file" id="bukti" name="bukti" accept="image/*" required />
            </div>

            <div class="button-group">
                <button type="button" id="review" class="btn-review">Review Pesanan</button>
                <button type="submit" id="submit" class="submit-btn">Pesan Sekarang</button>
            </div>

            <div id="review-result" class="review-result"></div>
            <div id="status" style="text-align: center; font-weight: 500; margin-top: 1.5rem; padding: 1rem; border-radius: 8px;"></div>
        </form>

        <div class="form-footer">
            <div class="back-link">
                <a href="tiket-umkm.php">
                    <i class="fas fa-arrow-left"></i>
                    Kembali ke Tiket & UMKM
                </a>
            </div>
        </div>
    </div>

    <script>
        const produk = document.getElementById("produk");
        const jumlah = document.getElementById("jumlah");
        const harga = document.getElementById("harga");
        const reviewBtn = document.getElementById("review");
        const status = document.getElementById("status");
        const reviewResult = document.getElementById("review-result");
        const form = document.getElementById("pesan-umkm-form");
        const metodePengiriman = document.getElementById("metode_pengiriman");
        const pickupSection = document.getElementById("pickup-section");
        const deliverySection = document.getElementById("delivery-section");
        const kota = document.getElementById("kota");
        const ongkir = document.getElementById("ongkir");
        const estimasi = document.getElementById("estimasi");
        const subtotal = document.getElementById("subtotal");
        const totalAkhir = document.getElementById("total_akhir");

        // Fungsi untuk memperbarui harga dan subtotal saat produk atau jumlah berubah
        function updateHarga() {
            const selected = produk.options[produk.selectedIndex];
            const hrg = parseInt(selected.getAttribute("data-harga"));
            const qty = parseInt(jumlah.value) || 1;
            const subtotalValue = hrg * qty;

            harga.value = "Rp " + hrg.toLocaleString('id-ID');
            subtotal.value = "Rp " + subtotalValue.toLocaleString('id-ID');

            updateTotalAkhir(); // Pastikan total akhir juga diperbarui
        }

        // Fungsi untuk memperbarui total akhir (termasuk ongkir)
        function updateTotalAkhir() {
            const selectedProduk = produk.options[produk.selectedIndex];
            const hrg = parseInt(selectedProduk.getAttribute("data-harga"));
            const qty = parseInt(jumlah.value) || 1;
            const subtotalValue = hrg * qty;

            let ongkirValue = 0;
            if (metodePengiriman.value === "delivery" && kota.value) {
                const selectedKota = kota.options[kota.selectedIndex];
                ongkirValue = parseInt(selectedKota.getAttribute("data-ongkir")) || 0;
            }

            const totalValue = subtotalValue + ongkirValue;
            totalAkhir.value = "Rp " + totalValue.toLocaleString('id-ID');
        }

        // Event listener untuk perubahan metode pengiriman
        metodePengiriman.addEventListener("change", function() {
            if (this.value === "pickup") {
                pickupSection.style.display = "block";
                deliverySection.style.display = "none";
                document.getElementById("tanggal_pickup").required = true;
                document.getElementById("kota").required = false;
                document.getElementById("alamat_lengkap").required = false;
                ongkir.value = "Rp 0";
                estimasi.value = "";
            } else if (this.value === "delivery") {
                pickupSection.style.display = "none";
                deliverySection.style.display = "block";
                document.getElementById("tanggal_pickup").required = false;
                document.getElementById("kota").required = true;
                document.getElementById("alamat_lengkap").required = true;
                // Panggil updateOngkirEstimasi agar langsung terisi jika kota sudah terpilih
                kota.dispatchEvent(new Event('change'));
            } else {
                pickupSection.style.display = "none";
                deliverySection.style.display = "none";
                document.getElementById("tanggal_pickup").required = false;
                document.getElementById("kota").required = false;
                document.getElementById("alamat_lengkap").required = false;
                ongkir.value = "";
                estimasi.value = "";
            }
            updateTotalAkhir();
        });

        // Event listener untuk perubahan kota tujuan
        kota.addEventListener("change", function() {
            if (this.value) {
                const selected = this.options[this.selectedIndex];
                const ongkirValue = parseInt(selected.getAttribute("data-ongkir"));
                const estimasiValue = selected.getAttribute("data-estimasi");

                ongkir.value = "Rp " + ongkirValue.toLocaleString('id-ID');
                estimasi.value = estimasiValue + " hari kerja";
            } else {
                ongkir.value = "";
                estimasi.value = "";
            }
            updateTotalAkhir();
        });

        // Event listeners untuk memperbarui harga/subtotal/total saat produk atau jumlah berubah
        produk.addEventListener("change", updateHarga);
        jumlah.addEventListener("input", updateHarga);

        // Event listener untuk tombol Review Pesanan
        reviewBtn.addEventListener("click", () => {
            const selected = produk.options[produk.selectedIndex];
            const item = selected.text;
            const qty = jumlah.value;
            const hrg = selected.getAttribute("data-harga");
            const metode = metodePengiriman.options[metodePengiriman.selectedIndex].text;

            let reviewHTML = `
                <h4>Review Pesanan</h4>
                <p><strong>Nama Pemesan:</strong> <span>${document.getElementById('nama').value}</span></p>
                <p><strong>WhatsApp / No HP:</strong> <span>${document.getElementById('wa').value}</span></p>
                <p><strong>Produk:</strong> <span>${item}</span></p>
                <p><strong>Jumlah:</strong> <span>${qty}</span></p>
                <p><strong>Harga per Item:</strong> <span>Rp ${parseInt(hrg).toLocaleString('id-ID')}</span></p>
                <p><strong>Subtotal:</strong> <span>${subtotal.value}</span></p>
                <p><strong>Metode Pengiriman:</strong> <span>${metode}</span></p>
            `;

            if (metodePengiriman.value === "delivery") {
                const kotaText = kota.options[kota.selectedIndex].text;
                const alamatLengkap = document.getElementById("alamat_lengkap").value;
                reviewHTML += `
                    <p><strong>Kota Tujuan:</strong> <span>${kotaText}</span></p>
                    <p><strong>Alamat Lengkap:</strong> <span>${alamatLengkap}</span></p>
                    <p><strong>Ongkos Kirim:</strong> <span>${ongkir.value}</span></p>
                    <p><strong>Estimasi:</strong> <span>${estimasi.value}</span></p>
                `;
            } else if (metodePengiriman.value === "pickup") {
                const tanggalPickup = document.getElementById("tanggal_pickup").value;
                reviewHTML += `<p><strong>Tanggal Pickup:</strong> <span>${tanggalPickup}</span></p>`;
            }

            reviewHTML += `<p><strong>Total Akhir:</strong> <span>${totalAkhir.value}</span></p>`;

            reviewResult.innerHTML = reviewHTML;
            reviewResult.style.display = 'block';
            status.textContent = "";
        });

        // Jalankan saat halaman dimuat untuk inisialisasi harga dan seleksi produk dari URL
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const initialProduk = urlParams.get('produk');
            if (initialProduk) {
                for (let i = 0; i < produk.options.length; i++) {
                    if (produk.options[i].value === initialProduk) {
                        produk.selectedIndex = i;
                        break;
                    }
                }
            }
            updateHarga(); // Inisialisasi harga berdasarkan produk terpilih
            metodePengiriman.dispatchEvent(new Event('change')); // Panggil event change untuk inisialisasi display section
        });
    </script>
</body>

</html>