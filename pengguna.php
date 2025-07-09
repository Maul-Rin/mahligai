<?php
session_start();
require_once 'check-admin.php';
requireAdmin();

$user = [
    'name' => $_SESSION['user_name'],
    'email' => $_SESSION['user_email']
];

require_once 'koneksi.php';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $user_id = $_POST['user_id'] ?? '';
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'delete') {
            // Hard delete for non-admin users (since no status column exists)
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
            $stmt->execute([$user_id]);
            echo json_encode(['success' => true, 'message' => 'Pengguna berhasil dihapus']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenali']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
    exit();
}

// Fetch users data - using actual database structure
try {
    $stmt = $pdo->query("
        SELECT id, name, email, role
        FROM users 
        ORDER BY id DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Exception $e) {
    error_log("Error fetching users: " . $e->getMessage());
    $users = [];
}

// Get user statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
    $total_admins = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
    $total_regular_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    // Since no status column, assume all users are active
    $active_users = $total_users;
} catch (Exception $e) {
    error_log("Error getting user stats: " . $e->getMessage());
    $total_users = $total_admins = $total_regular_users = $active_users = 0;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pengguna | Mahligai Heritage</title>
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
                <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="analytics.php"><i class="fas fa-chart-bar"></i> Analytics</a>
                <a href="pengguna.php" class="active"><i class="fas fa-users"></i> Pengguna</a>
                <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
                <a href="index.php"><i class="fas fa-globe"></i> Website</a>
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
                    <h1 class="admin-title">Manajemen Pengguna</h1>
                </div>
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                    <div class="user-details">
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </header>

            <!-- User Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #8b4513;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($total_users); ?></h3>
                        <p>Total Pengguna</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #a0522d;">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($total_admins); ?></h3>
                        <p>Administrator</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #cd853f;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($total_regular_users); ?></h3>
                        <p>Pengguna Biasa</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #d2691e;">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo number_format($active_users); ?></h3>
                        <p>Pengguna Aktif</p>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <section class="admin-section">
                <h2 class="section-title">Daftar Pengguna</h2>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td data-label="ID"><?php echo htmlspecialchars($u['id']); ?></td>
                                    <td data-label="Nama"><?php echo htmlspecialchars($u['name']); ?></td>
                                    <td data-label="Email"><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td data-label="Role">
                                        <span class="role-badge <?php echo $u['role']; ?>">
                                            <?php echo $u['role'] == 'admin' ? 'Administrator' : 'Pengguna'; ?>
                                        </span>
                                    </td>
                                    <td data-label="Aksi">
                                        <?php if ($u['role'] != 'admin' || $u['id'] != $_SESSION['user_id']): ?>
                                            <div class="action-buttons">
                                                <?php if ($u['role'] != 'admin'): ?>
                                                    <button class="btn-action btn-delete" onclick="deleteUser(<?php echo $u['id']; ?>)" title="Hapus Pengguna">
                                                        <i class="fas fa-trash"></i> Hapus
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-muted">Admin Lain</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Anda</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="5" class="no-data">Tidak ada data pengguna ditemukan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- User Registration Activity -->
            <section class="admin-section">
                <h2 class="section-title">Aktivitas Registrasi Terbaru</h2>
                <div class="activity-container">
                    <?php
                    // Get recent ticket orders to show user activity
                    try {
                        $stmt = $pdo->query("
                            SELECT pt.nama_pemesan, pt.created_at, u.name as user_name, u.email
                            FROM pesan_tiket pt
                            LEFT JOIN users u ON pt.user_id = u.id
                            ORDER BY pt.created_at DESC
                            LIMIT 5
                        ");
                        $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
                    } catch (Exception $e) {
                        $recent_orders = [];
                    }
                    ?>

                    <?php if (!empty($recent_orders)): ?>
                        <?php foreach ($recent_orders as $order): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div class="activity-info">
                                    <h4><?php echo htmlspecialchars($order['nama_pemesan']); ?></h4>
                                    <p>Memesan tiket • <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
                                    <?php if ($order['user_name']): ?>
                                        <small>User: <?php echo htmlspecialchars($order['user_name']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-data">Belum ada aktivitas pengguna.</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <script>
        function deleteUser(userId) {
            if (confirm('Apakah Anda yakin ingin menghapus pengguna ini? Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait pengguna.')) {
                fetch('pengguna.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete&user_id=${userId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification(data.message, 'success');
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            showNotification(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        showNotification('Terjadi kesalahan sistem!', 'error');
                    });
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('fade-out');
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        function toggleSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }
    </script>

    <style>
        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .role-badge.admin {
            background: #e3f2fd;
            color: #1565c0;
        }

        .role-badge.user {
            background: #f3e5f5;
            color: #7b1fa2;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.75rem;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .text-muted {
            color: #6c757d;
            font-style: italic;
            font-size: 0.875rem;
        }

        .activity-container {
            padding: 1.5rem;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .activity-info h4 {
            margin: 0 0 0.25rem 0;
            font-size: 0.875rem;
            font-weight: 600;
            color: #2b1e17;
        }

        .activity-info p {
            margin: 0 0 0.25rem 0;
            font-size: 0.75rem;
            color: #6b5b47;
        }

        .activity-info small {
            font-size: 0.6875rem;
            color: #999;
        }
    </style>
</body>

</html>