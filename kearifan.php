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
    <title>Kearifan Lokal | Mahligai Heritage</title>
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
        .item-gastro:hover .youtube-container { /* Changed from .item-kearifan to .item-gastro for consistency with other pages */
            transform: scale(1.02);
            transition: transform 0.4s ease;
        }

        /* Tombol Tonton Video untuk Kearifan Lokal */
        .tombol-tonton-video-kearifan {
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

        .tombol-tonton-video-kearifan:hover {
            background: linear-gradient(135deg, #cc0000, #990000);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4);
            color: white;
        }

        .tombol-tonton-video-kearifan i {
            font-size: 1.1rem;
        }


        /* Container untuk tombol-tombol */
        .button-container {
            text-align: center;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* --- CSS for Read More functionality - Localized to this page --- */
        /* Updated: Ensure all relevant elements have correct class for short/full desc */
        .item-gastro p.short-desc { /* This targets paragraphs inside .item-gastro */
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Limit to 3 lines initially */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-gastro p.full-desc { /* This targets paragraphs inside .item-gastro */
            display: none; /* Hidden by default */
            min-height: auto; /* Allow full description to take natural height */
        }

        .read-more-btn {
            background-color: transparent;
            border: none;
            color: #c82333; /* Red color as seen in your gastronomy page */
            cursor: pointer;
            padding: 5px 0;
            font-size: 0.85em;
            font-weight: 600;
            margin-top: 5px;
            display: block; /* Ensures button is on its own line */
            width: fit-content; /* Adjusts width to content */
            margin-left: auto; /* Center the button */
            margin-right: auto; /* Center the button */
        }

        .read-more-btn:hover {
            text-decoration: underline;
        }

        /* Adjustments for default <p> tags in item-gastro to work with short/full desc */
        .item-gastro p {
            min-height: 70px; /* Adjust as needed for consistent card height */
        }

        @media (max-width: 768px) {
            .youtube-container {
                height: 200px;
            }

            .button-container {
                flex-direction: column;
                align-items: center;
            }

            .tombol-tonton-video-kearifan,
            .tombol-pesan-gastro {
                margin: 5px 0;
                width: fit-content;
            }
        }

        @media (max-width: 480px) {
            .youtube-container {
                height: 180px;
            }
        }
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
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Mahligai Heritage</a>
            <div class="nav-links">
                <a href="index.php">Beranda</a>
                <a href="kearifan.php" class="active-kearifan">Kearifan Lokal</a>
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
                <h3 class="animate-fade-in">Kearifan Lokal</h3>
                <p class="ringkasan animate-fade-in delay-1s">Menjelajahi kekayaan tradisi, seni, dan budaya yang diwariskan dari generasi ke generasi di Danau Lamo.</p>
                <div class="search-container-kearifan">
                    <input type="text" placeholder="Cari seni atau tradisi..." class="search-box-kearifan" id="searchInputKearifan" onkeypress="handleSearchKearifan(event)" />
                    <button type="button" class="search-btn-kearifan" onclick="performSearchKearifanFromButton()">🔍</button>
                </div>
            </div>
        </section>

        <section id="musik-tradisional" class="bg-krem section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Musik Tradisional</h3>
                <p class="ringkasan animate-slide-up delay-05s">
                    Musik Gambangan adalah salah satu warisan tak benda Desa Danau Lamo yang masih lestari hingga kini. Dimainkan dalam berbagai upacara adat, musik ini menjadi bagian penting dari ekspresi budaya masyarakat.
                </p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="gambangan musik seni tabuhan melayu">
                        <div class="item-gastro-inner animate-pop-up">
                            <div class="youtube-container">
                                <iframe
                                    src="https://www.youtube.com/embed/your_video_id_here"
                                    title="Begambang Danau Lamo - Musik Tradisional Gambangan"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <h4>Gambangan</h4>
                            <p class="short-desc">
                                Gambangan adalah alat musik khas Jambi yang terbuat dari bambu atau kayu, menghasilkan melodi yang unik dan ritmis. Dimainkan dalam upacara adat, festival, dan acara komunitas, Gambangan menjadi simbol identitas budaya Melayu Jambi.
                            </p>
                            <p class="full-desc">
                                Gambangan adalah alat musik khas Jambi yang terbuat dari bambu atau kayu, menghasilkan melodi yang unik dan ritmis. Dimainkan dalam upacara adat, festival, dan acara komunitas, Gambangan menjadi simbol identitas budaya Melayu Jambi. Musiknya yang khas sering mengiringi tarian tradisional dan upacara penting, menunjukkan kekayaan warisan budaya di Danau Lamo.
                            </p>
                            <div class="button-container">
                                <a href="https://www.youtube.com/@mdwdesadanaulamo" target="_blank" class="tombol-tonton-video-kearifan">
                                    <i class="fab fa-youtube"></i> Tonton Video
                                </a>
                                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="anyaman-lokal" class="bg-putih section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Produk Anyaman Lokal</h3>
                <p class="ringkasan animate-slide-up delay-05s">
                    Temukan keindahan dan kekuatan anyaman tradisional Danau Lamo, dibuat dengan tangan terampil.
                </p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="tikar lapik bayi anyaman pandan rumbai simbol">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/tikar-bayi.jpg" alt="Tikar Lapik Bayi" />
                            <h4>Tikar Lapik Bayi</h4>
                            <p class="short-desc">
                                Tikar lapik bayi disusun dari lapisan tikar pandan dan rumbai (pelisir), dilapisi kain merah dan biru sebagai simbol keberanian dan wawasan luas. Ukuran dan susunannya mencerminkan jenjang harapan hidup yang tinggi dan menjunjung adat.
                            </p>
                            <p class="full-desc">
                                Tikar lapik bayi disusun dari lapisan tikar pandan dan rumbai (pelisir), dilapisi kain merah dan biru sebagai simbol keberanian dan wawasan luas. Ukuran dan susunannya mencerminkan jenjang harapan hidup yang tinggi dan menjunjung adat, serta merupakan bagian penting dari upacara tradisional kelahiran bayi di Danau Lamo.
                            </p>
                            <p><strong>Harga:</strong> Rp 40.000</p>
                            <div class="button-container">
                                <?php if ($isLoggedIn): ?>
                                    <a href="pesan-umkm.php?produk=tikar_lapik_bayi" class="tombol-pesan-umkm">Pesan Produk</a>
                                <?php else: ?>
                                    <a href="login.php?redirect=pesan-umkm.php?produk=tikar_lapik_bayi" class="tombol-pesan-gastro">
                                        <i class="fas fa-sign-in-alt"></i> Login untuk Pesan
                                    </a>
                                <?php endif; ?>
                                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                            </div>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="kembut berbucu enam tempat sirih pinang pandan ketahanan">
                        <div class="item-gastro-inner animate-pop-up delay-02s">
                            <img src="asset/kembut.jpg" alt="Kembut Berbucu Enam" />
                            <h4>Kembut Berbucu Enam</h4>
                            <p class="short-desc">
                                Tempat sirih dan pinang berbentuk segi enam yang terbuat dari daun pandan. Fungsinya untuk menyimpan benda-benda tahan lama, mencerminkan makna ketahanan dan keluhuran budaya.
                            </p>
                            <p class="full-desc">
                                Tempat sirih dan pinang berbentuk segi enam yang terbuat dari daun pandan. Fungsinya untuk menyimpan benda-benda tahan lama, mencerminkan makna ketahanan dan keluhuran budaya. Kembut seringkali digunakan dalam upacara adat dan menjadi simbol kekayaan tradisi lokal.
                            </p>
                            <p><strong>Harga:</strong> Rp 30.000</p>
                            <div class="button-container">
                                <?php if ($isLoggedIn): ?>
                                    <a href="pesan-umkm.php?produk=kembut_berbucu_enam" class="tombol-pesan-umkm">Pesan Produk</a>
                                <?php else: ?>
                                    <a href="login.php?redirect=pesan-umkm.php?produk=kembut_berbucu_enam" class="tombol-pesan-gastro">
                                        <i class="fas fa-sign-in-alt"></i> Login untuk Pesan
                                    </a>
                                <?php endif; ?>
                                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                            </div>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="tikar pandan bermotif estetika keterampilan dekorasi upacara">
                        <div class="item-gastro-inner animate-pop-up delay-04s">
                            <img src="asset/tikar.jpg" alt="Tikar Pandan Bermotif" />
                            <h4>Tikar Pandan Bermotif</h4>
                            <p class="short-desc">
                                Tikar pandan memiliki berbagai macam motif yang melambangkan nilai estetika dan keterampilan tinggi pengrajinnya. Digunakan sebagai alas, dekorasi, atau simbol upacara adat.
                            </p>
                            <p class="full-desc">
                                Tikar pandan memiliki berbagai macam motif yang melambangkan nilai estetika dan keterampilan tinggi pengrajinnya. Digunakan sebagai alas, dekorasi, atau simbol upacara adat, tikar ini sering dijumpai dalam kegiatan sehari-hari maupun perayaan khusus di Danau Lamo.
                            </p>
                            <p><strong>Harga:</strong> Rp 80.000</p>
                            <div class="button-container">
                                <?php if ($isLoggedIn): ?>
                                    <a href="pesan-umkm.php?produk=tikar_pandan_bermotif" class="tombol-pesan-umkm">Pesan Produk</a>
                                <?php else: ?>
                                    <a href="login.php?redirect=pesan-umkm.php?produk=tikar_pandan_bermotif" class="tombol-pesan-gastro">
                                        <i class="fas fa-sign-in-alt"></i> Login untuk Pesan
                                    </a>
                                <?php endif; ?>
                                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tradisi-lokal" class="bg-krem section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Produk Alam: Madu Mahligai</h3>
                <p class="ringkasan animate-slide-up delay-05s">
                    Madu asli dari lebah lokal yang dipanen secara tradisional.
                </p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="madu mahligai lebah tradisional murni">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/madu-m.jpg" alt="Madu Mahligai" />
                            <h4>Madu Mahligai</h4>
                            <p class="short-desc">
                                Madu Mahligai merupakan hasil peliharaan lebah lokal oleh masyarakat Danau Lamo yang dipanen secara tradisional tanpa merusak lingkungan. Proses pemanenan dilakukan secara turun-temurun dengan kearifan lokal, menjaga kemurnian madu sekaligus melestarikan habitat lebah dan keseimbangan alam sekitar.
                            </p>
                            <p class="full-desc">
                                Madu Mahligai merupakan hasil peliharaan lebah lokal oleh masyarakat Danau Lamo yang dipanen secara tradisional tanpa merusak lingkungan. Proses pemanenan dilakukan secara turun-temurun dengan kearifan lokal, menjaga kemurnian madu sekaligus melestarikan habitat lebah dan keseimbangan alam sekitar. Madu ini dikenal memiliki kualitas premium dan khasiat kesehatan yang tinggi.
                            </p>
                            <p><strong>Harga:</strong> Rp 50.000 / 100 ml</p>
                            <div class="button-container">
                                <?php if ($isLoggedIn): ?>
                                    <a href="pesan-umkm.php?produk=madu_mahligai" class="tombol-pesan-umkm">Pesan Produk</a>
                                <?php else: ?>
                                    <a href="login.php?redirect=pesan-umkm.php?produk=madu_mahligai" class="tombol-pesan-gastro">
                                        <i class="fas fa-sign-in-alt"></i> Login untuk Pesan
                                    </a>
                                <?php endif; ?>
                                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
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
        // JavaScript for Read More functionality - Localized to this page
        function toggleReadMore(button) {
            const parentElement = button.closest('.item-gastro-inner'); // Adjusted to find the closest parent item for descriptions
            if (!parentElement) { // Add a check to prevent errors if parent is not found
                console.error("Parent .item-gastro-inner not found for read more button.");
                return;
            }
            const shortDesc = parentElement.querySelector('.short-desc');
            const fullDesc = parentElement.querySelector('.full-desc');

            if (!shortDesc || !fullDesc) { // Add a check for descriptions
                console.error("Short or full description elements not found within the parent.");
                return;
            }

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

        // Search functions adapted for this page's items
        function handleSearchKearifan(event) {
            if (event.key === 'Enter') {
                performSearchKearifan();
            }
        }

        function performSearchKearifanFromButton() {
            performSearchKearifan();
        }

        function performSearchKearifan() {
            const searchValue = document.getElementById('searchInputKearifan').value.toLowerCase();
            // Select all relevant items on this page, including the main music section
            const kearifanItems = document.querySelectorAll('.grid-gastronomi .item-gastro');

            kearifanItems.forEach(item => {
                const keywords = item.getAttribute('data-keywords') ? item.getAttribute('data-keywords').toLowerCase() : '';
                const title = item.querySelector('h4') ? item.querySelector('h4').textContent.toLowerCase() : '';
                const shortDesc = item.querySelector('.short-desc') ? item.querySelector('.short-desc').textContent.toLowerCase() : '';
                const fullDesc = item.querySelector('.full-desc') ? item.querySelector('.full-desc').textContent.toLowerCase() : '';

                if (keywords.includes(searchValue) || title.includes(searchValue) || shortDesc.includes(searchValue) || fullDesc.includes(searchValue)) {
                    item.style.display = 'block'; // Show the item
                } else {
                    item.style.display = 'none'; // Hide the item
                }
            });
        }
    </script>
</body>

</html>