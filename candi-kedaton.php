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
    <title>Candi Kedaton | Mahligai Heritage</title>
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
                <a href="candi-kedaton.php" class="active-candi">Candi</a>
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
                <h3 class="animate-fade-in">Candi Kedaton</h3>
                <p class="ringkasan animate-fade-in delay-1s">Menelusuri kejayaan arsitektur dan spiritualitas kuno di situs Candi Kedaton, Muaro Jambi.</p>
                <img src="asset/candi-kedaton.jpeg" alt="Candi Kedaton" class="gambar-deskripsi-penuh" />
                <p class="short-desc" style="margin-top: 20px;">
                    Candi Kedaton adalah salah satu situs utama dalam kompleks percandian Muaro Jambi yang luas, terletak di Desa Muaro Jambi, Kecamatan Marosebo. Situs ini menyimpan berbagai misteri sejarah dari masa lampau, serta menjadi saksi bisu kejayaan peradaban Buddha-Hindu di Nusantara. Terletak sekitar 1500 meter di sebelah barat Candi Gedong II, Candi Kedaton menampilkan struktur arsitektur yang luar biasa lengkap, dengan dinding keliling berukuran 200 x 230 meter, dan lebih dari sepuluh struktur reruntuhan candi di dalamnya.
                </p>
                <p class="full-desc">
                    Candi Kedaton adalah salah satu situs utama dalam kompleks percandian Muaro Jambi yang luas, terletak di Desa Muaro Jambi, Kecamatan Marosebo. Situs ini menyimpan berbagai misteri sejarah dari masa lampau, serta menjadi saksi bisu kejayaan peradaban Buddha-Hindu di Nusantara. Terletak sekitar 1500 meter di sebelah barat Candi Gedong II, Candi Kedaton menampilkan struktur arsitektur yang luar biasa lengkap, dengan dinding keliling berukuran 200 x 230 meter, dan lebih dari sepuluh struktur reruntuhan candi di dalamnya.
                    <br><br>
                    Yang membedakan Candi Kedaton dari kompleks lainnya adalah keberadaan makara bertuliskan aksara Jawa Kuno yang menyebutkan nama "Mpu Kusuma", yang ditafsirkan sebagai tokoh spiritual yang menggunakan kawasan ini sebagai tempat meditasi. Temuan arkeologis lainnya seperti sumur tua, belanga besar dari perunggu, dan berbagai ornamen batu bata menunjukkan adanya aktivitas biara Buddha (vihāra) yang signifikan di masa lalu.
                    <br><br>
                    Meskipun belum sepopuler Borobudur, keunikan dan keluasan kawasan Candi Kedaton menjadikannya destinasi penting bagi pecinta sejarah, budaya, dan arkeologi. Kunjungan ke sini bukan hanya menyaksikan struktur fisik, tetapi juga merenungi warisan spiritual dan intelektual masa silam.
                </p>
                <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
            </div>
        </section>

        <section id="sejarah-candi" class="bg-krem section-highlight">
            <div class="layar-dalam">
                <h3 class="animate-slide-up">Arsitektur & Temuan</h3>
                <p class="ringkasan animate-slide-up delay-05s">Menampilkan lima aspek penting dari Candi Kedaton: arsitektur, gapura, makara, sumur tua, dan belanga perunggu.</p>

                <div class="grid-gastronomi grid-interactive">
                    <div class="item-gastro" data-keywords="arsitektur candi kedaton terbesar pondasi tinggi bata kerakal">
                        <div class="item-gastro-inner animate-pop-up">
                            <img src="asset/arsitektur-kedaton.png" alt="Arsitektur Candi Kedaton" />
                            <h4>Arsitektur Candi</h4>
                            <p class="short-desc">
                                Bangunan induk Candi Kedaton adalah yang terbesar di kompleks Muaro Jambi. Kaki bangunan setinggi 7,2 meter dipenuhi batu kerakal untuk kestabilan struktur, menunjukkan kecerdasan teknik pada masa itu.
                            </p>
                            <p class="full-desc">
                                Bangunan induk Candi Kedaton adalah yang terbesar di kompleks Muaro Jambi. Kaki bangunan setinggi 7,2 meter dipenuhi batu kerakal untuk kestabilan struktur, menunjukkan kecerdasan teknik pada masa itu, serta kehebatan arsitek masa lalu dalam membangun struktur monumental.
                            </p>
                            <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="gapura pintu masuk makara monyet tanduk simbolisme">
                        <div class="item-gastro-inner animate-pop-up delay-02s">
                            <img src="asset/pintu-masuk.png" alt="Gapura Candi Kedaton" />
                            <h4>Gapura Pintu Masuk</h4>
                            <p class="short-desc">
                                Gapura utama di sisi utara dikelilingi empat makara. Ambang pintu tidak sejajar dengan candi utama, menunjukkan simbolisme arsitektural khas. Salah satu makara memiliki pahatan kepala binatang mirip monyet bertanduk.
                            </p>
                            <p class="full-desc">
                                Gapura utama di sisi utara dikelilingi empat makara. Ambang pintu tidak sejajar dengan candi utama, menunjukkan simbolisme arsitektural khas. Salah satu makara memiliki pahatan kepala binatang mirip monyet bertanduk, menambah keunikan dan misteri pada situs ini.
                            </p>
                            <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="makara aksara jawa kuno mpu kusuma meditasi">
                        <div class="item-gastro-inner animate-pop-up delay-04s">
                            <img src="asset/arca.png" alt="Makara dengan Aksara Jawa" />
                            <h4>Makara Bertuliskan Aksara Jawa Kuno</h4>
                            <p class="short-desc">
                                Dua makara di dalam gapura bertuliskan aksara Jawa Kuno. Salah satunya berbunyi "Pamursitanira Mpu Kusuma", diartikan sebagai tempat mengheningkan cipta (meditasi) Mpu Kusuma.
                            </p>
                            <p class="full-desc">
                                Dua makara di dalam gapura bertuliskan aksara Jawa Kuno. Salah satunya berbunyi "Pamursitanira Mpu Kusuma", diartikan sebagai tempat mengheningkan cipta (meditasi) Mpu Kusuma. Keberadaan makara ini memberikan petunjuk penting tentang fungsi dan tokoh spiritual yang terkait dengan candi.
                            </p>
                            <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="sumur tua batu bata suci masyarakat tradisional">
                        <div class="item-gastro-inner animate-pop-up delay-06s">
                            <img src="asset/sumur.jpg" alt="Sumur Tua Candi Kedaton" />
                            <h4>Sumur Tua</h4>
                            <p class="short-desc">
                                Sebuah sumur dari susunan batu bata ditemukan di timur Candi Kedaton. Airnya dianggap suci dan masih digunakan secara tradisional oleh masyarakat sekitar.
                            </p>
                            <p class="full-desc">
                                Sebuah sumur dari susunan batu bata ditemukan di timur Candi Kedaton. Airnya dianggap suci dan masih digunakan secara tradisional oleh masyarakat sekitar untuk ritual dan keperluan sehari-hari, menunjukkan kesinambungan tradisi.
                            </p>
                            <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
                        </div>
                    </div>
                    <div class="item-gastro" data-keywords="belanga perunggu memasak vihara komunal keagamaan">
                        <div class="item-gastro-inner animate-pop-up delay-08s">
                            <img src="asset/belanga.png" alt="Belanga Besar" />
                            <h4>Belanga Perunggu</h4>
                            <p class="short-desc">
                                Ditemukan tahun 1994, belanga perunggu ini beratnya 160 kg dan tinggi 67 cm. Berfungsi sebagai alat memasak di vihāra, menandakan kehidupan komunal keagamaan.
                            </p>
                            <p class="full-desc">
                                Ditemukan tahun 1994, belanga perunggu ini beratnya 160 kg dan tinggi 67 cm. Berfungsi sebagai alat memasak di vihāra, menandakan kehidupan komunal keagamaan dan aktivitas sehari-hari para penghuni biara di masa lampau.
                            </p>
                            <button class="read-more-btn" onclick="toggleReadMore(this)">Selengkapnya</button>
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