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
    <title>Kategori Gastronomi | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Mahligai Heritage</a>

            <div class="nav-links">
                <a href="index.php">Beranda</a>
                <a href="kategori-gastronomi.php" class="active-gastronomi">Gastronomi</a>
            </div>

            <div class="user-section">
                <?php if ($isLoggedIn): ?>
                    <span class="user-name">&#128075; <?php echo htmlspecialchars($user['name']); ?></span>
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
                <h3 class="animate-fade-in">Gastronomi Danau Lamo</h3>
                <p class="ringkasan animate-fade-in delay-1s">
                    Gastronomi bukan hanya soal makanan, tetapi juga soal nilai, sejarah, dan kebudayaan dari sebuah daerah.
                    Di Danau Lamo, gastronomi mencakup berbagai sajian khas yang lahir dari warisan turun-temurun,
                    memanfaatkan bahan-bahan lokal dan rempah khas daerah.
                    Berbagai hidangan ini tidak hanya memanjakan lidah tetapi juga mengungkapkan nilai-nilai kearifan dan jati diri daerah Danau Lamo.
                </p>
                <div class="search-container-gastronomi">
                    <input type="text" placeholder="Cari kuliner atau festival..." class="search-box-gastronomi" id="searchInputGastronomi" onkeypress="handleSearchGastronomi(event)" />
                    <button type="button" class="search-btn-gastronomi" onclick="performSearchGastronomiFromButton()">&#128269;</button>
                </div>
            </div>
        </section>

        <section id="kuliner-khas" class="bg-putih section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Kuliner Khas Danau Lamo</h3>
                <p class="ringkasan animate-slide-up delay-05s">Hidangan khas dari daerah Danau Lamo yang kaya cita rasa dan nilai budaya</p>
                <div class="grid-gastronomi grid-interactive">

                    <div class="item-gastro" data-keywords="pindang toman sup ikan gurih asam">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/pindang-toman.png" alt="Pindang Toman" />
                            <h4>Pindang Toman</h4>
                            <p>Hidangan sup ikan toman dengan kuah asam pedas yang kaya rempah. Masakan ini memiliki cita rasa gurih dan segar yang khas, menjadikannya sajian istimewa dalam berbagai acara adat maupun santapan keluarga sehari-hari di Danau Lamo.</p>
                        </div>
                    </div>

                    <div class="item-gastro" data-keywords="nasi cegau ubi gurih lezat santapan">
                        <div class="item-gastro-inner animate-pop-up delay-02s">
                            <img src="asset/nasi-cegau.png" alt="Nasi Cegau Ubi" />
                            <h4>Nasi Cegau Ubi</h4>
                            <p>Nasi cegau ubi adalah perpaduan nasi dan potongan ubi kayu yang dimasak bersamaan, menghasilkan rasa gurih dan manis alami. Makanan ini menjadi simbol kesederhanaan dan kekayaan alam Danau Lamo, biasa disantap dalam keseharian maupun saat hajatan.</p>
                        </div>
                    </div>

                    <div class="item-gastro" data-keywords="jelatah timun segar pedas asam">
                        <div class="item-gastro-inner animate-pop-up delay-04s">
                            <img src="asset/jelatah-timun.png" alt="Jelatah Timun" />
                            <h4>Jelatah Timun</h4>
                            <p>Jelatah timun adalah sajian segar yang terdiri dari irisan mentimun, cabai, bawang merah, dan perasan jeruk atau cuka. Rasa asam dan pedasnya menyegarkan, sangat cocok sebagai pelengkap makanan berkuah atau berminyak di Danau Lamo.</p>
                        </div>
                    </div>

                    <div class="item-gastro" data-keywords="ikan lambak goreng gurih renyah">
                        <div class="item-gastro-inner animate-pop-up delay-06s">
                            <img src="asset/ikan-lambak.png" alt="Ikan Lambak Goreng" />
                            <h4>Ikan Lambak Goreng</h4>
                            <p>Ikan lambak, salah satu jenis ikan air tawar dari perairan sekitar Danau Lamo, digoreng hingga garing dengan bumbu sederhana. Rasanya yang gurih dan teksturnya yang renyah menjadikan lauk ini favorit masyarakat, apalagi bila disantap dengan sambal nanas.</p>
                        </div>
                    </div>

                    <div class="item-gastro" data-keywords="sambal nanas pedas manis asam">
                        <div class="item-gastro-inner animate-pop-up delay-08s">
                            <img src="asset/sambal-nanas.png" alt="Sambal Nanas" />
                            <h4>Sambal Nanas</h4>
                            <p>Sambal unik berbahan dasar buah nanas yang diulek bersama cabai, bawang, dan sedikit garam. Rasa manis, pedas, dan asamnya sangat khas dan menyegarkan. Biasanya disajikan bersama ikan goreng atau nasi cegau sebagai pelengkap yang menggugah selera.</p>
                        </div>
                    </div>

                    <div class="item-gastro" data-keywords="burlam singkong kelapa kukus sedekah">
                        <div class="item-gastro-inner animate-pop-up delay-1s">
                            <img src="asset/burlam.png" alt="Burlam" />
                            <h4>Burlam</h4>
                            <p>Burlam adalah makanan ringan tradisional yang terbuat dari singkong parut yang dicampur kelapa dan gula, lalu dikukus. Makanan ini memiliki rasa manis alami dan tekstur lembut. Burlam biasa disajikan dalam acara sedekah hamil 7 bulanan sebagai simbol doa dan syukur atas kehamilan yang sehat di Danau Lamo.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section id="festival-kuliner" class="bg-krem section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Festival Kuliner Danau Lamo</h3>
                <p class="ringkasan animate-slide-up delay-05s">
                    Ajang tahunan yang merayakan kekayaan kuliner lokal sekaligus menjadi ruang ekspresi budaya dan kebersamaan masyarakat.
                </p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="festival kenduren durian tradisi kuliner budaya umum">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/festival-kenduren.png" alt="Festival Kenduren" class="poster-img" />
                            <h4>Festival Kenduren</h4>
                            <p>
                                Festival Kenduren merupakan perayaan tahunan yang diselenggarakan oleh masyarakat Danau Lamo saat musim panen durian tiba. Acara ini terbuka untuk umum dan biasanya diadakan di sekitar kawasan Candi Koto Mahligai, menjadikannya perpaduan antara wisata kuliner dan pelestarian situs budaya.
                                <br><br>
                                Pengunjung disuguhi beragam olahan durian khas seperti tempoyak, gulo duren, durian bakar, lemang duren, ketan duren, dan bahkan kopi duren. Tidak hanya menikmati makanan, para tamu juga dapat menyaksikan pertunjukan seni tradisional, mengikuti bazar UMKM, dan merasakan suasana gotong royong khas desa. Festival ini menjadi wujud syukur atas hasil alam serta sarana mempererat tali persaudaraan dan promosi budaya lokal.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


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