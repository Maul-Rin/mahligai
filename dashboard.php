<?php
session_start();
require_once 'check-admin.php';

// Cek apakah user sudah login dan adalah admin
requireAdmin();

$user = [
    'name' => $_SESSION['user_name'],
    'email' => $_SESSION['user_email']
];

// Koneksi ke database
require_once 'koneksi.php';

// Handle AJAX requests untuk update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $order_id = $_POST['order_id'] ?? '';
    $action = $_POST['action'] ?? '';
    $table = $_POST['table'] ?? 'pesan_tiket';

    try {
        // Cek apakah kolom status ada
        $stmt = $pdo->query("SHOW COLUMNS FROM $table LIKE 'status'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("UPDATE $table SET status = ? WHERE id = ?");
            $stmt->execute([$action, $order_id]);
            echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Kolom status tidak tersedia']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

// Ambil data pesanan tiket
try {
    $stmt = $pdo->query("
        SELECT pt.*, u.name as user_name
        FROM pesan_tiket pt 
        LEFT JOIN users u ON pt.user_id = u.id 
        ORDER BY pt.created_at DESC 
        LIMIT 10
    ");
    $tiket_orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    error_log("Error fetching tiket orders: " . $e->getMessage());
    $tiket_orders = [];
}

// Ambil data pesanan UMKM
try {
    $stmt = $pdo->query("
        SELECT pu.*, u.name as user_name
        FROM pesan_umkm pu 
        LEFT JOIN users u ON pu.user_id = u.id 
        ORDER BY pu.created_at DESC 
        LIMIT 10
    ");
    $umkm_orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    error_log("Error fetching UMKM orders: " . $e->getMessage());
    $umkm_orders = [];
}

// Ambil statistik
try {
    // Total tiket terjual
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pesan_tiket");
    $total_tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total pendapatan dari tiket
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_harga), 0) as total FROM pesan_tiket");
    $tiket_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total produk UMKM terjual
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pesan_umkm");
    $total_umkm = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total pendapatan UMKM
    $stmt = $pdo->query("SELECT COALESCE(SUM(total_akhir), 0) as total FROM pesan_umkm");
    $umkm_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Total pengguna (SEMUA pengguna termasuk admin)
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $total_revenue = $tiket_revenue + $umkm_revenue;
} catch (Exception $e) {
    error_log("Error getting stats: " . $e->getMessage());
    $total_tickets = 0;
    $total_umkm = 0;
    $total_users = 0;
    $total_revenue = 0;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard-style.css">
</head>

<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="sidebar-logo">Mahligai Heritage</a>
                <p class="sidebar-subtitle">Admin Dashboard</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="analytics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
                <a href="pengguna.php"><i class="fas fa-users"></i> Pengguna</a>
                <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
                <div class="nav-divider"></div>
                <a href="index.php"><i class="fas fa-globe"></i> Lihat Website</a>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <!-- Header -->
            <header class="admin-header">
                <div class="header-left">
                    <button class="mobile-menu-btn" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="admin-title">Dashboard Admin</h1>
                </div>
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                    <div class="user-details">
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #8b4513;">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($total_tickets); ?></h3>
                        <p>Tiket Terjual</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #a0522d;">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($total_umkm); ?></h3>
                        <p>Produk UMKM Terjual</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #cd853f;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($total_users); ?></h3>
                        <p>Total Pengguna</p>
                        <small style="color: #6b5b47; font-size: 0.75rem;">Termasuk Admin</small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #d2691e;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Rp <?php echo number_format($total_revenue, 0, ',', '.'); ?></h3>
                        <p>Total Pendapatan</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Summary -->
            <div class="quick-summary">
                <div class="summary-card">
                    <h3>Ringkasan Hari Ini</h3>
                    <div class="summary-grid">
                        <?php
                        // Get today's stats
                        try {
                            $today = date('Y-m-d');

                            // Hitung jumlah tiket hari ini
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pesan_tiket WHERE DATE(created_at) = ?");
                            $stmt->execute([$today]);
                            $today_tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

                            // Hitung jumlah UMKM hari ini
                            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM pesan_umkm WHERE DATE(created_at) = ?");
                            $stmt->execute([$today]);
                            $today_umkm = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

                            // Hitung pendapatan TIKET hari ini
                            $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_harga), 0) as total FROM pesan_tiket WHERE DATE(created_at) = ?");
                            $stmt->execute([$today]);
                            $today_tiket_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

                            // Hitung pendapatan UMKM hari ini
                            $stmt = $pdo->prepare("SELECT COALESCE(SUM(total_akhir), 0) as total FROM pesan_umkm WHERE DATE(created_at) = ?");
                            $stmt->execute([$today]);
                            $today_umkm_revenue = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

                            // TOTAL pendapatan hari ini = tiket + umkm
                            $today_revenue = $today_tiket_revenue + $today_umkm_revenue;
                        } catch (Exception $e) {
                            $today_tickets = $today_umkm = $today_revenue = 0;
                        }
                        ?>
                        <div class="summary-item">
                            <div class="summary-number"><?php echo $today_tickets; ?></div>
                            <div class="summary-label">Tiket Hari Ini</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-number"><?php echo $today_umkm; ?></div>
                            <div class="summary-label">UMKM Hari Ini</div>
                        </div>
                        <div class="summary-item">
                            <div class="summary-number">Rp <?php echo number_format($today_revenue, 0, ',', '.'); ?></div>
                            <div class="summary-label">Pendapatan Hari Ini</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Ticket Orders -->
            <section class="admin-section">
                <h2 class="section-title">Pesanan Tiket Terbaru</h2>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Pemesan</th>
                                <th>Jenis Tiket</th>
                                <th>Jumlah</th>
                                <th>Total Harga</th>
                                <th>Tanggal Kunjungan</th>
                                <th>WhatsApp</th>
                                <th>Status</th>
                                <th>Bukti Bayar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tiket_orders as $order): ?>
                                <tr>
                                    <td data-label="ID"><?php echo htmlspecialchars($order['id']); ?></td>
                                    <td data-label="Nama"><?php echo htmlspecialchars($order['nama_pemesan']); ?></td>
                                    <td data-label="Jenis Tiket"><?php echo htmlspecialchars($order['jenis_tiket']); ?></td>
                                    <td data-label="Jumlah"><?php echo htmlspecialchars($order['jumlah']); ?></td>
                                    <td data-label="Total">Rp <?php echo number_format($order['total_harga'], 0, ',', '.'); ?></td>
                                    <td data-label="Tanggal"><?php echo date('d M Y', strtotime($order['tanggal_kunjungan'])); ?></td>
                                    <td data-label="WhatsApp">
                                        <?php if ($order['wa']): ?>
                                            <a href="https://wa.me/<?php echo htmlspecialchars($order['wa']); ?>" target="_blank" class="wa-link">
                                                <?php echo htmlspecialchars($order['wa']); ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Status">
                                        <span class="status-badge <?php echo htmlspecialchars($order['status'] ?? 'pending'); ?>">
                                            <?php
                                            switch ($order['status'] ?? 'pending') {
                                                case 'confirmed':
                                                    echo 'Disetujui';
                                                    break;
                                                case 'processing':
                                                    echo 'Diproses';
                                                    break;
                                                case 'completed':
                                                    echo 'Selesai';
                                                    break;
                                                default:
                                                    echo 'Menunggu';
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td data-label="Bukti">
                                        <?php if ($order['bukti_bayar']): ?>
                                            <a href="<?php echo htmlspecialchars($order['bukti_bayar']); ?>" target="_blank" class="view-proof">
                                                <i class="fas fa-image"></i> Lihat
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Aksi">
                                        <div class="action-buttons">
                                            <?php
                                            $current_status = $order['status'] ?? 'pending';
                                            switch ($current_status):
                                                case 'pending': ?>
                                                    <button class="action-btn approve-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'confirmed', 'pesan_tiket')" title="Setujui Pesanan">
                                                        <i class="fas fa-check"></i> Setujui
                                                    </button>
                                                    <button class="action-btn reject-btn" onclick="confirmReject(<?php echo $order['id']; ?>, 'pesan_tiket')" title="Tolak Pesanan">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                <?php break;
                                                case 'confirmed': ?>
                                                    <button class="action-btn process-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'processing', 'pesan_tiket')" title="Mulai Proses">
                                                        <i class="fas fa-cog"></i> Proses
                                                    </button>
                                                    <button class="action-btn back-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'pending', 'pesan_tiket')" title="Kembalikan ke Menunggu">
                                                        <i class="fas fa-arrow-left"></i> Kembali
                                                    </button>
                                                <?php break;
                                                case 'processing': ?>
                                                    <button class="action-btn complete-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'completed', 'pesan_tiket')" title="Selesaikan Pesanan">
                                                        <i class="fas fa-flag-checkered"></i> Selesai
                                                    </button>
                                                    <button class="action-btn back-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'confirmed', 'pesan_tiket')" title="Kembalikan ke Disetujui">
                                                        <i class="fas fa-arrow-left"></i> Kembali
                                                    </button>
                                                <?php break;
                                                case 'completed': ?>
                                                    <span class="completed-text">
                                                        <i class="fas fa-check-circle"></i> Pesanan Selesai
                                                    </span>
                                                    <button class="action-btn reopen-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'processing', 'pesan_tiket')" title="Buka Kembali">
                                                        <i class="fas fa-redo"></i> Buka Kembali
                                                    </button>
                                            <?php break;
                                            endswitch; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($tiket_orders)): ?>
                                <tr>
                                    <td colspan="10" class="no-data">Tidak ada data pesanan tiket.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Recent UMKM Orders -->
            <?php if (!empty($umkm_orders)): ?>
                <section class="admin-section">
                    <h2 class="section-title">Pesanan UMKM Terbaru</h2>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Pemesan</th>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Metode</th>
                                    <th class="pickup-date-col">Tanggal Pickup</th>
                                    <th>Total Akhir</th>
                                    <th>WhatsApp</th>
                                    <th>Status</th>
                                    <th>Bukti Bayar</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($umkm_orders as $order): ?>
                                    <tr>
                                        <td data-label="ID"><?php echo htmlspecialchars($order['id']); ?></td>
                                        <td data-label="Nama"><?php echo htmlspecialchars($order['nama_pemesan']); ?></td>
                                        <td data-label="Produk"><?php echo htmlspecialchars($order['produk']); ?></td>
                                        <td data-label="Jumlah"><?php echo htmlspecialchars($order['jumlah']); ?></td>
                                        <td data-label="Metode">
                                            <span class="method-badge <?php echo $order['metode_pengiriman']; ?>">
                                                <?php
                                                if ($order['metode_pengiriman'] == 'pickup') {
                                                    echo '<i class="fas fa-hand-holding"></i> Pick Up';
                                                } else {
                                                    echo '<i class="fas fa-truck"></i> Delivery';
                                                    if ($order['kota']) {
                                                        echo ' (' . htmlspecialchars($order['kota']) . ')';
                                                    }
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td data-label="Tanggal Pickup" class="pickup-date-col">
                                            <?php if ($order['metode_pengiriman'] == 'pickup'): ?>
                                                <?php if (isset($order['tanggal_pickup']) && $order['tanggal_pickup']): ?>
                                                    <span class="pickup-date">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <?php echo date('d M Y', strtotime($order['tanggal_pickup'])); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="pickup-date-missing">
                                                        <i class="fas fa-calendar-times"></i>
                                                        Belum ditentukan
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Total">Rp <?php echo number_format($order['total_akhir'], 0, ',', '.'); ?></td>
                                        <td data-label="WhatsApp">
                                            <?php if ($order['wa']): ?>
                                                <a href="https://wa.me/<?php echo htmlspecialchars($order['wa']); ?>" target="_blank" class="wa-link">
                                                    <?php echo htmlspecialchars($order['wa']); ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Status">
                                            <span class="status-badge <?php echo htmlspecialchars($order['status'] ?? 'pending'); ?>">
                                                <?php
                                                switch ($order['status'] ?? 'pending') {
                                                    case 'confirmed':
                                                        echo 'Disetujui';
                                                        break;
                                                    case 'processing':
                                                        echo 'Diproses';
                                                        break;
                                                    case 'completed':
                                                        echo 'Selesai';
                                                        break;
                                                    default:
                                                        echo 'Menunggu';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td data-label="Bukti">
                                            <?php if ($order['bukti_bayar']): ?>
                                                <a href="<?php echo htmlspecialchars($order['bukti_bayar']); ?>" target="_blank" class="view-proof">
                                                    <i class="fas fa-image"></i> Lihat
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Aksi">
                                            <div class="action-buttons">
                                                <?php
                                                $current_status = $order['status'] ?? 'pending';
                                                switch ($current_status):
                                                    case 'pending': ?>
                                                        <button class="action-btn approve-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'confirmed', 'pesan_umkm')" title="Setujui Pesanan">
                                                            <i class="fas fa-check"></i> Setujui
                                                        </button>
                                                        <button class="action-btn reject-btn" onclick="confirmReject(<?php echo $order['id']; ?>, 'pesan_umkm')" title="Tolak Pesanan">
                                                            <i class="fas fa-times"></i> Tolak
                                                        </button>
                                                    <?php break;
                                                    case 'confirmed': ?>
                                                        <button class="action-btn process-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'processing', 'pesan_umkm')" title="Mulai Proses">
                                                            <i class="fas fa-cog"></i> Proses
                                                        </button>
                                                        <button class="action-btn back-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'pending', 'pesan_umkm')" title="Kembalikan ke Menunggu">
                                                            <i class="fas fa-arrow-left"></i> Kembali
                                                        </button>
                                                    <?php break;
                                                    case 'processing': ?>
                                                        <button class="action-btn complete-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'completed', 'pesan_umkm')" title="Selesaikan Pesanan">
                                                            <i class="fas fa-flag-checkered"></i> Selesai
                                                        </button>
                                                        <button class="action-btn back-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'confirmed', 'pesan_umkm')" title="Kembalikan ke Disetujui">
                                                            <i class="fas fa-arrow-left"></i> Kembali
                                                        </button>
                                                    <?php break;
                                                    case 'completed': ?>
                                                        <span class="completed-text">
                                                            <i class="fas fa-check-circle"></i> Pesanan Selesai
                                                        </span>
                                                        <button class="action-btn reopen-btn" onclick="updateStatus(<?php echo $order['id']; ?>, 'processing', 'pesan_umkm')" title="Buka Kembali">
                                                            <i class="fas fa-redo"></i> Buka Kembali
                                                        </button>
                                                <?php break;
                                                endswitch; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php endif; ?>
        </main>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Memproses...</p>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.admin-sidebar');
            const menuBtn = document.querySelector('.mobile-menu-btn');

            if (window.innerWidth <= 992 &&
                !sidebar.contains(e.target) &&
                !menuBtn.contains(e.target) &&
                sidebar.classList.contains('active')) {
                toggleSidebar();
            }
        });

        // Function to update order status
        function updateStatus(orderId, newStatus, table) {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.display = 'flex';

            const formData = new FormData();
            formData.append('order_id', orderId);
            formData.append('action', newStatus);
            formData.append('table', table);

            fetch('dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.style.display = 'none';
                    if (data.success) {
                        showNotification('Status berhasil diupdate!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        showNotification(data.message || 'Terjadi kesalahan', 'error');
                    }
                })
                .catch(error => {
                    loadingOverlay.style.display = 'none';
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan jaringan', 'error');
                });
        }

        // Function to confirm rejection
        function confirmReject(orderId, table) {
            if (confirm('Apakah Anda yakin ingin menolak pesanan ini?')) {
                updateStatus(orderId, 'rejected', table);
            }
        }

        // Function to show notifications
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            setTimeout(() => {
                notification.classList.remove('show');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 3000);
        }

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 992) {
                document.querySelector('.admin-sidebar').classList.remove('active');
                document.querySelector('.sidebar-overlay').classList.remove('active');
            }
        });
    </script>

    <style>
        /* Method Badge Styles */
        .method-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .method-badge.pickup {
            background: #e8f5e8;
            color: #2e7d32;
        }

        .method-badge.delivery {
            background: #e3f2fd;
            color: #1565c0;
        }

        /* Pickup Date Styles */
        .pickup-date {
            color: #2e7d32;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
        }

        .pickup-date-missing {
            color: #f57c00;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
        }

        .wa-link {
            color: #25d366;
            text-decoration: none;
            font-weight: 500;
        }

        .wa-link:hover {
            text-decoration: underline;
        }

        .view-proof {
            color: #8b4513;
            text-decoration: none;
            font-size: 12px;
            padding: 4px 8px;
            background: rgba(139, 69, 19, 0.1);
            border-radius: 4px;
        }

        .view-proof:hover {
            background: rgba(139, 69, 19, 0.2);
        }

        .text-muted {
            color: #999;
            font-style: italic;
        }

        /* Navigation Divider */
        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 1rem 0;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .action-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 11px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .approve-btn {
            background: #28a745;
            color: white;
        }

        .approve-btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        .reject-btn {
            background: #dc3545;
            color: white;
        }

        .reject-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        .process-btn {
            background: #ffc107;
            color: #212529;
        }

        .process-btn:hover {
            background: #e0a800;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
        }

        .complete-btn {
            background: #17a2b8;
            color: white;
        }

        .complete-btn:hover {
            background: #138496;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
        }

        .back-btn {
            background: #6c757d;
            color: white;
        }

        .back-btn:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.3);
        }

        .reopen-btn {
            background: #fd7e14;
            color: white;
        }

        .reopen-btn:hover {
            background: #e8690b;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(253, 126, 20, 0.3);
        }

        .completed-text {
            color: #28a745;
            font-weight: 600;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 4px;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .loading-spinner i {
            font-size: 2rem;
            color: #8b4513;
            margin-bottom: 1rem;
        }

        .loading-spinner p {
            margin: 0;
            color: #666;
            font-weight: 500;
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 10000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.success {
            background: #28a745;
        }

        .notification.error {
            background: #dc3545;
        }

        .notification i {
            font-size: 1.2rem;
        }

        /* Quick Summary Styles */
        .quick-summary {
            margin: 2rem;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e8dcc6;
        }

        .summary-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.25rem;
            color: #8b4513;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }

        .summary-item {
            text-align: center;
            padding: 1rem;
            background: linear-gradient(135deg, #fff6ee, #fff1e0);
            border-radius: 8px;
            border: 1px solid #e8dcc6;
        }

        .summary-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #8b4513;
            margin-bottom: 0.5rem;
        }

        .summary-label {
            font-size: 0.875rem;
            color: #6b5b47;
            font-weight: 500;
        }

        /* Enhanced Mobile Responsiveness */
        @media (max-width: 1200px) {
            .pickup-date-col {
                min-width: 140px;
            }
        }

        @media (max-width: 992px) {
            .summary-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .quick-summary {
                margin: 1rem;
            }

            .summary-card {
                padding: 1.5rem;
            }

            .action-buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .action-btn {
                justify-content: center;
                margin-bottom: 4px;
            }

            .notification {
                right: 10px;
                left: 10px;
                transform: translateY(-100%);
            }

            .notification.show {
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .method-badge {
                font-size: 0.6875rem;
                padding: 0.2rem 0.5rem;
            }

            .pickup-date,
            .pickup-date-missing {
                font-size: 0.75rem;
            }

            .action-btn {
                font-size: 10px;
                padding: 6px 10px;
            }
        }

        /* Mobile Table Improvements */
        @media (max-width: 600px) {
            .admin-table td:before {
                font-weight: 600;
                color: #8b4513;
                width: 40%;
            }

            .admin-table td[data-label="Tanggal Pickup"]:before {
                content: "Tanggal Pickup: ";
            }

            .pickup-date-col {
                display: table-cell !important;
            }

            .method-badge {
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
                font-size: 0.625rem;
            }
        }
    </style>
</body>

</html>
