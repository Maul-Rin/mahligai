<?php
session_start();
require_once 'check-admin.php';
requireAdmin();

$user = [
    'name' => $_SESSION['user_name'],
    'email' => $_SESSION['user_email'],
    'id' => $_SESSION['user_id']
];

require_once 'koneksi.php';

$message = '';
$message_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        try {
            // Verify current password if new password is provided
            if (!empty($new_password)) {
                $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
                $stmt->execute([$user['id']]);
                $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user_data || !password_verify($current_password, $user_data['password'])) {
                    throw new Exception('Password saat ini tidak benar');
                }

                // Update with new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $email, $hashed_password, $user['id']]);
            } else {
                // Update without password change
                $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$name, $email, $user['id']]);
            }

            // Update session
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $user['name'] = $name;
            $user['email'] = $email;

            $message = 'Profil berhasil diperbarui!';
            $message_type = 'success';
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// Get system info
$server_info = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
$php_version = PHP_VERSION;
$db_version = '';
try {
    $stmt = $pdo->query("SELECT VERSION() as version");
    $db_version = $stmt->fetch(PDO::FETCH_ASSOC)['version'] ?? 'Unknown';
} catch (Exception $e) {
    $db_version = 'Unknown';
}

// Get database stats
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pesan_tiket");
    $total_tickets = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pesan_umkm");
    $total_umkm = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
} catch (Exception $e) {
    $total_users = $total_tickets = $total_umkm = 0;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan | Mahligai Heritage</title>
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
                <a href="pengguna.php"><i class="fas fa-users"></i> Pengguna</a>
                <a href="pengaturan.php" class="active"><i class="fas fa-cog"></i> Pengaturan</a>
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
                    <h1 class="admin-title">Pengaturan</h1>
                </div>
                <div class="user-info">
                    <div class="user-avatar"><?php echo strtoupper(substr($user['name'], 0, 1)); ?></div>
                    <div class="user-details">
                        <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </header>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <div class="settings-grid">
                <!-- Profile Settings -->
                <section class="admin-section">
                    <h2 class="section-title">
                        <i class="fas fa-user-cog"></i>
                        Profil Administrator
                    </h2>
                    <form method="POST" class="settings-form">
                        <input type="hidden" name="action" value="update_profile">

                        <div class="form-group">
                            <label for="name">
                                <i class="fas fa-user"></i>
                                Nama Lengkap
                            </label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">
                                <i class="fas fa-envelope"></i>
                                Email
                            </label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="form-divider">
                            <span>Ubah Password (Opsional)</span>
                        </div>

                        <div class="form-group">
                            <label for="current_password">
                                <i class="fas fa-lock"></i>
                                Password Saat Ini
                            </label>
                            <input type="password" id="current_password" name="current_password" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>

                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-key"></i>
                                Password Baru
                            </label>
                            <input type="password" id="new_password" name="new_password" placeholder="Kosongkan jika tidak ingin mengubah password">
                        </div>

                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save"></i>
                            Simpan Perubahan
                        </button>
                    </form>
                </section>

                <!-- System Information -->
                <section class="admin-section">
                    <h2 class="section-title">
                        <i class="fas fa-server"></i>
                        Informasi Sistem
                    </h2>
                    <div class="system-info">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-server"></i>
                            </div>
                            <div class="info-content">
                                <h4>Server</h4>
                                <p><?php echo htmlspecialchars($server_info); ?></p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fab fa-php"></i>
                            </div>
                            <div class="info-content">
                                <h4>PHP Version</h4>
                                <p><?php echo htmlspecialchars($php_version); ?></p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-database"></i>
                            </div>
                            <div class="info-content">
                                <h4>Database</h4>
                                <p><?php echo htmlspecialchars($db_version); ?></p>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-content">
                                <h4>Last Login</h4>
                                <p><?php echo date('d M Y H:i:s'); ?></p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Database Statistics -->
                <section class="admin-section">
                    <h2 class="section-title">
                        <i class="fas fa-chart-pie"></i>
                        Statistik Database
                    </h2>
                    <div class="db-stats">
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($total_users); ?></div>
                            <div class="stat-label">Total Pengguna</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($total_tickets); ?></div>
                            <div class="stat-label">Pesanan Tiket</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number"><?php echo number_format($total_umkm); ?></div>
                            <div class="stat-label">Pesanan UMKM</div>
                        </div>
                    </div>
                </section>

                <!-- Quick Actions -->
                <section class="admin-section">
                    <h2 class="section-title">
                        <i class="fas fa-bolt"></i>
                        Aksi Cepat
                    </h2>
                    <div class="quick-actions">
                        <a href="dashboard.php" class="action-btn">
                            <i class="fas fa-home"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="pengguna.php" class="action-btn">
                            <i class="fas fa-users"></i>
                            <span>Kelola Pengguna</span>
                        </a>
                        <a href="analytics.php" class="action-btn">
                            <i class="fas fa-chart-bar"></i>
                            <span>Lihat Analytics</span>
                        </a>
                        <a href="index.php" class="action-btn">
                            <i class="fas fa-globe"></i>
                            <span>Kunjungi Website</span>
                        </a>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <script>
        function toggleSidebar() {
            document.querySelector('.admin-sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        // Password validation
        document.getElementById('new_password').addEventListener('input', function() {
            const currentPassword = document.getElementById('current_password');
            if (this.value && !currentPassword.value) {
                currentPassword.required = true;
                currentPassword.placeholder = 'Wajib diisi untuk mengubah password';
            } else if (!this.value) {
                currentPassword.required = false;
                currentPassword.placeholder = 'Kosongkan jika tidak ingin mengubah password';
            }
        });
    </script>

    <style>
        .alert {
            margin: 2rem;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin: 2rem;
        }

        .settings-form {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            color: #8b4513;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e8dcc6;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #8b4513;
            box-shadow: 0 0 0 3px rgba(139, 69, 19, 0.1);
        }

        .form-divider {
            margin: 2rem 0;
            text-align: center;
            position: relative;
        }

        .form-divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e8dcc6;
        }

        .form-divider span {
            background: white;
            padding: 0 1rem;
            color: #6b5b47;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.2s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 69, 19, 0.3);
        }

        .system-info,
        .db-stats,
        .quick-actions {
            padding: 2rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-content h4 {
            margin: 0 0 0.25rem 0;
            font-size: 0.875rem;
            font-weight: 600;
            color: #2b1e17;
        }

        .info-content p {
            margin: 0;
            font-size: 0.75rem;
            color: #6b5b47;
        }

        .db-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 1.5rem 1rem;
            background: linear-gradient(135deg, #fff6ee, #fff1e0);
            border-radius: 8px;
            border: 1px solid #e8dcc6;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #8b4513;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.75rem;
            color: #6b5b47;
            font-weight: 500;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 1rem;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 1.5rem 1rem;
            background: white;
            border: 2px solid #e8dcc6;
            border-radius: 8px;
            text-decoration: none;
            color: #8b4513;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            border-color: #8b4513;
            background: rgba(139, 69, 19, 0.05);
            transform: translateY(-2px);
        }

        .action-btn i {
            font-size: 1.5rem;
        }

        .action-btn span {
            font-size: 0.875rem;
            font-weight: 500;
        }

        @media (max-width: 992px) {
            .settings-grid {
                grid-template-columns: 1fr;
                margin: 1rem;
            }
        }
    </style>
</body>

</html>