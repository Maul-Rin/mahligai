<?php
session_start();
require_once 'check-admin.php';
requireAdmin();

$user = [
    'name' => $_SESSION['user_name'],
    'email' => $_SESSION['user_email']
];

require_once 'koneksi.php';

// Get analytics data
try {
    // Monthly sales data for chart
    // COMBINED query for both pesan_tiket and pesan_umkm
    $stmt = $pdo->query("
        SELECT
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as total_orders,
            SUM(total_harga) as total_revenue
        FROM pesan_tiket
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')

        UNION ALL

        SELECT
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as total_orders,
            SUM(total_akhir) as total_revenue
        FROM pesan_umkm
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')

        ORDER BY month ASC
    ");
    $monthly_data_raw = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Aggregate monthly data (summing up orders and revenue for the same month from both sources)
    $monthly_data = [];
    foreach ($monthly_data_raw as $row) {
        $month = $row['month'];
        if (!isset($monthly_data[$month])) {
            $monthly_data[$month] = [
                'month' => $month,
                'total_orders' => 0,
                'total_revenue' => 0
            ];
        }
        $monthly_data[$month]['total_orders'] += $row['total_orders'];
        $monthly_data[$month]['total_revenue'] += $row['total_revenue'];
    }
    // Re-index to a simple array for JSON encoding
    $monthly_data = array_values($monthly_data);


    // Top selling tickets
    $stmt = $pdo->query("
        SELECT jenis_tiket, COUNT(*) as total_sold, SUM(total_harga) as revenue
        FROM pesan_tiket
        GROUP BY jenis_tiket
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    $top_tickets = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Top UMKM products
    $stmt = $pdo->query("
        SELECT produk, COUNT(*) as total_sold, SUM(total_akhir) as revenue
        FROM pesan_umkm
        GROUP BY produk
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    $top_umkm = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Recent activity
    $stmt = $pdo->query("
        (SELECT 'tiket' as type, jenis_tiket as item, total_harga as amount, created_at
         FROM pesan_tiket ORDER BY created_at DESC LIMIT 5)
        UNION ALL
        (SELECT 'umkm' as type, produk as item, total_akhir as amount, created_at
         FROM pesan_umkm ORDER BY created_at DESC LIMIT 5)
        ORDER BY created_at DESC LIMIT 10
    ");
    $recent_activity = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

    // Summary stats
    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(total_harga) as revenue FROM pesan_tiket");
    $tiket_stats = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->query("SELECT COUNT(*) as total, SUM(total_akhir) as revenue FROM pesan_umkm");
    $umkm_stats = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Analytics error: " . $e->getMessage());
    $monthly_data = $top_tickets = $top_umkm = $recent_activity = [];
    $tiket_stats = $umkm_stats = ['total' => 0, 'revenue' => 0];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard-style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="index.php" class="sidebar-logo">Mahligai Heritage</a>
                <p class="sidebar-subtitle">Admin Dashboard</p>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="analytics.php" class="active"><i class="fas fa-chart-bar"></i> Analytics</a>
                <a href="pengguna.php"><i class="fas fa-users"></i> Pengguna</a>
                <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
                <a href="index.php"><i class="fas fa-globe"></i> Website</a>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div class="header-left">
                    <button class="mobile-menu-btn" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="admin-title">Analytics Dashboard</h1>
                </div>
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                    <div class="user-details">
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </header>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #8b4513;">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($tiket_stats['total']); ?></h3>
                        <p>Total Tiket Terjual</p>
                        <small>Rp <?php echo number_format($tiket_stats['revenue'], 0, ',', '.'); ?></small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #a0522d;">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($umkm_stats['total']); ?></h3>
                        <p>Total Produk UMKM</p>
                        <small>Rp <?php echo number_format($umkm_stats['revenue'], 0, ',', '.'); ?></small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #cd853f;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Rp <?php echo number_format($tiket_stats['revenue'] + $umkm_stats['revenue'], 0, ',', '.'); ?></h3>
                        <p>Total Pendapatan</p>
                        <small>6 Bulan Terakhir</small>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #d2691e;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo count($monthly_data); ?></h3>
                        <p>Bulan Aktif</p>
                        <small>Periode Analisis</small>
                    </div>
                </div>
            </div>

            <div class="analytics-grid">
                <section class="admin-section chart-section">
                    <h2 class="section-title">
                        <i class="fas fa-chart-line"></i>
                        Penjualan Bulanan (6 Bulan Terakhir)
                    </h2>
                    <div class="chart-container">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </section>

                <section class="admin-section">
                    <h2 class="section-title">
                        <i class="fas fa-star"></i>
                        Tiket Terlaris
                    </h2>
                    <div class="top-products">
                        <?php foreach ($top_tickets as $index => $ticket): ?>
                            <div class="product-item">
                                <div class="product-rank"><?php echo $index + 1; ?></div>
                                <div class="product-info">
                                    <h4><?php echo htmlspecialchars($ticket['jenis_tiket']); ?></h4>
                                    <p><?php echo $ticket['total_sold']; ?> terjual • Rp <?php echo number_format($ticket['revenue'], 0, ',', '.'); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($top_tickets)): ?>
                            <p class="no-data">Belum ada data penjualan tiket.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <div class="analytics-grid">
                <section class="admin-section">
                    <h2 class="section-title">
                        <i class="fas fa-shopping-cart"></i>
                        Produk UMKM Terlaris
                    </h2>
                    <div class="top-products">
                        <?php foreach ($top_umkm as $index => $product): ?>
                            <div class="product-item">
                                <div class="product-rank"><?php echo $index + 1; ?></div>
                                <div class="product-info">
                                    <h4><?php echo htmlspecialchars($product['produk']); ?></h4>
                                    <p><?php echo $product['total_sold']; ?> terjual • Rp <?php echo number_format($product['revenue'], 0, ',', '.'); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($top_umkm)): ?>
                            <p class="no-data">Belum ada data penjualan UMKM.</p>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="admin-section">
                    <h2 class="section-title">
                        <i class="fas fa-clock"></i>
                        Aktivitas Terbaru
                    </h2>
                    <div class="activity-list">
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon <?php echo $activity['type']; ?>">
                                    <i class="fas fa-<?php echo $activity['type'] == 'tiket' ? 'ticket-alt' : 'shopping-bag'; ?>"></i>
                                </div>
                                <div class="activity-info">
                                    <h4><?php echo htmlspecialchars($activity['item']); ?></h4>
                                    <p>Rp <?php echo number_format($activity['amount'], 0, ',', '.'); ?> • <?php echo date('d M Y H:i', strtotime($activity['created_at'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($recent_activity)): ?>
                            <p class="no-data">Belum ada aktivitas.</p>
                        <?php endif; ?>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <script>
        // Monthly Sales Chart
        const ctx = document.getElementById('monthlyChart').getContext('2d');
        const monthlyData = <?php echo json_encode($monthly_data); ?>;

        const labels = monthlyData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('id-ID', {
                month: 'short',
                year: 'numeric'
            });
        });

        const orderData = monthlyData.map(item => item.total_orders);
        const revenueData = monthlyData.map(item => item.total_revenue);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Pesanan',
                    data: orderData,
                    borderColor: '#8b4513',
                    backgroundColor: 'rgba(139, 69, 19, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Pendapatan (Rp)',
                    data: revenueData,
                    borderColor: '#d2691e',
                    backgroundColor: 'rgba(210, 105, 30, 0.1)',
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        function toggleSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }
    </script>

    <style>
        .analytics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin: 2rem;
        }

        .chart-section {
            grid-column: 1 / -1;
        }

        .chart-container {
            height: 400px;
            padding: 2rem;
        }

        .top-products,
        .activity-list {
            padding: 1.5rem;
        }

        .product-item,
        .activity-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-item:last-child,
        .activity-item:last-child {
            border-bottom: none;
        }

        .product-rank {
            width: 30px;
            height: 30px;
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .activity-icon.tiket {
            background: #8b4513;
        }

        .activity-icon.umkm {
            background: #a0522d;
        }

        .product-info h4,
        .activity-info h4 {
            margin: 0 0 0.25rem 0;
            font-size: 0.875rem;
            font-weight: 600;
            color: #2b1e17;
        }

        .product-info p,
        .activity-info p {
            margin: 0;
            font-size: 0.75rem;
            color: #6b5b47;
        }

        @media (max-width: 992px) {
            .analytics-grid {
                grid-template-columns: 1fr;
                margin: 1rem;
            }
        }
    </style>
</body>

</html>