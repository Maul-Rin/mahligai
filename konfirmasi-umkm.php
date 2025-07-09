<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['is_logged_in']) || !$_SESSION['is_logged_in']) {
    header('Location: login.php');
    exit();
}

// Cek apakah ada data pesanan
if (!isset($_SESSION['pesanan_umkm'])) {
    header('Location: tiket-umkm.php');
    exit();
}

$pesanan = $_SESSION['pesanan_umkm'];
$user = [
    'name' => $_SESSION['user_name'],
    'email' => $_SESSION['user_email']
];

// The product name is already stored in $pesanan['produk'] from pesan-umkm.php
// No need for $produk_info array if $pesanan['produk'] already holds the display name
$nama_produk = $pesanan['produk']; // Use the 'produk' key directly for the name

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan UMKM | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f4e6 0%, #f0e6d2 100%);
            color: #2c1810;
            line-height: 1.4;
            min-height: 100vh;
        }

        /* Navbar Compact */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 0.8rem 1.5rem;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #8b4513;
            text-decoration: none;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .user-name {
            font-weight: 500;
            color: #8b4513;
            font-size: 0.85rem;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 0.4rem 0.8rem;
            border: none;
            border-radius: 15px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        /* Main Content */
        .main-container {
            max-width: 800px;
            margin: 1rem auto;
            padding: 0 1rem;
        }

        .success-banner {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .success-banner h2 {
            font-size: 1.2rem;
            margin-bottom: 0.3rem;
            font-weight: 600;
        }

        /* Order Card Design - Mobile Responsive */
        .order-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .order-main {
            display: grid;
            grid-template-columns: 2fr 1fr;
            min-height: 350px;
        }

        /* Left Side - Order Info */
        .order-left {
            padding: 1.5rem;
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            position: relative;
        }

        .order-left::after {
            content: '';
            position: absolute;
            right: -10px;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background: #f8f4e6;
            border-radius: 50%;
            box-shadow: 0 -30px 0 #f8f4e6, 0 30px 0 #f8f4e6, 0 -60px 0 #f8f4e6, 0 60px 0 #f8f4e6;
        }

        .order-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .order-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.3rem;
        }

        .order-subtitle {
            font-size: 0.85rem;
            opacity: 0.9;
        }

        .order-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            text-align: center;
        }

        .detail-label {
            font-size: 0.7rem;
            opacity: 0.8;
            margin-bottom: 0.2rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 0.9rem;
            font-weight: 600;
        }

        .order-number-section {
            text-align: center;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            margin-top: 1rem;
        }

        .order-number-label {
            font-size: 0.7rem;
            opacity: 0.8;
            margin-bottom: 0.3rem;
        }

        .order-number-value {
            font-family: 'Courier New', monospace;
            font-size: 1.1rem;
            font-weight: bold;
            letter-spacing: 1px;
        }

        /* Right Side - Summary */
        .order-right {
            padding: 1.5rem;
            background: #fafafa;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-left: 2px dashed #8b4513;
        }

        .summary-section {
            background: #fff6ee;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e8dcc6;
            margin-bottom: 1rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.8rem;
        }

        .summary-row:last-child {
            margin-bottom: 0;
            font-weight: 600;
            font-size: 0.9rem;
            padding-top: 0.5rem;
            border-top: 1px solid #e8dcc6;
        }

        .summary-label {
            color: #6b5b47;
        }

        .summary-value {
            color: #2c1810;
        }

        .contact-section {
            background: #f0f8ff;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #b3d9ff;
            text-align: center;
        }

        .contact-title {
            font-size: 0.8rem;
            color: #0066cc;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .contact-text {
            font-size: 0.7rem;
            color: #004499;
            line-height: 1.4;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.8rem;
            margin-bottom: 1rem;
        }

        .btn {
            flex: 1;
            padding: 0.8rem 1rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(139, 69, 19, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #8b4513;
            border: 1px solid #8b4513;
        }

        .btn-secondary:hover {
            background: #8b4513;
            color: white;
        }

        /* Back Link */
        .back-section {
            text-align: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            color: #6b5b47;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .back-link:hover {
            background: white;
            color: #8b4513;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .main-container {
                padding: 0 0.5rem;
                margin: 0.5rem auto;
            }

            .navbar {
                padding: 0.6rem 1rem;
            }

            .nav-container {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .logo {
                font-size: 1.3rem;
            }

            .user-section {
                gap: 0.5rem;
            }

            .user-name {
                font-size: 0.8rem;
            }

            .logout-btn {
                font-size: 0.75rem;
                padding: 0.3rem 0.6rem;
            }

            .success-banner {
                padding: 0.8rem 1rem;
                font-size: 0.85rem;
            }

            .success-banner h2 {
                font-size: 1.1rem;
            }

            .order-main {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .order-left::after {
                display: none;
            }

            .order-right {
                border-left: none;
                border-top: 2px dashed #8b4513;
            }

            .order-left,
            .order-right {
                padding: 1rem;
            }

            .order-title {
                font-size: 1.3rem;
            }

            .order-subtitle {
                font-size: 0.8rem;
            }

            .order-details {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }

            .detail-label {
                font-size: 0.65rem;
            }

            .detail-value {
                font-size: 0.85rem;
            }

            .order-number-section {
                padding: 0.8rem;
            }

            .order-number-value {
                font-size: 1rem;
            }

            .summary-row {
                font-size: 0.75rem;
            }

            .summary-row:last-child {
                font-size: 0.85rem;
            }

            .contact-title {
                font-size: 0.75rem;
            }

            .contact-text {
                font-size: 0.65rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.6rem;
            }

            .btn {
                padding: 0.7rem;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .main-container {
                padding: 0 0.3rem;
            }

            .navbar {
                padding: 0.5rem 0.8rem;
            }

            .logo {
                font-size: 1.2rem;
            }

            .nav-container {
                justify-content: center;
                text-align: center;
            }

            .user-section {
                width: 100%;
                justify-content: center;
                margin-top: 0.3rem;
            }

            .order-left,
            .order-right {
                padding: 0.8rem;
            }

            .order-title {
                font-size: 1.2rem;
            }

            .success-banner {
                padding: 0.6rem 0.8rem;
            }

            .back-link {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }
        }

        /* Animation */
        .order-container {
            animation: slideUp 0.6s ease forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Mahligai Heritage</a>

            <div class="user-section">
                <span class="user-name">👋 <?php echo htmlspecialchars($user['name']); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <div class="main-container">
        <div class="success-banner">
            <h2>🛍️ Pesanan Berhasil!</h2>
            <p>Terima kasih telah memesan produk UMKM. Berikut adalah detail pesanan Anda:</p>
        </div>

        <div class="order-container">
            <div class="order-main">
                <div class="order-left">
                    <div class="order-header">
                        <h1 class="order-title">MAHLIGAI HERITAGE</h1>
                        <p class="order-subtitle">Pesanan Produk UMKM</p>
                    </div>

                    <div class="order-details">
                        <div class="detail-item">
                            <div class="detail-label">Nama Pemesan</div>
                            <div class="detail-value"><?php echo htmlspecialchars($pesanan['nama']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Produk</div>
                            <div class="detail-value"><?php echo htmlspecialchars($nama_produk); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Jumlah</div>
                            <div class="detail-value"><?php echo htmlspecialchars($pesanan['jumlah']); ?> Item</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Metode</div>
                            <div class="detail-value">
                                <?php
                                if ($pesanan['metode_pengiriman'] == 'pickup') {
                                    echo 'Pick Up';
                                } else {
                                    echo 'Delivery';
                                }
                                ?>
                            </div>
                        </div>
                        <?php if ($pesanan['metode_pengiriman'] == 'pickup' && !empty($pesanan['tanggal_pickup'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Tanggal Pickup</div>
                                <div class="detail-value"><?php echo date('d/m/Y', strtotime($pesanan['tanggal_pickup'])); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($pesanan['metode_pengiriman'] == 'delivery' && !empty($pesanan['kota'])): ?>
                            <div class="detail-item">
                                <div class="detail-label">Kota Tujuan</div>
                                <div class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $pesanan['kota'])); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Estimasi</div>
                                <div class="detail-value"><?php echo htmlspecialchars($pesanan['estimasi']); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="order-number-section">
                        <div class="order-number-label">NOMOR PESANAN</div>
                        <div class="order-number-value"><?php echo htmlspecialchars($pesanan['nomor_pesanan']); ?></div>
                    </div>
                </div>

                <div class="order-right">
                    <div class="summary-section">
                        <div class="summary-row">
                            <span class="summary-label">Harga Item:</span>
                            <span class="summary-value">Rp <?php echo number_format($pesanan['harga'], 0, ',', '.'); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Jumlah:</span>
                            <span class="summary-value"><?php echo htmlspecialchars($pesanan['jumlah']); ?>x</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Subtotal:</span>
                            <span class="summary-value">Rp <?php echo number_format($pesanan['subtotal'], 0, ',', '.'); ?></span>
                        </div>
                        <?php if ($pesanan['metode_pengiriman'] == 'delivery' && !empty($pesanan['ongkir'])): ?>
                            <div class="summary-row">
                                <span class="summary-label">Ongkir:</span>
                                <span class="summary-value">Rp <?php echo number_format($pesanan['ongkir'], 0, ',', '.'); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($pesanan['wa'])): ?>
                            <div class="summary-row">
                                <span class="summary-label">WhatsApp:</span>
                                <span class="summary-value"><?php echo htmlspecialchars($pesanan['wa']); ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="summary-row">
                            <span class="summary-label">Total Akhir:</span>
                            <span class="summary-value">Rp <?php echo number_format($pesanan['total_akhir'], 0, ',', '.'); ?></span>
                        </div>
                    </div>

                    <?php if ($pesanan['metode_pengiriman'] == 'delivery' && !empty($pesanan['alamat_lengkap'])): ?>
                        <div class="address-section" style="background: #f0f8ff; padding: 1rem; border-radius: 8px; border: 1px solid #b3d9ff; margin-bottom: 1rem;">
                            <div style="font-size: 0.8rem; color: #0066cc; margin-bottom: 0.5rem; font-weight: 600;">📍 Alamat Pengiriman:</div>
                            <div style="font-size: 0.8rem; color: #004499; line-height: 1.4;"><?php echo nl2br(htmlspecialchars($pesanan['alamat_lengkap'])); ?></div>
                        </div>
                    <?php endif; ?>

                    <div class="contact-section">
                        <div class="contact-title">📞 Kontak Penjual</div>
                        <p class="contact-text">
                            Untuk pertanyaan lebih lanjut, hubungi:<br>
                            <strong>WA Bisnis: <a href="https://wa.me/6281234567890" target="_blank" style="color: #25D366; text-decoration: none;">+62 812-3456-7890</a></strong><br>
                            <small>Jam operasional: 08:00 - 17:00 WIB</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-primary" onclick="window.print()">
                🖨️ Cetak Pesanan
            </button>
            <button class="btn btn-secondary" onclick="shareOrder()">
                📱 Bagikan
            </button>
        </div>

        <div class="back-section">
            <a href="tiket-umkm.php" class="back-link">
                ← Kembali ke Tiket & UMKM
            </a>
        </div>
    </div>

    <script>
        function shareOrder() {
            const orderNumber = '<?php echo htmlspecialchars($pesanan['nomor_pesanan']); ?>';
            const productName = '<?php echo htmlspecialchars($nama_produk); ?>';
            const total = 'Rp <?php echo number_format($pesanan['total_akhir'], 0, ',', '.'); ?>';

            const message = `Pesanan UMKM Mahligai Heritage\n\nNomor: ${orderNumber}\nProduk: ${productName}\nTotal: ${total}\n\nTerima kasih!`;

            if (navigator.share) {
                navigator.share({
                    title: 'Pesanan UMKM Mahligai Heritage',
                    text: message
                });
            } else {
                // Fallback untuk browser yang tidak support Web Share API
                navigator.clipboard.writeText(message).then(() => {
                    alert('Detail pesanan telah disalin ke clipboard!');
                });
            }
        }
    </script>
</body>

</html>