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

// Data tiket yang tersedia dengan nama dan harga yang diperbarui
$tiket_data = [
    'candi_kedaton' => ['nama' => 'Candi Kedaton', 'harga' => 5000, 'deskripsi' => 'Tiket masuk ke kompleks candi bersejarah dengan struktur bata merah.'],
    'candi_mahligai' => ['nama' => 'Candi Koto Mahligai', 'harga' => 5000, 'deskripsi' => 'Tiket masuk ke candi unik di tengah kebun karet dengan arsitektur menawan.'],
    'paket_singkat' => ['nama' => 'Paket Singkat', 'harga' => 15000, 'deskripsi' => 'Pengalaman menyusuri kanal kuno rute singkat (&plusmn;15 menit) dengan perahu tradisional.'],
    'paket_meeting_boat' => ['nama' => 'Paket Meeting Boat', 'harga' => 1450000, 'deskripsi' => 'Paket eksklusif (&plusmn;1 jam) dengan perahu yang lebih besar, cocok untuk kelompok hingga 16 orang.'],
];

// Ambil jenis tiket dari parameter URL
// Jika tidak ada di URL atau tidak valid, default ke 'candi_kedaton'
$jenis_tiket_url = $_GET['jenis'] ?? 'candi_kedaton';
$tiket_terpilih = $tiket_data[$jenis_tiket_url] ?? $tiket_data['candi_kedaton'];
$selected_option_value = $jenis_tiket_url;


// Sertakan koneksi database
require_once 'koneksi.php';

// Proses form jika ada POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'] ?? '';
    $wa = $_POST['wa'] ?? '';
    $jenis_form_post = $_POST['jenis'] ?? '';
    $jumlah = $_POST['jumlah'] ?? '';
    $tanggal_kunjungan = $_POST['tanggal_kunjungan'] ?? '';

    $harga_valid = $tiket_data[$jenis_form_post]['harga'] ?? 0;
    $total_valid = $harga_valid * (int)$jumlah;

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
        $stmt = $pdo->prepare("INSERT INTO pesan_tiket (user_id, nama_pemesan, wa, jenis_tiket, jumlah, tanggal_kunjungan, total_harga, bukti_bayar, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $nama,
            $wa,
            $tiket_data[$jenis_form_post]['nama'] ?? 'Tidak Diketahui',
            $jumlah,
            $tanggal_kunjungan,
            $total_valid,
            $bukti_bayar
        ]);

        // Simpan ke sesi untuk konfirmasi
        $_SESSION['pesanan_tiket'] = [
            'nama' => $nama,
            'wa' => $wa,
            'jenis' => $tiket_data[$jenis_form_post]['nama'] ?? 'Tidak Diketahui',
            'jumlah' => $jumlah,
            'tanggal_kunjungan' => $tanggal_kunjungan,
            'harga' => $harga_valid,
            'total' => $total_valid,
            'user_id' => $_SESSION['user_id'] ?? null,
            'user_name' => $_SESSION['user_name'] ?? 'Tamu',
            'user_email' => $_SESSION['user_email'] ?? 'email@contoh.com',
            'nomor_tiket' => 'MH' . date('Ymd') . rand(1000, 9999),
            'bukti_bayar' => $bukti_bayar
        ];

        header("Location: konfirmasi-tiket.php");
        exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage() . " at " . date('Y-m-d H:i:s'));
        $error_message = urlencode("Gagal menyimpan pesanan. Cek log untuk detail.");
        header("Location: pesan-tiket.php?error=" . $error_message . "&jenis=" . urlencode($jenis_tiket_url));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pesan Tiket | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style-auth.css" />
    <style>
        /* Semua style CSS asli dipertahankan */
        .login-container {
            max-width: 520px !important;
            width: 100% !important;
            padding: 20px !important;
        }

        .login-header h1 {
            font-size: 24px !important;
            margin-bottom: 8px !important;
        }

        .login-header p {
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

        /* Bagian .ticket-info ini dihapus sesuai permintaan Anda */
        /* .ticket-info { ... } */

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
            .login-container {
                max-width: 95% !important;
                padding: 16px !important;
            }

            .review-result p {
                flex-direction: column;
                align-items: flex-start;
                gap: 2px;
            }

            .ticket-info {
                /* Ini akan dihapus, tapi jika ada di halaman lain, style ini tetap relevan */
                padding: 15px;
            }

            .ticket-info h3 {
                font-size: 16px;
            }

            .ticket-info p {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Form Pemesanan Tiket</h1>
            <p>Lengkapi data di bawah untuk memesan tiket</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div style="color: red; text-align: center; margin-bottom: 10px;">
                <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
            </div>
        <?php endif; ?>

        <div id="alert-container"></div>

        <form id="pesan-tiket-form" method="POST" enctype="multipart/form-data" action="">
            <div class="form-group">
                <label for="nama">Nama Pemesan</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user['name']); ?>" required />
            </div>

            <div class="form-group">
                <label for="wa">WhatsApp / No HP</label>
                <input type="text" id="wa" name="wa" placeholder="Contoh: 081234567890" />
            </div>

            <div class="form-group">
                <label for="jenis">Jenis Tiket</label>
                <div class="enhanced-select">
                    <select id="jenis" name="jenis" required>
                        <option value="candi_kedaton" data-harga="5000" data-deskripsi="Tiket masuk ke kompleks candi bersejarah dengan struktur bata merah." <?php echo $selected_option_value == 'candi_kedaton' ? 'selected' : ''; ?>>Candi Kedaton - Rp 5.000</option>
                        <option value="candi_mahligai" data-harga="5000" data-deskripsi="Tiket masuk ke candi unik di tengah kebun karet dengan arsitektur menawan." <?php echo $selected_option_value == 'candi_mahligai' ? 'selected' : ''; ?>>Candi Koto Mahligai - Rp 5.000</option>
                        <option value="paket_singkat" data-harga="15000" data-deskripsi="Pengalaman menyusuri kanal kuno rute singkat (&plusmn;15 menit) dengan perahu tradisional." <?php echo $selected_option_value == 'paket_singkat' ? 'selected' : ''; ?>>Paket Singkat - Rp 15.000</option>
                        <option value="paket_meeting_boat" data-harga="1450000" data-deskripsi="Paket eksklusif (&plusmn;1 jam) dengan perahu yang lebih besar, cocok untuk kelompok hingga 16 orang." <?php echo $selected_option_value == 'paket_meeting_boat' ? 'selected' : ''; ?>>Paket Meeting Boat - Rp 1.450.000</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah Tiket</label>
                <input type="number" id="jumlah" name="jumlah" required min="1" value="1" />
            </div>

            <div class="form-group">
                <label for="tanggal_kunjungan">Tanggal Kunjungan</label>
                <input type="date" id="tanggal_kunjungan" name="tanggal_kunjungan" required min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" />
            </div>

            <div class="form-group">
                <label for="harga">Harga per Tiket</label>
                <input type="text" id="harga" name="harga" readonly />
            </div>

            <div class="form-group">
                <label for="total">Total Harga</label>
                <input type="text" id="total" name="total" readonly />
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
        const jenis = document.getElementById("jenis");
        const jumlah = document.getElementById("jumlah");
        const harga = document.getElementById("harga");
        const total = document.getElementById("total");
        const reviewBtn = document.getElementById("review");
        const status = document.getElementById("status");
        const reviewResult = document.getElementById("review-result");
        const form = document.getElementById("pesan-tiket-form");

        function updateHarga() {
            const selected = jenis.options[jenis.selectedIndex];
            const hrg = parseInt(selected.getAttribute("data-harga"));
            const qty = parseInt(jumlah.value) || 1; // Pastikan jumlah juga diambil dari input
            harga.value = "Rp " + hrg.toLocaleString('id-ID');
            total.value = "Rp " + (hrg * qty).toLocaleString('id-ID'); // Gunakan qty
        }

        // Inisialisasi harga saat DOM Content Loaded
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const initialJenis = urlParams.get('jenis');
            if (initialJenis) {
                for (let i = 0; i < jenis.options.length; i++) {
                    if (jenis.options[i].value === initialJenis) {
                        jenis.selectedIndex = i;
                        break;
                    }
                }
            }
            updateHarga(); // Panggil updateHarga setelah mengatur selectedIndex
        });

        jenis.addEventListener("change", updateHarga);
        jumlah.addEventListener("input", updateHarga); // Tambahkan event listener untuk jumlah

        reviewBtn.addEventListener("click", () => {
            const selected = jenis.options[jenis.selectedIndex];
            const tiket = selected.text;
            const qty = jumlah.value;
            const hrg = selected.getAttribute("data-harga");

            reviewResult.innerHTML = `
                <h4>Review Pesanan</h4>
                <p><strong>Nama Pemesan:</strong> <span>${document.getElementById('nama').value}</span></p>
                <p><strong>WhatsApp / No HP:</strong> <span>${document.getElementById('wa').value}</span></p>
                <p><strong>Tiket:</strong> <span>${tiket}</span></p>
                <p><strong>Jumlah:</strong> <span>${qty}</span></p>
                <p><strong>Tanggal Kunjungan:</strong> <span>${document.getElementById('tanggal_kunjungan').value}</span></p>
                <p><strong>Harga per Tiket:</strong> <span>Rp ${parseInt(hrg).toLocaleString('id-ID')}</span></p>
                <p><strong>Total Harga:</strong> <span>${total.value}</span></p>
            `;
            reviewResult.style.display = 'block';
            status.textContent = "";
        });

        // Debugging: Tambahkan log untuk memastikan form disubmit
        form.addEventListener("submit", (e) => {
            console.log("Form submitted with data:", new FormData(form));
        });
    </script>
</body>

</html>