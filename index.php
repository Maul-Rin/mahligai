<?php
session_start();
// Cek apakah user sudah login
$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];
$user = null;
$isAdmin = false;

if ($isLoggedIn) {
    $user = [
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];

    // Cek apakah user adalah admin
    require_once 'koneksi.php';
    try {
        $stmt = $pdo->prepare("SELECT role FROM users WHERE email = ?");
        $stmt->execute([$_SESSION['user_email']]);
        $userRole = $stmt->fetch(PDO::FETCH_ASSOC);
        $isAdmin = ($userRole && $userRole['role'] === 'admin');
    } catch (Exception $e) {
        error_log("Error checking admin role: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Mahligai Heritage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


    <style>
        .admin-dashboard-btn {
            background: #d2691e !important;
            color: white !important;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 10px;
            transition: background-color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
        }

        .admin-dashboard-btn:hover {
            background: #b8541a !important;
            color: white !important;
            transform: translateY(-1px);
        }

        .admin-menu-mobile {
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 215, 0, 0.4) !important;
            color: #f4d03f !important;
            font-weight: 600 !important;
        }

        .admin-menu-mobile:hover {
            background: rgba(255, 215, 0, 0.2) !important;
            border-color: rgba(255, 215, 0, 0.6) !important;
            color: #fff !important;
        }

        /* Pastikan auth section terlihat dengan baik */
        nav .auth-section {
            display: flex !important;
            align-items: center;
            gap: 15px;
            margin-left: 30px;
        }

        /* Pastikan hanya satu dashboard yang tampil (untuk breakpoint mobile) */
        @media (max-width: 991px) {
            .admin-dashboard-btn {
                display: none !important;
            }

            nav .auth-section {
                display: none !important; /* Sembunyikan auth-section desktop di mobile */
            }
        }

        @media (min-width: 992px) {
            .admin-menu-mobile {
                display: none !important;
            }

            nav .mobile-controls {
                display: none !important;
            }
        }

        /* Styling untuk section peta lokasi - DISEDERHANAKAN */
        #lokasi {
            padding: 80px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .peta-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .peta-header {
            background: linear-gradient(135deg, #8b4513, #a0522d);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .peta-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .peta-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin: 0;
        }

        #heritage-map {
            height: 500px;
            width: 100%;
        }

        .custom-popup {
            font-family: 'Inter', sans-serif;
            text-align: center;
        }

        .custom-popup h4 {
            color: #8b4513;
            margin-bottom: 8px;
            font-size: 1.1rem;
        }

        .custom-popup p {
            margin-bottom: 5px;
            color: #555;
            line-height: 1.4;
            font-size: 0.9rem;
        }

        /* Styling untuk YouTube video embed */
        .youtube-container {
            position: relative;
            width: 100%;
            height: 200px;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .youtube-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
            border-radius: 8px;
        }

        /* Hover effect untuk video container */
        .item-kearifan:hover .youtube-container {
            transform: scale(1.02);
            transition: transform 0.4s ease;
        }

        @media (max-width: 768px) {
            #lokasi {
                padding: 60px 0;
            }

            .peta-header {
                padding: 25px 20px;
            }

            .peta-header h3 {
                font-size: 1.8rem;
            }

            .peta-header p {
                font-size: 1rem;
            }

            #heritage-map {
                height: 400px;
            }

            .youtube-container {
                height: 180px;
            }
        }

        @media (max-width: 480px) {
            .youtube-container {
                height: 160px;
            }
        }

        /* Style untuk tombol mute/unmute */
        .mute-toggle-btn {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: background-color 0.3s ease;
        }

        .mute-toggle-btn:hover {
            background: rgba(0, 0, 0, 0.7);
        }
    </style>
</head>

<body>

    <nav>
        <div class="layar-dalam">
            <div class="logo">
                <a href="index.php" class="logo-text">Mahligai Heritage</a>
            </div>
            <div class="mobile-controls">
                <div class="mobile-auth-btn">
                    <?php if ($isLoggedIn): ?>
                        <a href="logout.php" class="mobile-logout-btn" title="Logout">
                            <span class="auth-icon">👤</span> <span class="auth-text">Logout</span>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="mobile-login-btn" title="Login">
                            <span class="auth-icon">👤</span> <span class="auth-text">Login</span>
                        </a>
                    <?php endif; ?>
                </div>
                <a href="#" class="tombol-menu" onclick="toggleMobileMenu()">
                    <span class="garis"></span>
                    <span class="garis"></span>
                    <span class="garis"></span>
                </a>
            </div>
            <div class="menu" id="mobile-menu">
                <ul>
                    <li><a href="#beranda" onclick="closeMobileMenu()">Beranda</a></li>
                    <li><a href="#gastronomi" onclick="closeMobileMenu()">Gastronomi</a></li>
                    <li><a href="#kanal" onclick="closeMobileMenu()">Kanal Kuno</a></li>
                    <li><a href="#candi" onclick="closeMobileMenu()">Candi</a></li>
                    <li><a href="#kearifan" onclick="closeMobileMenu()">Kearifan Lokal</a></li>
                    <li>
                        <?php if ($isLoggedIn): ?>
                            <a href="tiket-umkm.php" onclick="closeMobileMenu()">Tiket & UMKM</a>
                        <?php else: ?>
                            <a href="#" onclick="showCustomAlert('Silakan login terlebih dahulu untuk mengakses Tiket & UMKM'); closeMobileMenu(); return false;" class="login-required">Tiket & UMKM</a>
                        <?php endif; ?>
                    </li>
                    <?php if ($isAdmin): ?>
                        <li><a href="dashboard.php" onclick="closeMobileMenu()" class="admin-menu-mobile">📊 Dashboard Admin</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="auth-section desktop-auth" id="auth-section">
                <?php if ($isLoggedIn): ?>
                    <a href="#" class="user-name-button-dark" title="Informasi Pengguna">
                        <span class="auth-icon">👋</span>
                        <span class="auth-text"><?php echo htmlspecialchars($user['name']); ?></span>
                    </a>
                    <?php if ($isAdmin): ?>
                        <a href="dashboard.php" class="admin-dashboard-btn" title="Dashboard Admin">
                            <span class="auth-icon">📊</span>
                            <span class="auth-text">Dashboard</span>
                        </a>
                        <?php else: /* User biasa login */ ?>
                        <a href="logout.php" class="logout-button-dark" title="Logout">
                            <span class="auth-icon">👤</span>
                            <span class="auth-text">Logout</span>
                        </a>
                    <?php endif; ?>
                <?php else: /* Belum login */ ?>
                    <a href="login.php" class="login-link">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="layar-penuh">
        <header id="beranda">
            <div class="overlay"></div>
            <video autoplay muted loop playsinline id="heroVideo">
                <source src="asset/Mahligai.mp4" type="video/mp4" />
            </video>
            <button class="mute-toggle-btn" id="muteToggleButton">
                <i class="fas fa-volume-mute"></i>
            </button>
            <div class="intro">
                <h3>Mahligai Heritage</h3>
                <p>Mengenal warisan budaya Danau Lamo, Kabupaten Muaro Jambi</p>
                <div class="search-container-header">
                    <input type="text" placeholder="Cari budaya..." class="search-box-header" id="searchInputHeader" onkeypress="handleSearchHeader(event)" />
                    <button type="button" class="search-btn-header" onclick="performSearchFromButtonHeader()">🔍</button>
                </div>
            </div>
        </header>

        <main>
            <section id="gastronomi" class="bg-putih">
                <div class="layar-dalam">
                    <h3>Gastronomi</h3>
                    <p class="ringkasan">Kuliner khas Danau Lamo dan festival makanan rakyat</p>
                    <div class="grid-gastronomi">
                        <div class="item-gastro">
                            <img src="asset/pindang-toman.png" alt="Pindang Toman" />
                            <h4>Pindang Toman</h4>
                            <p>Olahan ikan toman dengan bumbu asam pedas khas Jambi. Dihidangkan hangat bersama nasi, menyegarkan dan beraroma rempah kuat.</p>
                        </div>
                        <div class="item-gastro">
                            <img src="asset/nasi-cegau.png" alt="Nasi Cegau Ubi" />
                            <h4>Nasi Cegau Ubi</h4>
                            <p>Nasi tradisional yang dicampur dengan ubi kukus dan kelapa parut, menjadi santapan khas masyarakat Danau Lamo pada saat kenduri atau musim panen.</p>
                        </div>
                        <div class="item-gastro">
                            <img src="asset/festival-kenduren.png" alt="Festival Kuliner Danau Lamo" />
                            <h4>Festival Kuliner Tahunan</h4>
                            <p>Ajang tahunan warga Danau Lamo untuk memperkenalkan makanan lokal dan pertunjukan budaya kepada wisatawan.</p>
                        </div>
                    </div>
                    <div class="selengkapnya">
                        <a href="kategori-gastronomi.php" class="tombol">Selengkapnya</a>
                    </div>
                </div>
            </section>

            <section id="kanal" class="bg-krem">
                <div class="layar-dalam">
                    <h3>Susur Kanal Kuno</h3>
                    <p class="ringkasan">Kanal kuno adalah peninggalan bersejarah yang digunakan nenek moyang sebagai jalur transportasi air dan pengairan wilayah kerajaan di Muaro Jambi.</p>
                    <div class="kanal-wrapper">
                        <div class="kanal-item">
                            <img src="asset/sejarah-kanal.jpg" alt="Sejarah Kanal Kuno" />
                            <h4>Sejarah Kanal</h4>
                            <p>Kanal kuno di Muaro Jambi adalah bukti kejayaan teknologi peradaban masa lampau. Digunakan untuk transportasi, distribusi logistik, dan sistem irigasi kawasan kerajaan.</p>
                            <a href="kanal.php#sejarah-kanal" class="tombol-kanal">Baca Selengkapnya</a>
                        </div>
                        <div class="kanal-item">
                            <img src="asset/susur-pendek.jpg" alt="Paket dan Rute Jelajah Kanal" />
                            <h4>Paket & Rute Jelajah</h4>
                            <p>Nikmati pengalaman menjelajah kanal dengan perahu tradisional. Tersedia paket singkat dan meeting boat yang cocok untuk wisata budaya dan diskusi santai di atas air.</p>
                            <a href="kanal.php#paket-jelajah" class="tombol-kanal">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="candi" class="bg-putih">
                <div class="layar-dalam">
                    <h3>Jejak Candi Kuno</h3>
                    <p class="ringkasan">Situs peninggalan sejarah peradaban Melayu Kuno</p>
                    <div class="candi-flex-wrapper">
                        <div class="candi-item">
                            <img src="asset/candi-kedaton.jpeg" alt="Candi Kedaton" />
                            <h4>Candi Kedaton</h4>
                            <p>Kompleks percandian yang luas dengan temuan arkeologis seperti saluran kuno, struktur bata, dan pagar keliling. Memiliki nilai penting dalam studi peradaban masa klasik di Muaro Jambi.</p>
                            <a href="candi-kedaton.php" class="tombol-candi">Baca Selengkapnya</a>
                        </div>
                        <div class="candi-item">
                            <img src="asset/koto.jpg" alt="Candi Koto Mahligai" />
                            <h4>Candi Koto Mahligai</h4>
                            <p>Salah satu candi utama di Muaro Jambi yang dulunya menjadi pusat pendidikan Buddha. Memiliki struktur bata khas dan dikelilingi suasana alami yang menenangkan.</p>
                            <a href="candi-koto-mahligai.php" class="tombol-candi">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
            </section>

            <section id="kearifan" class="bg-putih">
                <div class="layar-dalam">
                    <h3>Kearifan Lokal</h3>
                    <p class="ringkasan">Menjelajahi kekayaan tradisi, seni, dan budaya yang diwariskan dari generasi ke generasi di Danau Lamo.</p>
                    <div class="grid-kearifan">
                        <div class="item-kearifan">
                            <div class="youtube-container">
                                <iframe
                                    src="https://www.youtube.com/embed/your_video_id_here"
                                    title="Begambang Danau Lamo - Musik Tradisional Gambangan"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <h4>Musik Tradisional: Gambangan</h4>
                            <p>Gambangan adalah alat musik khas Jambi yang digunakan dalam upacara adat dan pertunjukan rakyat untuk menyampaikan pesan budaya.</p>
                        </div>
                        <div class="item-kearifan">
                            <img src="asset/anyaman.jpg" alt="Produk Anyaman Lokal">
                            <h4>Anyaman Tradisional</h4>
                            <p>Anyaman dari bambu dan pandan yang merupakan kerajinan tangan bernilai seni tinggi dan warisan turun-temurun.</p>
                        </div>
                        <div class="item-kearifan">
                            <img src="asset/madu-m.jpg" alt="Madu Mahligai">
                            <h4>Madu Mahligai</h4>
                            <p>Madu hasil peliharaan lebah lokal di Danau Lamo yang dipanen secara tradisional. Produk ini menjadi simbol ketekunan warga dalam menjaga alam dan kearifan lokal yang berkelanjutan.</p>
                        </div>
                    </div>
                    <div class="selengkapnya">
                        <a href="kearifan.php" class="tombol">Selengkapnya</a>
                    </div>
                </div>
            </section>

            <section id="sorotan" class="bg-krem">
                <div class="layar-dalam">
                    <h3>Sorotan Tokoh</h3>
                    <p class="ringkasan">Figur publik yang mendukung budaya Danau Lamo</p>
                    <div class="grid-sorotan">
                        <div class="item-sorotan">
                            <img src="asset/gubernur.jpg" alt="Gubernur Jambi" />
                            <h4>Gubernur Jambi</h4>
                            <p>Kunjungan Bapak Gubernur Jambi, Al Haris, berkunjung ke Desa Danau Lamo.</p>
                        </div>
                        <div class="item-sorotan">
                            <img src="asset/Mentri.jpg" alt="Istri Gubernur" />
                            <h4>Menteri Desa & PDT</h4>
                            <p>Menteri Desa & PDT, Yandri Susanto, berkungjung ke Desa Danau Lamo dan mencoba berbagai kearifan lokal yang ada.</p>
                        </div>
                        <div class="item-sorotan">
                            <img src="asset/najwa.jpg" alt="Najwa Shihab" />
                            <h4>Najwa Shihab</h4>
                            <p>Najwa Shihab berkunjung ke Candi Kedaton untuk melihat peradaban yang hilang.</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="lokasi">
                <div class="layar-dalam">
                    <div class="peta-container">
                        <div class="peta-header">
                            <h3>Desa Danau Lamo</h3>
                            <p>Lokasi Candi Koto Mahligai dan Candi Kedaton</p>
                        </div>

                        <div id="heritage-map"></div>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <div class="layar-dalam">
                <div>
                    <h5>Tentang Situs</h5>
                    Platform digital budaya lokal Desa Danau Lamo
                    <p>
                        Mahligai Heritage adalah platform yang didedikasikan untuk melestarikan dan mempromosikan kekayaan budaya tak benda serta warisan sejarah yang ada di Desa Danau Lamo, Kabupaten Muaro Jambi. Kami berkomitmen untuk menyajikan informasi mendalam mengenai gastronomi, kanal kuno, situs candi, dan kearifan lokal yang telah membentuk identitas masyarakat Danau Lamo dari generasi ke generasi.
                    </p>
                </div>
                <div>
                    <h5>Kontak</h5>
                    Email: info@mahligaiheritage.id<br />
                    Telp: +62 812-3456-7890
                </div>
                <div>
                    <h5>Media Sosial</h5>
                    <ul class="social-links">
                        <li><a href="https://www.youtube.com/@mdwdesadanaulamo" target="_blank"><i class="fab fa-youtube"></i> MDW Desa Danau Lamo</a></li>
                        <li><a href="https://www.instagram.com/mahligai_danau_lamo" target="_blank"><i class="fab fa-instagram"></i> mahligai_danau_lamo</a></li>
                        <li><a href="https://www.instagram.com/danaulamo.official" target="_blank"><i class="fab fa-instagram"></i> danaulamo.official</a></li>
                        <li><a href="https://www.instagram.com/danaumahligai" target="_blank"><i class="fab fa-instagram"></i> danaumahligai</a></li>
                        <li><a href="https://www.instagram.com/mahligaibudaya_" target="_blank"><i class="fab fa-instagram"></i> mahligaibudaya_</a></li>
                        <li><a href="https://www.instagram.com/official_gambang" target="_blank"><i class="fab fa-instagram"></i> official_gambang</a></li>
                        <li><a href="https://www.facebook.com/mahligaiheritageofficial" target="_blank"><i class="fab fa-facebook"></i> Mahligai Heritage</a></li>
                    </ul>
                </div>
                <div>
                    <h5>Peta Situs</h5>
                    <ul>
                        <li><a href="#beranda">Beranda</a></li>
                        <li><a href="#gastronomi">Gastronomi</a></li>
                        <li><a href="#kanal">Kanal Kuno</a></li>
                        <li><a href="#candi">Candi</a></li>
                        <li><a href="#kearifan">Kearifan Lokal</a></li>
                        <li><a href="tiket-umkm.php">Tiket & UMKM</a></li>
                        <li><a href="#lokasi">Lokasi</a></li>
                    </ul>
                </div>
            </div>
            <div class="layar-dalam">
                <div class="copyright">© <?php echo date('Y'); ?> Mahligai Heritage</div>
            </div>
        </footer>

        <style>
            /* Tambahkan atau perbarui CSS berikut di style.css Anda */
            footer .social-links {
                list-style: none;
                /* Hapus bullet default */
                padding: 0;
                margin: 0;
            }

            footer .social-links li {
                margin-bottom: 8px;
                /* Jarak antar item */
            }

            footer .social-links a {
                color: inherit;
                /* Gunakan warna teks dari parent */
                text-decoration: none;
                transition: color 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                /* Jarak antara ikon dan teks */
            }

            footer .social-links a:hover {
                color: #d2691e;
                /* Warna hover yang cocok dengan tema */
            }

            footer .social-links a i {
                font-size: 1.2em;
                /* Ukuran ikon */
            }

            /* Styling untuk Peta Situs (jika belum ada) */
            footer div:last-child ul {
                /* Menargetkan ul di peta situs */
                list-style: none;
                padding: 0;
                margin: 0;
            }

            footer div:last-child ul li {
                margin-bottom: 8px;
            }

            footer div:last-child ul li a {
                color: inherit;
                text-decoration: none;
                transition: color 0.3s ease;
            }

            footer div:last-child ul li a:hover {
                color: #d2691e;
            }
        </style>

    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="script.js"></script>

    <script>
        // Inisialisasi peta setelah DOM loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Koordinat untuk Desa Danau Lamo dan candi-candi
            const danauLamoCenter = [-1.4851, 103.6137];
            const candiKotoMahligai = [-1.4845, 103.6140]; // Sedikit berbeda untuk menunjukkan lokasi spesifik
            const candiKedaton = [-1.4857, 103.6134]; // Sedikit berbeda untuk menunjukkan lokasi spesifik

            // Inisialisasi peta
            const map = L.map('heritage-map', {
                zoomControl: true,
                scrollWheelZoom: true
            }).setView(danauLamoCenter, 14);

            // Tambahkan tile layer OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 18,
            }).addTo(map);

            // Custom icon untuk Candi Koto Mahligai
            const candiKotoIcon = L.divIcon({
                className: 'custom-marker',
                html: `
            <div style="
                background: linear-gradient(135deg, #8b4513, #a0522d);
                color: white;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                border: 3px solid white;
                box-shadow: 0 6px 15px rgba(0,0,0,0.3);
            ">
                🏛️
            </div>
        `,
                iconSize: [45, 45],
                iconAnchor: [22.5, 22.5]
            });

            // Custom icon untuk Candi Kedaton
            const candiKedatonIcon = L.divIcon({
                className: 'custom-marker',
                html: `
            <div style="
                background: linear-gradient(135deg, #a0522d, #cd853f);
                color: white;
                border-radius: 50%;
                width: 45px;
                height: 45px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 18px;
                border: 3px solid white;
                box-shadow: 0 6px 15px rgba(0,0,0,0.3);
            ">
                🏯
            </div>
        `,
                iconSize: [45, 45],
                iconAnchor: [22.5, 22.5]
            });

            // Marker untuk Candi Koto Mahligai
            const markerKotoMahligai = L.marker(candiKotoMahligai, {
                icon: candiKotoIcon
            }).addTo(map);
            markerKotoMahligai.bindPopup(`
        <div class="custom-popup">
            <h4>🏛️ Candi Koto Mahligai</h4>
            <p>Situs bersejarah peradaban Melayu Kuno</p>
            <p>Terletak di tengah kebun karet dengan bentuk unik</p>
        </div>
    `);

            // Marker untuk Candi Kedaton
            const markerKedaton = L.marker(candiKedaton, {
                icon: candiKedatonIcon
            }).addTo(map);
            markerKedaton.bindPopup(`
        <div class="custom-popup">
            <h4>🏯 Candi Kedaton</h4>
            <p>Kompleks candi dengan struktur bata merah</p>
            <p>Lokasi wisata sejarah dan ritual budaya</p>
        </div>
    `);

            // Tambahkan area highlight untuk Desa Danau Lamo
            const desaArea = L.polygon([
                [-1.490, 103.605],
                [-1.490, 103.622],
                [-1.480, 103.622],
                [-1.480, 103.605]
            ], {
                color: '#8b4513',
                fillColor: '#8b4513',
                fillOpacity: 0.1,
                weight: 2,
                dashArray: '8, 4'
            }).addTo(map);

            desaArea.bindPopup(`
        <div class="custom-popup">
            <h4>🌾 Desa Danau Lamo</h4>
            <p>Desa bersejarah dengan warisan budaya Melayu</p>
            <p>Lokasi Candi Koto Mahligai dan Candi Kedaton</p>
        </div>
    `);

            // Tambahkan kontrol scale
            L.control.scale({
                metric: true,
                imperial: false,
                position: 'bottomright'
            }).addTo(map);

            // Event listener untuk responsive
            window.addEventListener('resize', function() {
                setTimeout(function() {
                    map.invalidateSize();
                }, 100);
            });

            // Auto-open popup untuk Candi Koto Mahligai setelah delay
            setTimeout(() => {
                markerKotoMahligai.openPopup();
            }, 1500);
        });

        // Script untuk mute/unmute video
        document.addEventListener('DOMContentLoaded', () => {
            const video = document.getElementById('heroVideo');
            const muteButton = document.getElementById('muteToggleButton');
            const muteIcon = muteButton.querySelector('i');

            if (video && muteButton && muteIcon) {
                // Set initial state based on HTML (muted)
                video.muted = true;
                muteIcon.classList.remove('fa-volume-up');
                muteIcon.classList.add('fa-volume-mute');

                muteButton.addEventListener('click', () => {
                    if (video.muted) {
                        video.muted = false;
                        muteIcon.classList.remove('fa-volume-mute');
                        muteIcon.classList.add('fa-volume-up');
                    } else {
                        video.muted = true;
                        muteIcon.classList.remove('fa-volume-up');
                        muteIcon.classList.add('fa-volume-mute');
                    }
                });
            }
        });
    </script>

    <?php if ($isLoggedIn && isset($_GET['login']) && $_GET['login'] == 'success'): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                showCustomAlert('Selamat datang, <strong><?php echo htmlspecialchars($user['name']); ?></strong>! Anda berhasil login ke Mahligai Heritage.');
            });
        </script>
    <?php endif; ?>

</body>

</html>