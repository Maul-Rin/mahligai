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

        /* Styling untuk icon placeholder */
        .icon-placeholder {
            position: relative;
            width: 100%;
            height: 200px;
            margin-bottom: 15px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: all 0.4s ease;
        }

        .icon-placeholder.tegak-tiang {
            background: linear-gradient(135deg, #8b4513 0%, #a0522d 50%, #cd853f 100%);
            color: white;
        }

        .icon-placeholder.baselang {
            background: linear-gradient(135deg, #2c5530 0%, #3d7c47 50%, #4a9d54 100%);
            color: white;
        }

        .icon-placeholder:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .icon-placeholder .main-icon {
            font-size: 4rem;
            margin-bottom: 10px;
            opacity: 0.9;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .icon-placeholder .icon-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 5px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .icon-placeholder .icon-subtitle {
            font-size: 0.85rem;
            opacity: 0.8;
            font-style: italic;
        }

        /* Decorative elements */
        .icon-placeholder::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
            pointer-events: none;
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Hover effect untuk video container */
        .item-gastro:hover .youtube-container {
            transform: scale(1.02);
            transition: transform 0.4s ease;
        }

        /* Styling untuk tombol tonton video yang sudah ada */
        .tombol-tonton-video {
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
        }

        .tombol-tonton-video:hover {
            background: linear-gradient(135deg, #cc0000, #990000);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 0, 0, 0.4);
            color: white;
        }

        .tombol-tonton-video i {
            font-size: 1.1rem;
        }

        /* Styling untuk tombol "Pesan Disini" */
        .tombol-pesan-disini {
            background: #dc3545;
            /* Bootstrap's danger color */
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
            /* Red shadow */
            margin-top: 20px;
            /* Add some space above the button */
        }

        .tombol-pesan-disini:hover {
            background: #c82333;
            /* Darker red on hover */
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            color: white;
        }

        /* Badge untuk menandai konten khusus */
        .content-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 255, 255, 0.9);
            color: #8b4513;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 768px) {

            .youtube-container,
            .icon-placeholder {
                height: 180px;
            }

            .icon-placeholder .main-icon {
                font-size: 3rem;
            }

            .icon-placeholder .icon-title {
                font-size: 1rem;
            }

            .icon-placeholder .icon-subtitle {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {

            .youtube-container,
            .icon-placeholder {
                height: 160px;
            }

            .icon-placeholder .main-icon {
                font-size: 2.5rem;
            }

            .icon-placeholder .icon-title {
                font-size: 0.9rem;
            }

            .icon-placeholder .icon-subtitle {
                font-size: 0.8rem;
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
                <a href="kearifan.php" class="active-kearifan">Kearifan Lokal</a>
            </div>
            <div class="user-section">
                <?php if ($isLoggedIn): ?>
                    <span class="user-name">👋 <span class="auth-text"><?php echo htmlspecialchars($user['name']); ?></span></span>
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
                    <input type="text" placeholder="Cari musik, anyaman, atau tradisi..." class="search-box-kearifan" id="searchInputKearifan" onkeypress="handleSearchKearifan(event)" />
                    <button type="button" class="search-btn-kearifan" onclick="performSearchKearifanFromButton()">🔍</button>
                </div>
            </div>
        </section>

        <section id="musik-tradisional" class="bg-krem section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Musik Tradisional: Gambangan</h3>
                <p class="ringkasan animate-slide-up delay-05s">Dengarkan melodi Gambangan yang memukau, iringan setia dalam setiap upacara adat.</p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="gambangan musik alat khas jambi tradisional upacara adat">
                        <div class="item-gastro-inner animate-pop-up">
                            <div class="youtube-container">
                                <iframe
                                    src="https://www.youtube.com/embed/8-g0MR5Wc1w"
                                    title="Begambang Danau Lamo - Musik Tradisional Gambangan"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <h4>Gambangan: Suara Warisan</h4>
                            <p>
                                Gambangan adalah alat musik khas Jambi yang digunakan dalam upacara adat dan pertunjukan rakyat untuk menyampaikan pesan dan nilai-nilai budaya. Suara khas dari alat musik ini membawa aura spiritual dan makna kebersamaan bagi masyarakat setempat.
                            </p>
                            <div style="text-align: center; margin-top: 20px;">
                                <a href="https://youtu.be/8-g0MR5Wc1w?si=zq7kOyAQtTMvkIzz" target="_blank" class="tombol-tonton-video">Tonton di YouTube <i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="anyaman-lokal" class="bg-putih section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Produk Anyaman Lokal</h3>
                <p class="ringkasan animate-slide-up delay-05s">Temukan keindahan dan kekuatan anyaman tradisional Danau Lamo, dibuat dengan tangan terampil.</p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro">
                        <img src="asset/tikar-bayi.jpg" alt="Anyaman Tikar" />
                        <h4>Tikar Lapik Bayi</h4>
                        <p>
                            Tikar lapik bayi disusun dari lapisan tikar pandan dan rumbai (pelisir), dilapisi kain merah dan biru sebagai simbol keberanian dan wawasan luas. Ukuran dan susunannya mencerminkan jenjang harapan hidup yang tinggi dan menjunjung adat.
                        </p>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="#contact" class="tombol-pesan-disini">Pesan Disini</a>
                        </div>
                    </div>
                    <div class="item-gastro">
                        <img src="asset/kembut.jpg" alt="Anyaman Kembut" />
                        <h4>Kembut Berbucu Enam</h4>
                        <p>
                            Tempat sirih dan pinang berbentuk segi enam yang terbuat dari daun pandan. Fungsinya untuk menyimpan benda-benda tahan lama, mencerminkan makna ketahanan dan keluhuran budaya.
                        </p>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="#contact" class="tombol-pesan-disini">Pesan Disini</a>
                        </div>
                    </div>
                    <div class="item-gastro">
                        <img src="asset/tikar.jpg" alt="Motif Tikar Pandan" />
                        <h4>Tikar Pandan Bermotif</h4>
                        <p>
                            Tikar pandan memiliki berbagai macam motif yang melambangkan nilai estetika dan keterampilan tinggi pengrajinnya. Digunakan sebagai alas, dekorasi, atau simbol upacara adat.
                        </p>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="#contact" class="tombol-pesan-disini">Pesan Disini</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="produk-lokal" class="bg-putih section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Produk Alam: Madu Mahligai</h3>
                <p class="ringkasan animate-slide-up delay-05s">Hasil hutan asli Danau Lamo yang murni dan menyehatkan.</p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro">
                        <img src="asset/madu.jpg" alt="Madu Mahligai" />
                        <h4>Madu Mahligai</h4>
                        <p>
                            Madu Mahligai merupakan hasil peliharaan lebah lokal oleh masyarakat Danau Lamo yang dipanen secara tradisional tanpa merusak lingkungan. Proses pemanenan dilakukan secara turun-temurun dengan kearifan lokal, menjaga kemurnian madu sekaligus melestarikan habitat lebah dan keseimbangan alam sekitar.
                        </p>
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="#contact" class="tombol-pesan-disini">Pesan Disini</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tradisi-lokal" class="bg-krem section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Tradisi Lokal</h3>
                <p class="ringkasan animate-slide-up delay-05s">Saksikan dan pahami makna di balik tradisi-tradisi yang membentuk identitas masyarakat Danau Lamo.</p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro">
                        <div class="youtube-container">
                            <iframe
                                src="https://www.youtube.com/embed/JF-sBLakP2M"
                                title="Tradisi Tkud - Danau Lamo"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                            </iframe>
                        </div>
                        <h4>Tradisi Tkud</h4>
                        <p>
                            Tkud adalah alat tiup tradisional khas Desa Danau Lamo, terbuat dari bambu dan dimainkan untuk menirukan suara burung ruak-ruak. Selain digunakan dalam praktik berburu, Tkud juga menjadi sarana hiburan malam hari dan interaksi sosial masyarakat.
                        </p>
                        <div style="text-align: center; margin-top: 15px;">
                            <a href="https://youtu.be/JF-sBLakP2M?si=aKS8EjFE7sMlhY-o" target="_blank" class="tombol-tonton-video">Tonton di YouTube <i class="fab fa-youtube"></i></a>
                        </div>
                    </div>

                    <div class="item-gastro">
                        <div class="icon-placeholder tegak-tiang">
                            <div class="content-badge">Tradisi</div>
                            <div class="main-icon">🏗️</div>
                            <div class="icon-title">Tegak Tiang Tuo</div>
                            <div class="icon-subtitle">Upacara Pendirian Rumah Adat</div>
                        </div>
                        <h4>Tradisi Tegak Tiang Tuo</h4>
                        <p>
                            Upacara pendirian rumah atau bangunan tertentu. Dilakukan bersama warga sebagai simbol kekuatan dan kebersamaan dalam membangun fondasi kehidupan yang kokoh.
                        </p>
                    </div>

                    <div class="item-gastro">
                        <div class="icon-placeholder baselang">
                            <div class="content-badge">Gotong Royong</div>
                            <div class="main-icon">🤝</div>
                            <div class="icon-title">Baselang</div>
                            <div class="icon-subtitle">Tradisi Kerja Sama</div>
                        </div>
                        <h4>Tradisi Baselang</h4>
                        <p>
                            Tradisi gotong royong saat acara besar, seperti panen atau pesta adat. Menumbuhkan rasa solidaritas dan kerja sama antarwarga dalam semangat kebersamaan yang kuat.
                        </p>
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
</body>

</html>