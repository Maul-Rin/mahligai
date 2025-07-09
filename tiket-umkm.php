<?php
session_start();
// Check if user is logged in
if (!isset($_SESSION['is_logged_in'])) {
    header('Location: login.php');
    exit();
}

$user = [
    'name' => $_SESSION['user_name'] ?? '',
    'email' => $_SESSION['user_email'] ?? ''
];

// Check if user is admin
$isAdmin = false;
require_once 'koneksi.php';

try {
    $stmt = $pdo->prepare("SELECT role FROM users WHERE email = ?");
    $stmt->execute([$user['email']]);
    $userRole = $stmt->fetch(PDO::FETCH_ASSOC);
    $isAdmin = ($userRole && $userRole['role'] === 'admin');
} catch (Exception $e) {
    error_log("Error checking admin role: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket & UMKM | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="style.css?v=<?= time() ?>">
    <style>
        .admin-dashboard-link {
            color: #d2691e !important;
            font-weight: bold !important;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: rgba(210, 105, 30, 0.1);
            border: 1px solid rgba(210, 105, 30, 0.2);
        }

        .admin-dashboard-link:hover {
            background: rgba(210, 105, 30, 0.2) !important;
            color: #b8541a !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(210, 105, 30, 0.3);
        }

        .tombol-pesan {
            background: #dc3545;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            margin-top: 20px;
        }

        .tombol-pesan:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            color: white;
        }

        .grid-gastronomi {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            align-items: stretch;
        }

        .item-gastro {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .item-gastro img {
            height: 200px;
            object-fit: cover;
            width: 100%;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .item-gastro p {
            min-height: 100px;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .price-display {
            font-weight: 700;
            color: #2c5530;
            margin-top: 10px;
            font-size: 1.1em;
            text-align: center;
        }

        .button-container {
            text-align: center;
            margin-top: auto;
            padding-top: 10px;
        }

        /* Search highlight styles for tiket page */
        .highlight-tiket {
            background: rgba(255, 193, 7, 0.3) !important;
            border: 2px solid #ffc107 !important;
            border-radius: 8px !important;
            transform: scale(1.02) !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(255, 193, 7, 0.4) !important;
        }

        @media (max-width: 991px) {
            .admin-dashboard-link {
                font-size: 13px !important;
                padding: 0.3rem 0.6rem !important;
            }
        }

        @media (max-width: 768px) {
            .button-container {
                flex-direction: column;
                align-items: center;
            }

            .tombol-pesan {
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Mahligai Heritage</a>
            <div class="nav-links">
                <a href="index.php">Beranda</a>
                <a href="tiket-umkm.php" class="active-tiket">Tiket & UMKM</a>
                <?php if ($isAdmin): ?>
                    <a href="dashboard.php" class="admin-dashboard-link">📊 Dashboard Admin</a>
                <?php endif; ?>
            </div>
            <a href="#" class="tombol-menu" onclick="toggleMobileMenu(); return false;">
                <span class="garis"></span>
                <span class="garis"></span>
                <span class="garis"></span>
            </a>
            <div class="user-section">
                <span class="user-name">👋 <span class="auth-text"><?= htmlspecialchars($user['name']) ?></span></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <main style="margin-top:0;">
        <section class="bg-putih section-intro" style="padding-top: 100px;">
            <div class="layar-dalam">
                <h3 class="animate-fade-in">Tiket & UMKM</h3>
                <p class="ringkasan animate-fade-in delay-1s">Pesan tiket wisata dan produk UMKM lokal Danau Lamo dengan mudah dan terpercaya.</p>
                <div class="search-container-tiket">
                    <input type="text" placeholder="Cari tiket atau produk UMKM..." class="search-box-tiket" id="searchInputTiket">
                    <button type="button" class="search-btn-tiket" id="searchButtonTiket">🔍</button>
                </div>
            </div>
        </section>

        <section id="tiket-wisata" class="bg-krem section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Tiket Wisata</h3>
                <p class="ringkasan animate-slide-up delay-05s">Jelajahi keindahan dan sejarah Danau Lamo dengan berbagai pilihan paket wisata.</p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="candi kedaton sejarah bata merah kompleks wisata">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/candi-kedaton.jpeg" alt="Candi Kedaton">
                            <h4>Candi Kedaton</h4>
                            <p>Situs utama dalam kompleks percandian Muaro Jambi, menyimpan misteri sejarah kejayaan peradaban Buddha-Hindu. Candi ini memiliki struktur arsitektur yang luar biasa lengkap dan diyakini sebagai tempat meditasi Mpu Kusuma.</p>
                            <div class="price-display">Rp 5.000 / orang</div>
                            <div class="button-container">
                                <a href="pesan-tiket.php?jenis=candi_kedaton" class="tombol-pesan">Pesan Tiket</a>
                            </div>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="candi koto mahligai kebun karet unik wisata">
                        <div class="item-gastro-inner animate-pop-up delay-02s">
                            <img src="asset/candi-koto.png" alt="Candi Koto Mahligai">
                            <h4>Candi Koto Mahligai</h4>
                            <p>Peninggalan penting Kerajaan Melayu Kuno, dipercaya menjadi pusat pendidikan dan spiritual Buddha abad ke-7 hingga ke-9 Masehi. Struktur bata merah tanpa semen dengan lingkungan asri.</p>
                            <div class="price-display">Rp 5.000 / orang</div>
                            <div class="button-container">
                                <a href="pesan-tiket.php?jenis=candi_mahligai" class="tombol-pesan">Pesan Tiket</a>
                            </div>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="paket singkat susur kanal cepat 15 menit wisata perahu">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/susur-pendek.jpg" alt="Paket Jelajah Singkat">
                            <h4>Paket Singkat</h4>
                            <p>Paket ini cocok untuk pengunjung yang ingin mencoba sensasi menyusuri kanal dalam waktu singkat. Perjalanan berdurasi ±15 menit menyusuri jalur utama dengan perahu tradisional sambil mendengar penjelasan singkat dari pemandu.</p>
                            <div class="price-display">Rp 15.000 / orang</div>
                            <div class="button-container">
                                <a href="pesan-tiket.php?jenis=paket_singkat" class="tombol-pesan">Pesan Tiket</a>
                            </div>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="paket meeting boat kanal panjang 1 jam perahu wisata eksklusif">
                        <div class="item-gastro-inner animate-pop-up delay-02s">
                            <img src="asset/susur-panjang.png" alt="Paket Meeting Boat">
                            <h4>Paket Meeting Boat</h4>
                            <p>Paket eksklusif berdurasi ±1 jam dengan perahu yang lebih besar, cocok untuk kelompok kecil hingga 16 orang. Di atas perahu, peserta bisa menikmati obrolan santai sambil menyantap cemilan dan minuman ringan yang telah disediakan.</p>
                            <div class="price-display">Rp 1.450.000 / per sesi</div>
                            <div class="button-container">
                                <a href="pesan-tiket.php?jenis=meeting_boat" class="tombol-pesan">Pesan Tiket</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="produk-umkm" class="bg-putih section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Produk UMKM Lokal</h3>
                <p class="ringkasan animate-slide-up delay-05s">Dukung ekonomi lokal dengan membeli produk kerajinan dan makanan khas Danau Lamo.</p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="tikar lapik bayi anyaman tradisional pandan umkm kerajinan">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/tikar-bayi.jpg" alt="Anyaman Tikar Lapik Bayi">
                            <h4>Tikar Lapik Bayi</h4>
                            <p>Tikar lapik bayi disusun dari lapisan tikar pandan dan rumbai (pelisir), dilapisi kain merah dan biru sebagai simbol keberanian dan wawasan luas. Ukuran dan susunannya mencerminkan jenjang harapan hidup yang tinggi dan menjunjung adat.</p>
                            <div class="price-display">Harga: Rp 40.000</div>
                            <div class="button-container">
                                <a href="pesan-umkm.php?produk=tikar_lapik_bayi" class="tombol-pesan">Pesan Produk</a>
                            </div>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="kembut berbucu enam anyaman tempat sirih pinang umkm kerajinan">
                        <div class="item-gastro-inner animate-pop-up delay-02s">
                            <img src="asset/kembut.jpg" alt="Anyaman Kembut Berbucu Enam">
                            <h4>Kembut Berbucu Enam</h4>
                            <p>Tempat sirih dan pinang berbentuk segi enam yang terbuat dari daun pandan. Fungsinya untuk menyimpan benda-benda tahan lama, mencerminkan makna ketahanan dan keluhuran budaya.</p>
                            <div class="price-display">Harga: Rp 30.000</div>
                            <div class="button-container">
                                <a href="pesan-umkm.php?produk=kembut_berbucu_enam" class="tombol-pesan">Pesan Produk</a>
                            </div>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="tikar pandan bermotif anyaman estetika keterampilan umkm kerajinan">
                        <div class="item-gastro-inner animate-pop-up delay-04s">
                            <img src="asset/tikar.jpg" alt="Motif Tikar Pandan">
                            <h4>Tikar Pandan Bermotif</h4>
                            <p>Tikar pandan memiliki berbagai macam motif yang melambangkan nilai estetika dan keterampilan tinggi pengrajinnya. Digunakan sebagai alas, dekorasi, atau simbol upacara adat.</p>
                            <div class="price-display">Harga: Rp 80.000</div>
                            <div class="button-container">
                                <a href="pesan-umkm.php?produk=tikar_pandan_bermotif" class="tombol-pesan">Pesan Produk</a>
                            </div>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="madu mahligai madu hutan alami murni danau lamo umkm makanan">
                        <div class="item-gastro-inner animate-pop-up delay-06s">
                            <img src="asset/madu.jpg" alt="Madu Mahligai">
                            <h4>Madu Mahligai</h4>
                            <p>Madu Mahligai merupakan hasil peliharaan lebah lokal oleh masyarakat Danau Lamo yang dipanen secara tradisional tanpa merusak lingkungan. Proses pemanenan dilakukan secara turun-temurun dengan kearifan lokal.</p>
                            <div class="price-display">Harga: Rp 50.000 / 250 ml</div>
                            <div class="button-container">
                                <a href="pesan-umkm.php?produk=madu_mahligai" class="tombol-pesan">Pesan Produk</a>
                            </div>
                        </div>
                    </div>
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
                    <li><a href="https://www.instagram.com/kampungtradisional.official" target="_blank"><i class="fab fa-instagram"></i> kampungtradisional.official</a></li>
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

    <script src="script.js"></script>
    <script>
        // Script khusus untuk halaman tiket - pastikan event listener terpasang
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Tiket page loaded, setting up search...');

            const searchInput = document.getElementById('searchInputTiket');
            const searchButton = document.getElementById('searchButtonTiket');

            if (searchInput && searchButton) {
                console.log('Search elements found, adding event listeners...');

                // Event listener untuk tombol
                searchButton.addEventListener('click', function() {
                    console.log('Search button clicked!');
                    const searchTerm = searchInput.value.toLowerCase().trim();
                    if (searchTerm === '') {
                        alert('Masukkan kata kunci pencarian!');
                        return;
                    }
                    performTiketSearch(searchTerm);
                });

                // Event listener untuk Enter key
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        console.log('Enter key pressed!');
                        e.preventDefault();
                        const searchTerm = e.target.value.toLowerCase().trim();
                        if (searchTerm === '') {
                            alert('Masukkan kata kunci pencarian!');
                            return;
                        }
                        performTiketSearch(searchTerm);
                    }
                });

                console.log('Event listeners added successfully!');
            } else {
                console.error('Search elements not found!');
            }
        });

        // Fungsi pencarian khusus untuk tiket
        function performTiketSearch(searchTerm) {
            console.log('Performing search for:', searchTerm);

            const items = document.querySelectorAll('.grid-interactive .item-gastro');
            console.log('Found items:', items.length);

            // Reset semua highlight
            items.forEach(item => {
                item.classList.remove('highlight-tiket');
                item.style.display = 'block';
            });

            const matchingItems = [];

            // Cari item yang cocok
            items.forEach(item => {
                const keywords = item.getAttribute('data-keywords') || '';
                const title = item.querySelector('h4') ? item.querySelector('h4').textContent.toLowerCase() : '';
                const description = item.querySelector('p') ? item.querySelector('p').textContent.toLowerCase() : '';

                if (keywords.includes(searchTerm) || title.includes(searchTerm) || description.includes(searchTerm)) {
                    matchingItems.push(item);
                    console.log('Match found:', title);
                }
            });

            console.log('Total matches:', matchingItems.length);

            if (matchingItems.length > 0) {
                // Scroll ke item pertama
                matchingItems[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Highlight semua item yang cocok
                matchingItems.forEach(item => {
                    item.classList.add('highlight-tiket');
                });

                // Hapus highlight setelah 3 detik
                setTimeout(() => {
                    matchingItems.forEach(item => {
                        item.classList.remove('highlight-tiket');
                    });
                }, 3000);

                // Clear search input
                document.getElementById('searchInputTiket').value = '';

            } else {
                alert(`Tidak ditemukan hasil untuk "${searchTerm}". Coba kata kunci lain seperti: candi, tikar, madu, wisata, umkm`);
            }
        }
    </script>
</body>

</html>