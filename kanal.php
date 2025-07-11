<?php
session_start();
// Cek apakah user sudah login
$isLoggedIn = isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];
$user = null;

if ($isLoggedIn) {
    $user = [
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email']
    ];
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Susur Kanal Kuno | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Styling untuk YouTube video embed */
        .youtube-container {
            position: relative;
            width: 100%;
            height: 220px;
            margin-bottom: 20px;
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
        .item-gastro:hover .youtube-container {
            transform: scale(1.02);
            transition: transform 0.4s ease;
        }

        /* Tombol YouTube untuk kanal */
        .tombol-youtube-kanal {
            background: linear-gradient(135deg, #ff0000, #cc0000);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 0, 0, 0.3);
            margin-top: 15px;
            margin-left: 10px;
        }

        .tombol-youtube-kanal:hover {
            background: linear-gradient(135deg, #cc0000, #990000);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4);
            color: white;
        }

        .tombol-youtube-kanal i {
            font-size: 1.1rem;
        }

        /* Styling untuk tombol pesan yang sudah ada (akan digantikan oleh tombol-pesan-umkm) */
        /* .tombol-pesan-kanal { ... } */

        /* Container untuk tombol-tombol */
        .button-container {
            text-align: center;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* --- START: New CSS for Read More --- */
        .item-gastro p.short-desc {
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Limit to 3 lines initially */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-gastro p.full-desc {
            display: none; /* Hidden by default */
        }

        .read-more-btn {
            background-color: transparent;
            border: none;
            color: #c82333; /* Adjust color to match your theme */
            cursor: pointer;
            padding: 5px 0;
            font-size: 0.85em;
            font-weight: 600;
            margin-top: 5px;
            display: block; /* Ensures button is on its own line */
            width: fit-content; /* Adjusts width to content */
        }

        .read-more-btn:hover {
            text-decoration: underline;
        }
        /* --- END: New CSS for Read More --- */


        @media (max-width: 768px) {
            .youtube-container {
                height: 200px;
            }

            .button-container {
                flex-direction: column;
                align-items: center;
            }

            /* Pastikan tombol-pesan-umkm juga responsif */
            .tombol-youtube-kanal,
            .tombol-pesan-kanal, /* tetap biarkan ini jika masih ada tombol yang pakai */
            .tombol-pesan-gastro,
            .tombol-pesan-umkm { /* Tambahkan tombol-pesan-umkm di sini untuk mobile responsif */
                margin: 5px 0;
                width: fit-content;
            }
        }

        @media (max-width: 480px) {
            .youtube-container {
                height: 180px;
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
                <a href="kanal.php" class="active-kanal">Kanal Kuno</a>
            </div>
            <div class="user-section">
                <?php if ($isLoggedIn): ?>
                    <span class="user-name">👋 <?php echo htmlspecialchars($user['name']); ?></span>
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="logout-btn login-btn-custom">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main style="margin-top:0;">
        <section class="bg-putih section-intro" style="padding-top: 100px;">
            <div class="layar-dalam">
                <h3 class="animate-fade-in">Susur Kanal Kuno</h3>
                <p class="ringkasan animate-fade-in delay-1s">Menelusuri jejak sejarah dan menikmati pengalaman wisata kanal bersejarah di Muaro Jambi</p>
                <div class="search-container-kanal">
                    <input type="text" placeholder="Cari sejarah atau paket..." class="search-box-kanal" id="searchInputKanal" onkeypress="handleSearchKanal(event)" />
                    <button type="button" class="search-btn-kanal" onclick="performSearchKanalFromButton()">🔍</button>
                </div>
            </div>
        </section>

        <section id="sejarah-kanal" class="bg-krem section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Sejarah Kanal Kuno</h3>
                <p class="ringkasan animate-slide-up delay-05s">
                    Kanal Kuno di kawasan Muaro Jambi merupakan jalur air buatan yang dibangun oleh peradaban Melayu Kuno sebagai sarana transportasi utama pada masa lampau. Kanal ini menghubungkan antara situs cagar budaya, permukiman, dan lahan pertanian kerajaan.
                </p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="jejak sejarah fungsi transportasi irigasi distribusi">
                        <div class="item-gastro-inner animate-pop-up">
                            <div class="youtube-container">
                                <iframe
                                    src="https://www.youtube.com/embed/b0e3AC-rLQc"
                                    title="Menelusuri Kanal Kuno - Sejarah Kanal Danau Lamo"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <h4>Jejak Sejarah dan Peran Kanal</h4>
                            <p class="short-desc">
                                Dibangun dengan teknik tradisional, kanal ini menjadi bagian penting dari sistem logistik dan pengelolaan air. Selain mengangkut bahan bangunan dan hasil bumi, kanal juga mempermudah akses antar wilayah pada masa kerajaan. Hari ini, kanal tersebut menjadi saksi bisu kemajuan teknologi hidrologi kuno yang dapat dinikmati kembali melalui wisata budaya.
                            </p>
                            <p class="full-desc">
                                Dibangun dengan teknik tradisional, kanal ini menjadi bagian penting dari sistem logistik dan pengelolaan air. Selain mengangkut bahan bangunan dan hasil bumi, kanal juga mempermudah akses antar wilayah pada masa kerajaan. Hari ini, kanal tersebut menjadi saksi bisu kemajuan teknologi hidrologi kuno yang dapat dinikmati kembali melalui wisata budaya. Kanal-kanal ini tidak hanya berfungsi sebagai jalur transportasi, tetapi juga berperan dalam irigasi dan distribusi air untuk lahan pertanian, menunjukkan kecanggihan peradaban Melayu Kuno dalam mengelola sumber daya alam.
                            </p>
                            <div class="button-container">
                                <a href="https://youtu.be/b0e3AC-rLQc?si=M7sCOpT7tcWEqfyf" target="_blank" class="tombol-youtube-kanal">
                                    <i class="fab fa-youtube"></i> Tonton di YouTube
                                </a>
                                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="paket-jelajah" class="bg-putih section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Paket & Rute Jelajah</h3>
                <p class="ringkasan animate-slide-up delay-05s">
                    Nikmati pengalaman menyusuri kanal kuno seperti para leluhur dahulu. Tersedia dua pilihan paket wisata: Paket Singkat dan Paket "Meeting Boat", yang menawarkan suasana tenang di atas air sambil menikmati pemandangan dan cerita sejarah.
                </p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="paket singkat susur kanal cepat 15 menit">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/susur-pendek.jpg" alt="Paket Jelajah Singkat" />
                            <h4>Paket Singkat</h4>
                            <p class="short-desc">
                                Paket ini cocok untuk pengunjung yang ingin mencoba sensasi menyusuri kanal dalam waktu singkat. Perjalanan berdurasi &plusmn;15 menit menyusuri jalur utama dengan perahu tradisional sambil mendengar penjelasan singkat dari pemandu.
                            </p>
                            <p class="full-desc">
                                Paket ini cocok untuk pengunjung yang ingin mencoba sensasi menyusuri kanal dalam waktu singkat. Perjalanan berdurasi &plusmn;15 menit menyusuri jalur utama dengan perahu tradisional sambil mendengar penjelasan singkat dari pemandu. Rute singkat ini memberikan gambaran umum tentang keindahan kanal dan cocok bagi mereka yang memiliki waktu terbatas namun tetap ingin merasakan pengalaman unik ini.
                            </p>
                            <p><strong>Harga:</strong> Rp 15.000 / orang</p>
                            <div class="button-container">
                                <?php if ($isLoggedIn): ?>
                                    <a href="pesan-tiket.php?jenis=paket_singkat" class="tombol-pesan-umkm">Pesan Tiket</a>
                                <?php else: ?>
                                    <a href="login.php?redirect=pesan-tiket.php?jenis=paket_singkat" class="tombol-pesan-gastro">
                                        <i class="fas fa-sign-in-alt"></i> Login untuk Pesan
                                    </a>
                                <?php endif; ?>
                                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                            </div>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="paket meeting boat kanal panjang 1 jam perahu wisata eksklusif">
                        <div class="item-gastro-inner animate-pop-up delay-02s">
                            <img src="asset/susur-panjang.png" alt="Paket Meeting Boat" />
                            <h4>Paket Meeting Boat</h4>
                            <p class="short-desc">
                                Paket eksklusif berdurasi &plusmn;1 jam dengan perahu yang lebih besar, cocok untuk kelompok kecil hingga 16 orang. Di atas perahu, peserta bisa menikmati obrolan santai sambil menyantap cemilan dan minuman ringan yang telah disediakan. Cocok untuk komunitas, keluarga, atau instansi yang ingin mengadakan pertemuan berkonsep wisata.
                            </p>
                            <p class="full-desc">
                                Paket eksklusif berdurasi &plusmn;1 jam dengan perahu yang lebih besar, cocok untuk kelompok kecil hingga 16 orang. Di atas perahu, peserta bisa menikmati obrolan santai sambil menyantap cemilan dan minuman ringan yang telah disediakan. Cocok untuk komunitas, keluarga, atau instansi yang ingin mengadakan pertemuan berkonsep wisata yang berbeda dan lebih intim. Pemandu akan memberikan penjelasan lebih mendalam tentang sejarah kanal dan sekitarnya selama perjalanan.
                            </p>
                            <p><strong>Harga:</strong> Rp 1.450.000 / per sesi (maks. 16 orang)</p>
                            <div class="button-container">
                                <?php if ($isLoggedIn): ?>
                                    <a href="pesan-tiket.php?jenis=paket_meeting_boat" class="tombol-pesan-umkm">Pesan Tiket</a>
                                <?php else: ?>
                                    <a href="login.php?redirect=pesan-tiket.php?jenis=paket_meeting_boat" class="tombol-pesan-gastro">
                                        <i class="fas fa-sign-in-alt"></i> Login untuk Pesan
                                    </a>
                                <?php endif; ?>
                                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="info-kanal" style="margin-top: 30px; text-align: center;">
                    <p><em>Semua paket telah termasuk pelampung keselamatan dan didampingi oleh pemandu lokal berpengalaman.</em></p>
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

    <script>
        // This function should be added to your existing script.js or placed here
        function toggleReadMore(button) {
            const itemGastroInner = button.closest('.item-gastro-inner');
            const shortDesc = itemGastroInner.querySelector('.short-desc');
            const fullDesc = itemGastroInner.querySelector('.full-desc');

            if (shortDesc.style.display === 'none') {
                // Currently showing full description, switch to short
                shortDesc.style.display = '-webkit-box'; // Re-apply truncated style
                fullDesc.style.display = 'none';
                button.textContent = 'Selengkapnya';
            } else {
                // Currently showing short description, switch to full
                shortDesc.style.display = 'none';
                fullDesc.style.display = 'block';
                button.textContent = 'Sembunyikan';
            }
        }

        // Keep your existing search functions
        function handleSearchKanal(event) {
            if (event.key === 'Enter') {
                performSearchKanal();
            }
        }

        function performSearchKanalFromButton() {
            performSearchKanal();
        }

        function performSearchKanal() {
            const searchValue = document.getElementById('searchInputKanal').value.toLowerCase();
            const gastroItems = document.querySelectorAll('.item-gastro');

            gastroItems.forEach(item => {
                const keywords = item.getAttribute('data-keywords').toLowerCase();
                const title = item.querySelector('h4').textContent.toLowerCase();
                const shortDesc = item.querySelector('.short-desc') ? item.querySelector('.short-desc').textContent.toLowerCase() : '';
                const fullDesc = item.querySelector('.full-desc') ? item.querySelector('.full-desc').textContent.toLowerCase() : ''; // Get full description content

                if (keywords.includes(searchValue) || title.includes(searchValue) || shortDesc.includes(searchValue) || fullDesc.includes(searchValue)) {
                    item.style.display = 'block'; // Show the item
                } else {
                    item.style.display = 'none'; // Hide the item
                }
            });
        }

        // Existing script.js content (if any) should be included or linked
        // For example, if you have other animations or mobile menu toggles, keep them.
    </script>
</body>

</html>