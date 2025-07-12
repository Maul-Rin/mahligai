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
    <title>Candi Koto Mahligai | Mahligai Heritage</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS for Read More functionality - Localized to this page */
        p.short-desc {
            display: -webkit-box;
            -webkit-line-clamp: 3; /* Limit to 3 lines initially */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        p.full-desc {
            display: none; /* Hidden by default */
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
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">Mahligai Heritage</a>

            <div class="nav-links">
                <a href="index.php">Beranda</a>
                <a href="candi-koto-mahligai.php" class="active-candi">Candi</a>
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
                <h3 class="animate-fade-in">Candi Koto Mahligai</h3>
                <p class="ringkasan animate-fade-in delay-1s">Situs candi penting dengan nilai sejarah dan spiritual yang tinggi di Muaro Jambi.</p>
                <img src="asset/koto.jpg" alt="Candi Koto Mahligai" class="gambar-deskripsi-penuh" />
                <p class="short-desc" style="margin-top: 20px;">
                    Candi Koto Mahligai merupakan salah satu peninggalan penting dari masa kejayaan Kerajaan Melayu Kuno di kawasan Muaro Jambi. Terletak di Desa Danau Lamo, candi ini dipercaya menjadi pusat pendidikan dan spiritual agama Buddha pada abad ke-7 hingga ke-9 Masehi. Posisi strategisnya dekat dengan Sungai Batanghari memperkuat dugaan bahwa candi ini menjadi bagian dari jalur perdagangan dan jaringan keagamaan internasional.
                </p>
                <p class="full-desc">
                    Candi Koto Mahligai merupakan salah satu peninggalan penting dari masa kejayaan Kerajaan Melayu Kuno di kawasan Muaro Jambi. Terletak di Desa Danau Lamo, candi ini dipercaya menjadi pusat pendidikan dan spiritual agama Buddha pada abad ke-7 hingga ke-9 Masehi. Posisi strategisnya dekat dengan Sungai Batanghari memperkuat dugaan bahwa candi ini menjadi bagian dari jalur perdagangan dan jaringan keagamaan internasional.
                    <br><br>
                    Struktur bangunan utama terdiri dari bata merah dengan teknik penyusunan tanpa semen. Area candi sempat tertutup semak belukar, namun kini telah dibersihkan dan ditata sebagai situs yang ramah pengunjung. Lingkungan sekitarnya yang rimbun membuat suasana semakin tenang dan reflektif, menjadikannya tempat yang ideal untuk meditasi dan belajar sejarah.
                </p>
                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
            </div>
        </section>

        <section class="bg-krem section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Arkeologi & Temuan</h3>
                <p class="ringkasan animate-slide-up delay-05s">Beragam elemen penting yang ditemukan di sekitar Candi Mahligai</p>
                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="struktur utama candi meditasi biksu bata merah">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/struktur.jpeg" alt="Struktur utama candi" />
                            <h4>Struktur Utama</h4>
                            <p class="short-desc">
                                Struktur utama candi berbentuk persegi dengan pondasi tinggi dari bata merah. Bangunan ini kemungkinan digunakan sebagai tempat meditasi para biksu.
                            </p>
                            <p class="full-desc">
                                Struktur utama candi berbentuk persegi dengan pondasi tinggi dari bata merah. Bangunan ini kemungkinan digunakan sebagai tempat meditasi para biksu, menunjukkan kompleksitas arsitektur dan fungsi spiritualnya di masa lampau.
                            </p>
                            <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="reruntuhan bata bangunan pendukung balai ruang pembelajaran">
                        <div class="item-gastro-inner animate-pop-up delay-02s">
                            <img src="asset/reruntuhan.png" alt="Reruntuhan bata candi" />
                            <h4>Reruntuhan Pelengkap</h4>
                            <p class="short-desc">
                                Beberapa struktur runtuh di sekitar candi menunjukkan bahwa dulunya kompleks ini memiliki bangunan pendukung seperti balai atau ruang pembelajaran.
                            </p>
                            <p class="full-desc">
                                Beberapa struktur runtuh di sekitar candi menunjukkan bahwa dulunya kompleks ini memiliki bangunan pendukung seperti balai atau ruang pembelajaran, menegaskan peran Candi Koto Mahligai sebagai pusat keagamaan dan pendidikan yang lengkap.
                            </p>
                            <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="arca batu fragmen buddha mahayana ibadah">
                        <div class="item-gastro-inner animate-pop-up delay-04s">
                            <img src="asset/arca-koto.png" alt="Fragmen arca Buddha" />
                            <h4>Arca Batu</h4>
                            <p class="short-desc">
                                Fragmen arca ditemukan di sekitar kompleks, memperkuat indikasi bahwa candi ini merupakan pusat ibadah Buddha Mahayana.
                            </p>
                            <p class="full-desc">
                                Fragmen arca ditemukan di sekitar kompleks, memperkuat indikasi bahwa candi ini merupakan pusat ibadah Buddha Mahayana, serta menjadi bukti keberadaan komunitas Buddha yang berkembang pesat di wilayah ini.
                            </p>
                            <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="lingkungan asri ketenangan spiritual edukatif pohon rindang">
                        <div class="item-gastro-inner animate-pop-up delay-06s">
                            <img src="asset/lingkungan.png" alt="Lingkungan candi" />
                            <h4>Lingkungan Asri</h4>
                            <p class="short-desc">
                                Lokasi candi dikelilingi pohon rindang yang menciptakan ketenangan, cocok untuk kegiatan spiritual dan edukatif.
                            </p>
                            <p class="full-desc">
                                Lokasi candi dikelilingi pohon rindang yang menciptakan ketenangan, cocok untuk kegiatan spiritual dan edukatif. Keindahan alam di sekitarnya menambah nilai historis dan menenangkan bagi setiap pengunjung yang datang.
                            </p>
                            <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="bg-putih section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Nilai Historis & Kunjungan</h3>
                <ul class="fakta">
                    <li>Ukuran tapak bangunan utama sekitar 18 x 18 meter.</li>
                    <li>Dibangun dengan teknik bata kering tanpa perekat semen.</li>
                    <li>Termasuk dalam kawasan Cagar Budaya Nasional.</li>
                    <li>Sudah tersedia fasilitas umum seperti toilet, jalur setapak, dan tempat sampah.</li>
                </ul>
                <p class="short-desc" style="margin-top: 20px;">
                    Waktu terbaik untuk berkunjung adalah pagi atau sore hari. Disarankan menggunakan alas kaki yang nyaman dan membawa perlindungan cuaca. Wisata ke Candi Mahligai memberikan pengalaman mendalam bagi pecinta sejarah dan budaya.
                </p>
                 <p class="full-desc">
                    Waktu terbaik untuk berkunjung adalah pagi atau sore hari untuk menghindari terik matahari dan merasakan suasana yang lebih syahdu. Disarankan menggunakan alas kaki yang nyaman dan membawa perlindungan cuaca seperti topi atau payung. Wisata ke Candi Mahligai memberikan pengalaman mendalam bagi pecinta sejarah dan budaya, serta kesempatan untuk merenung di tengah ketenangan alam.
                </p>
                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
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
            const parentElement = button.parentElement; // Get the direct parent (e.g., .item-gastro-inner or .layar-dalam)
            const shortDesc = parentElement.querySelector('.short-desc');
            const fullDesc = parentElement.querySelector('.full-desc');

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

        // Search functions (copied from kategori-gastronomi.php, adapted for this page's items)
        function handleSearchCandi(event) {
            if (event.key === 'Enter') {
                performSearchCandi();
            }
        }

        function performSearchCandiFromButton() {
            performSearchCandi();
        }

        function performSearchCandi() {
            const searchValue = document.getElementById('searchInputCandi').value.toLowerCase();
            // Select all relevant items on this page
            const candiItems = document.querySelectorAll('.item-gastro'); // Adjust selector if needed

            candiItems.forEach(item => {
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