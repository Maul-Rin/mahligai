// =========================
// FUNGSI CUSTOM ALERT (Ditingkatkan untuk mencegah duplikasi)
// =========================
let isAlertShowing = false // Flag untuk melacak status alert

function showCustomAlert(message) {
  if (isAlertShowing) {
    console.warn("Alert sudah tampil, mencegah duplikasi.")
    return // Jangan tampilkan alert baru jika sudah ada
  }

  isAlertShowing = true // Set flag menjadi true

  const alertBox = document.createElement("div")
  alertBox.classList.add("custom-alert")
  alertBox.innerHTML = `
        <div class="alert-content">
            <p>${message}</p>
            <button class="alert-close-btn">OK</button>
        </div>
    `
  document.body.appendChild(alertBox)

  // Memicu reflow agar transisi CSS berjalan
  alertBox.offsetHeight
  alertBox.classList.add("show")

  const closeBtn = alertBox.querySelector(".alert-close-btn")
  const closeAlert = () => {
    alertBox.classList.remove("show")
    alertBox.addEventListener(
      "transitionend",
      () => {
        alertBox.remove()
        isAlertShowing = false // Reset flag setelah alert hilang
      },
      { once: true },
    )
  }

  closeBtn.addEventListener("click", closeAlert)

  alertBox.addEventListener("click", (e) => {
    if (e.target === alertBox) {
      closeAlert()
    }
  })

  // Menambahkan event listener untuk tombol Enter pada dokumen
  const handleDocumentKeydown = (e) => {
    if (e.key === "Enter" && isAlertShowing) {
      e.preventDefault() // Mencegah event default (misal submit form)
      closeAlert()
      document.removeEventListener("keydown", handleDocumentKeydown) // Hapus listener setelah digunakan
    }
  }
  document.addEventListener("keydown", handleDocumentKeydown)

  console.log("Custom Alert Shown:", message) // Debugging
}

// =========================
// FUNGSI KLIK MENU HAMBURGER & INISIALISASI SEARCH BARS
// =========================
let menuOpen = false

function klikMenu() {
  const tombolMenu = document.querySelector(".tombol-menu")
  // Menentukan menu yang aktif berdasarkan keberadaan kelas 'navbar'
  const currentMenu = document.querySelector("nav").classList.contains("navbar")
    ? document.querySelector(".navbar .nav-links")
    : document.querySelector("nav .menu")

  if (!tombolMenu) {
    console.log("Tombol menu tidak ditemukan.")
    return
  }
  if (!currentMenu) {
    console.log("Elemen menu navigasi tidak ditemukan untuk halaman ini.")
    return
  }

  // Hanya tambahkan event listener sekali
  if (!tombolMenu._hasClickListener) {
    tombolMenu.addEventListener("click", (e) => {
      e.preventDefault()
      e.stopPropagation()
      menuOpen = !menuOpen
      if (menuOpen) {
        currentMenu.classList.add("menu-active")
        tombolMenu.classList.add("active")
      } else {
        currentMenu.classList.remove("menu-active")
        tombolMenu.classList.remove("active")
      }
    })
    tombolMenu._hasClickListener = true
  }

  if (!currentMenu._hasClickListener) {
    currentMenu.addEventListener("click", (e) => {
      e.stopPropagation()
    })
    currentMenu._hasClickListener = true
  }

  // Definisi semua search bars dan handler-nya
  const searchBars = [
    { id: "searchInputHeader", container: ".search-container-header", handler: handleSearchHeader },
    { id: "searchInputGastronomi", container: ".search-container-gastronomi", handler: handleSearchGastronomi },
    { id: "searchInputKanal", container: ".search-container-kanal", handler: handleSearchKanal },
    { id: "searchInputCandi", container: ".search-container-candi", handler: handleSearchCandi },
    { id: "searchInputKearifan", container: ".search-container-kearifan", handler: handleSearchKearifan },
    { id: "searchInputTiket", container: ".search-container-tiket", handler: handleSearchTiket },
  ]

  // Iterasi untuk menginisialisasi event listeners pada setiap search bar
  searchBars.forEach((bar) => {
    const searchBox = document.getElementById(bar.id)
    const searchContainer = document.querySelector(bar.container)

    if (searchContainer) {
      // Pastikan click listener pada container hanya ditambahkan sekali
      if (!searchContainer._hasClickListener) {
        searchContainer.addEventListener("click", (e) => {
          e.stopPropagation()
        })
        searchContainer._hasClickListener = true
      }
    }
    if (searchBox) {
      // Pastikan event listeners pada search box hanya ditambahkan sekali
      if (!searchBox._hasClickListener) {
        // Hapus listener keypress lama jika ada untuk mencegah duplikasi perilaku
        searchBox.removeEventListener("keypress", bar.handler)
        searchBox.addEventListener("click", (e) => {
          e.stopPropagation()
        })
        searchBox.addEventListener("focus", (e) => {
          e.stopPropagation()
        })
        searchBox.addEventListener("input", (e) => {
          e.stopPropagation()
        })
        searchBox.addEventListener("keypress", bar.handler) // Tambahkan kembali handler keypress
        searchBox._hasClickListener = true
      }
    }
  })
}

// =========================
// FUNGSI UNTUK MENUTUP MENU MOBILE
// =========================
function closeMobileMenu() {
  const tombolMenu = document.querySelector(".tombol-menu")
  const currentMenu = document.querySelector("nav").classList.contains("navbar")
    ? document.querySelector(".navbar .nav-links")
    : document.querySelector("nav .menu")
  if (menuOpen && currentMenu && tombolMenu) {
    currentMenu.classList.remove("menu-active")
    tombolMenu.classList.remove("active")
    menuOpen = false
  }
}

// =========================
// HANDLE KLIK DI LUAR MENU/SEARCH
// =========================
function handleOutsideClick(event) {
  const tombolMenu = document.querySelector(".tombol-menu")
  const currentMenu = document.querySelector("nav").classList.contains("navbar")
    ? document.querySelector(".navbar .nav-links")
    : document.querySelector("nav .menu")
  const searchContainers = [
    document.querySelector(".search-container-header"),
    document.querySelector(".search-container-gastronomi"),
    document.querySelector(".search-container-kanal"),
    document.querySelector(".search-container-candi"),
    document.querySelector(".search-container-kearifan"),
    document.querySelector(".search-container-tiket"),
  ]

  if (
    menuOpen &&
    currentMenu &&
    tombolMenu &&
    !currentMenu.contains(event.target) &&
    !tombolMenu.contains(event.target)
  ) {
    closeMobileMenu()
  }

  // Tidak ada aksi spesifik yang diperlukan untuk menutup search bar saat klik di luar
  // karena search bar hanya berupa input field.
  searchContainers.forEach((container) => {
    if (container && !container.contains(event.target)) {
      // Misalnya, jika ada dropdown atau saran pencarian yang perlu ditutup,
      // logika penutupannya bisa ditambahkan di sini.
    }
  })
}

// =========================
// PENGATURAN TAMPILAN MENU (Responsive)
// =========================
function setMenuDisplay() {
  const navElement = document.querySelector("nav")
  const isOldNavbar = navElement && !navElement.classList.contains("navbar")
  const menuToAdjust = isOldNavbar ? document.querySelector("nav .menu") : document.querySelector(".navbar .nav-links")
  const hamburger = document.querySelector(".tombol-menu")

  if (!menuToAdjust || !hamburger) return

  if (window.innerWidth < 992) {
    // Untuk mobile, pastikan menu tertutup dan reset state
    menuToAdjust.classList.remove("menu-active")
    hamburger.classList.remove("active")
    menuOpen = false
  } else {
    // Untuk desktop, pastikan menu terbuka dan hapus gaya inline yang diterapkan oleh JS mobile
    menuToAdjust.classList.remove("menu-active") // Hapus kelas mobile aktif
    hamburger.classList.remove("active") // Hapus kelas hamburger aktif
    menuOpen = false // Reset state menu

    // Hapus semua gaya inline yang mungkin diterapkan oleh JS untuk mobile view
    menuToAdjust.style.transform = ""
    menuToAdjust.style.position = ""
    menuToAdjust.style.background = ""
    menuToAdjust.style.backdropFilter = ""
    menuToAdjust.style.width = ""
    menuToAdjust.style.height = ""
    menuToAdjust.style.justifyContent = ""
    menuToAdjust.style.alignItems = ""
    menuToAdjust.style.padding = ""
    menuToAdjust.style.flexDirection = ""
    menuToAdjust.style.marginTop = ""
    menuToAdjust.style.overflowY = ""
    menuToAdjust.style.marginLeft = ""

    // Pastikan menu ditampilkan sebagai flexbox di desktop untuk navbar baru
    if (!isOldNavbar) {
      menuToAdjust.style.display = "flex"
    }
  }
}

// =========================
// SMOOTH SCROLLING UNTUK SEMUA ANCHOR LINKS
// =========================
function initSmoothScrolling() {
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    // Pastikan link adalah anchor internal ke halaman yang sama
    if (anchor.getAttribute("href").startsWith("#") && anchor.pathname === window.location.pathname) {
      anchor.addEventListener("click", function (e) {
        e.preventDefault()
        const target = document.querySelector(this.getAttribute("href"))
        if (target) {
          target.scrollIntoView({
            behavior: "smooth",
            block: "start",
          })
          closeMobileMenu() // Tutup menu mobile setelah klik
        }
      })
    }
  })
}

// =========================
// FUNGSI SEARCH (Untuk Index.php)
// =========================
function performSearchFromButtonHeader() {
  const searchInput = document.getElementById("searchInputHeader")
  const searchTerm = searchInput.value.toLowerCase().trim()
  if (searchTerm === "") {
    showCustomAlert("Masukkan kata kunci pencarian!")
    return
  }
  performSearchOnIndex(searchTerm)
}

function handleSearchHeader(e) {
  if (e.key === "Enter") {
    e.preventDefault()
    const searchTerm = e.target.value.toLowerCase().trim()
    if (searchTerm === "") {
      showCustomAlert("Masukkan kata kunci pencarian!")
      return
    }
    performSearchOnIndex(searchTerm)
  }
}

function performSearchOnIndex(searchTerm) {
  const searchableContent = [
    {
      keyword: ["gastronomi", "kuliner", "makanan", "gulai", "tempoyak", "gandus", "festival"],
      section: "gastronomi",
      title: "Gastronomi",
    },
    {
      keyword: ["kanal", "kuno", "susur", "sejarah", "transportasi", "air"],
      section: "kanal",
      title: "Kanal Kuno",
    },
    {
      keyword: ["candi", "kedaton", "mahligai", "koto", "sejarah", "peninggalan", "arkeologi"],
      section: "candi",
      title: "Candi",
    },
    {
      keyword: ["kearifan", "lokal", "tradisi", "musik", "gambangan", "anyaman", "tkud"],
      section: "kearifan",
      title: "Kearifan Lokal",
    },
    {
      keyword: ["sorotan", "tokoh", "gubernur", "najwa", "figur"],
      section: "sorotan",
      title: "Sorotan Tokoh",
    },
  ]

  let foundContent = null
  for (const content of searchableContent) {
    if (content.keyword.some((keyword) => searchTerm.includes(keyword) || keyword.includes(searchTerm))) {
      foundContent = content
      break
    }
  }

  if (foundContent) {
    if (window.location.pathname.endsWith("index.php")) {
      closeMobileMenu()
      setTimeout(() => {
        const targetSection = document.getElementById(foundContent.section)
        if (targetSection) {
          targetSection.scrollIntoView({
            behavior: "smooth",
            block: "start",
          })
          // Highlight section utama di index.php
          targetSection.style.backgroundColor = "rgba(244, 208, 63, 0.1)"
          setTimeout(() => {
            targetSection.style.backgroundColor = ""
          }, 3000)
          const currentSearchInput = document.getElementById("searchInputHeader")
          if (currentSearchInput) currentSearchInput.value = ""
        }
      }, 300)
    } else {
      // Jika pencarian dilakukan di halaman kategori lain, redirect ke index.php dengan hash
      window.location.href = `index.php#${foundContent.section}`
      localStorage.setItem("searchTermForIndex", searchTerm) // Simpan searchTerm untuk diproses setelah redirect
    }
  } else {
    const suggestions = [
      "Coba kata kunci: gastronomi, kuliner, makanan",
      "Coba kata kunci: kanal, susur, sejarah",
      "Coba kata kunci: candi, kedaton, mahligai",
      "Coba kata kunci: kearifan, tradisi",
      "Coba kata kunci: sorotan, tokoh",
    ]
    showCustomAlert(
      `Tidak ditemukan hasil untuk "${searchTerm}"<br><br>Saran pencarian:<br>${suggestions.join("<br>")}`,
    )
  }
}

// =========================
// FUNGSI SEARCH (Untuk Kategori Gastronomi)
// =========================
function performSearchGastronomiFromButton() {
  const searchInput = document.getElementById("searchInputGastronomi")
  const searchTerm = searchInput.value.toLowerCase().trim()
  if (searchTerm === "") {
    showCustomAlert("Masukkan kata kunci pencarian!")
    return
  }
  filterGastronomiItems(searchTerm)
}

function handleSearchGastronomi(e) {
  if (e.key === "Enter") {
    e.preventDefault()
    const searchTerm = e.target.value.toLowerCase().trim()
    if (searchTerm === "") {
      showCustomAlert("Masukkan kata kunci pencarian!")
      return
    }
    filterGastronomiItems(searchTerm)
  }
}

function filterGastronomiItems(searchTerm) {
  console.log("Mulai filterGastronomiItems untuk:", searchTerm)
  const items = document.querySelectorAll(".grid-interactive .item-gastro") // Gunakan .grid-interactive untuk spesifikasi

  // 1. Hapus highlight dari semua item yang ada di halaman ini
  items.forEach((item) => {
    item.classList.remove("highlight-gastronomi")
    item.style.display = "block" // Pastikan semua item selalu terlihat (tidak disembunyikan)
  })

  const matchingItems = []

  // 2. Iterasi untuk mencari item yang cocok
  items.forEach((item) => {
    const keywords = item.getAttribute("data-keywords") || ""
    const title = item.querySelector("h4") ? item.querySelector("h4").textContent.toLowerCase() : ""
    const description = item.querySelector("p") ? item.querySelector("p").textContent.toLowerCase() : ""

    if (keywords.includes(searchTerm) || title.includes(searchTerm) || description.includes(searchTerm)) {
      matchingItems.push(item)
    }
    // Penting: Tidak ada 'else { item.style.display = 'none'; }' di sini
    // agar item yang tidak cocok tetap terlihat.
  })

  console.log("Jumlah item yang cocok ditemukan:", matchingItems.length)

  // 3. Tentukan apakah ada item yang cocok dan lakukan aksi
  if (matchingItems.length > 0) {
    const firstFoundItem = matchingItems[0]

    // Gulir ke item pertama yang ditemukan
    if (firstFoundItem) {
      firstFoundItem.scrollIntoView({
        behavior: "smooth",
        block: "center",
      })
    }

    // Tambahkan kelas highlight sementara untuk semua item yang cocok
    matchingItems.forEach((item) => {
      item.classList.add("highlight-gastronomi")
    })

    // Hapus highlight setelah beberapa detik
    setTimeout(() => {
      matchingItems.forEach((item) => {
        item.classList.remove("highlight-gastronomi")
      })
    }, 3000)
  } else {
    // Jika tidak ada item yang cocok sama sekali
    showCustomAlert(`Tidak ditemukan hasil untuk "${searchTerm}" di halaman ini. Coba kata kunci lain.`)
  }
}

// Fungsi untuk mereset highlight saat input kosong di halaman gastronomi
document.addEventListener("DOMContentLoaded", () => {
  const searchInputGastronomi = document.getElementById("searchInputGastronomi")
  if (searchInputGastronomi) {
    searchInputGastronomi.addEventListener("input", () => {
      if (searchInputGastronomi.value.trim() === "") {
        document.querySelectorAll(".grid-interactive .item-gastro").forEach((item) => {
          item.classList.remove("highlight-gastronomi")
        })
      }
    })
  }
})

// =========================
// FUNGSI SEARCH (Untuk Halaman Kanal Kuno)
// =========================
function performSearchKanalFromButton() {
  const searchInput = document.getElementById("searchInputKanal")
  const searchTerm = searchInput.value.toLowerCase().trim()
  if (searchTerm === "") {
    showCustomAlert("Masukkan kata kunci pencarian!")
    return
  }
  filterKanalItems(searchTerm)
}

function handleSearchKanal(e) {
  if (e.key === "Enter") {
    e.preventDefault()
    const searchTerm = e.target.value.toLowerCase().trim()
    if (searchTerm === "") {
      showCustomAlert("Masukkan kata kunci pencarian!")
      return
    }
    filterKanalItems(searchTerm)
  }
}

function filterKanalItems(searchTerm) {
  console.log("Mulai filterKanalItems untuk:", searchTerm)
  const items = document.querySelectorAll(".grid-interactive .item-gastro") // Menyesuaikan selektor

  // 1. Hapus highlight dari semua item
  items.forEach((item) => {
    item.classList.remove("highlight-kanal")
    item.style.display = "block" // Pastikan semua item selalu terlihat
  })

  const matchingItems = []

  // 2. Iterasi untuk mencari item yang cocok
  items.forEach((item) => {
    const keywords = item.getAttribute("data-keywords") || ""
    const title = item.querySelector("h4") ? item.querySelector("h4").textContent.toLowerCase() : ""
    const description = item.querySelector("p") ? item.querySelector("p").textContent.toLowerCase() : ""

    if (keywords.includes(searchTerm) || title.includes(searchTerm) || description.includes(searchTerm)) {
      matchingItems.push(item)
    }
    // Penting: Tidak ada 'else { item.style.display = 'none'; }' di sini
    // agar item yang tidak cocok tetap terlihat.
  })

  console.log("Jumlah item yang cocok ditemukan:", matchingItems.length)

  // 3. Tentukan apakah ada item yang cocok dan lakukan aksi
  if (matchingItems.length > 0) {
    const firstFoundItem = matchingItems[0]

    // Gulir ke item pertama yang ditemukan
    if (firstFoundItem) {
      firstFoundItem.scrollIntoView({
        behavior: "smooth",
        block: "center",
      })
    }

    // Tambahkan kelas highlight sementara untuk semua item yang cocok
    matchingItems.forEach((item) => {
      item.classList.add("highlight-kanal")
    })

    // Hapus highlight setelah beberapa detik
    setTimeout(() => {
      matchingItems.forEach((item) => {
        item.classList.remove("highlight-kanal")
      })
    }, 3000)
  } else {
    // Jika tidak ada item yang cocok sama sekali
    showCustomAlert(`Tidak ditemukan hasil untuk "${searchTerm}" di halaman ini. Coba kata kunci lain.`)
  }
}

// Fungsi untuk mereset highlight saat input kosong di halaman kanal
document.addEventListener("DOMContentLoaded", () => {
  const searchInputKanal = document.getElementById("searchInputKanal")
  if (searchInputKanal) {
    searchInputKanal.addEventListener("input", () => {
      if (searchInputKanal.value.trim() === "") {
        document.querySelectorAll(".grid-interactive .item-gastro").forEach((item) => {
          item.classList.remove("highlight-kanal")
        })
      }
    })
  }
})

// =========================
// FUNGSI SEARCH (Untuk Halaman Candi Kedaton)
// =========================
function performSearchCandiFromButton() {
  const searchInput = document.getElementById("searchInputCandi")
  const searchTerm = searchInput.value.toLowerCase().trim()
  if (searchTerm === "") {
    showCustomAlert("Masukkan kata kunci pencarian!")
    return
  }
  filterCandiItems(searchTerm)
}

function handleSearchCandi(e) {
  if (e.key === "Enter") {
    e.preventDefault()
    const searchTerm = e.target.value.toLowerCase().trim()
    if (searchTerm === "") {
      showCustomAlert("Masukkan kata kunci pencarian!")
      return
    }
    filterCandiItems(searchTerm)
  }
}

function filterCandiItems(searchTerm) {
  console.log("Mulai filterCandiItems untuk:", searchTerm)
  const items = document.querySelectorAll(".grid-interactive .item-gastro") // Menyesuaikan selektor

  // 1. Hapus highlight dari semua item
  items.forEach((item) => {
    item.classList.remove("highlight-candi")
    item.style.display = "block" // Pastikan semua item selalu terlihat
  })

  const matchingItems = []

  // 2. Iterasi untuk mencari item yang cocok
  items.forEach((item) => {
    const keywords = item.getAttribute("data-keywords") || ""
    const title = item.querySelector("h4") ? item.querySelector("h4").textContent.toLowerCase() : ""
    const description = item.querySelector("p") ? item.querySelector("p").textContent.toLowerCase() : ""

    if (keywords.includes(searchTerm) || title.includes(searchTerm) || description.includes(searchTerm)) {
      matchingItems.push(item)
    }
    // Penting: Tidak ada 'else { item.style.display = 'none'; }' di sini
    // agar item yang tidak cocok tetap terlihat.
  })

  console.log("Jumlah item yang cocok ditemukan:", matchingItems.length)

  // 3. Tentukan apakah ada item yang cocok dan lakukan aksi
  if (matchingItems.length > 0) {
    const firstFoundItem = matchingItems[0]

    // Gulir ke item pertama yang ditemukan
    if (firstFoundItem) {
      firstFoundItem.scrollIntoView({
        behavior: "smooth",
        block: "center",
      })
    }

    // Tambahkan kelas highlight sementara untuk semua item yang cocok
    matchingItems.forEach((item) => {
      item.classList.add("highlight-candi")
    })

    // Hapus highlight setelah beberapa detik
    setTimeout(() => {
      matchingItems.forEach((item) => {
        item.classList.remove("highlight-candi")
      })
    }, 3000)
  } else {
    // Jika tidak ada item yang cocok sama sekali
    showCustomAlert(`Tidak ditemukan hasil untuk "${searchTerm}" di halaman ini. Coba kata kunci lain.`)
  }
}

// Fungsi untuk mereset highlight saat input kosong di halaman candi
document.addEventListener("DOMContentLoaded", () => {
  const searchInputCandi = document.getElementById("searchInputCandi")
  if (searchInputCandi) {
    searchInputCandi.addEventListener("input", () => {
      if (searchInputCandi.value.trim() === "") {
        document.querySelectorAll(".grid-interactive .item-gastro").forEach((item) => {
          item.classList.remove("highlight-candi")
        })
      }
    })
  }
})

// =========================
// FUNGSI SEARCH (Untuk Halaman Kearifan Lokal)
// =========================
function performSearchKearifanFromButton() {
  const searchInput = document.getElementById("searchInputKearifan")
  const searchTerm = searchInput.value.toLowerCase().trim()
  if (searchTerm === "") {
    showCustomAlert("Masukkan kata kunci pencarian!")
    return
  }
  filterKearifanItems(searchTerm)
}

function handleSearchKearifan(e) {
  if (e.key === "Enter") {
    e.preventDefault()
    const searchTerm = e.target.value.toLowerCase().trim()
    if (searchTerm === "") {
      showCustomAlert("Masukkan kata kunci pencarian!")
      return
    }
    filterKearifanItems(searchTerm)
  }
}

function filterKearifanItems(searchTerm) {
  console.log("Mulai filterKearifanItems untuk:", searchTerm)
  // Selector ini akan mencari item-gastro di dalam section kearifan lokal
  const items = document.querySelectorAll(
    "#musik-tradisional .item-gastro, #anyaman-lokal .item-gastro, #tradisi-lokal .item-gastro",
  )

  // 1. Hapus highlight dari semua item
  items.forEach((item) => {
    item.classList.remove("highlight-kearifan")
    item.style.display = "block" // Pastikan semua item selalu terlihat
  })

  const matchingItems = []

  // 2. Iterasi untuk mencari item yang cocok
  items.forEach((item) => {
    const keywords = item.getAttribute("data-keywords") || ""
    const title = item.querySelector("h4") ? item.querySelector("h4").textContent.toLowerCase() : ""
    const description = item.querySelector("p") ? item.querySelector("p").textContent.toLowerCase() : ""

    if (keywords.includes(searchTerm) || title.includes(searchTerm) || description.includes(searchTerm)) {
      matchingItems.push(item)
    }
  })

  console.log("Jumlah item yang cocok ditemukan:", matchingItems.length)

  // 3. Tentukan apakah ada item yang cocok dan lakukan aksi
  if (matchingItems.length > 0) {
    const firstFoundItem = matchingItems[0]

    // Gulir ke item pertama yang ditemukan
    if (firstFoundItem) {
      firstFoundItem.scrollIntoView({
        behavior: "smooth",
        block: "center",
      })
    }

    // Tambahkan kelas highlight sementara untuk semua item yang cocok
    matchingItems.forEach((item) => {
      item.classList.add("highlight-kearifan")
    })

    // Hapus highlight setelah beberapa detik
    setTimeout(() => {
      matchingItems.forEach((item) => {
        item.classList.remove("highlight-kearifan")
      })
    }, 3000)
  } else {
    // Jika tidak ada item yang cocok sama sekali
    showCustomAlert(`Tidak ditemukan hasil untuk "${searchTerm}" di halaman ini. Coba kata kunci lain.`)
  }
}

// Fungsi untuk mereset highlight saat input kosong di halaman kearifan lokal
document.addEventListener("DOMContentLoaded", () => {
  const searchInputKearifan = document.getElementById("searchInputKearifan")
  if (searchInputKearifan) {
    // Hapus listener lama untuk mencegah duplikasi (opsional)
    searchInputKearifan.removeEventListener("keypress", handleSearchKearifan)
    searchInputKearifan.addEventListener("keypress", handleSearchKearifan)

    searchInputKearifan.addEventListener("input", () => {
      if (searchInputKearifan.value.trim() === "") {
        document
          .querySelectorAll("#musik-tradisional .item-gastro, #anyaman-lokal .item-gastro, #tradisi-lokal .item-gastro")
          .forEach((item) => {
            item.classList.remove("highlight-kearifan")
          })
      }
    })
  }
})

// =========================
// FUNGSI SEARCH (Untuk Halaman Tiket & UMKM)
// =========================
function performSearchTiketFromButton() {
  console.log("performSearchTiketFromButton dipanggil")
  const searchInput = document.getElementById("searchInputTiket")
  const searchTerm = searchInput.value.toLowerCase().trim()
  if (searchTerm === "") {
    showCustomAlert("Masukkan kata kunci pencarian!")
    return
  }
  filterTiketItems(searchTerm)
}

function handleSearchTiket(e) {
  console.log("handleSearchTiket dipanggil dengan key:", e.key)
  if (e.key === "Enter") {
    e.preventDefault()
    const searchTerm = e.target.value.toLowerCase().trim()
    if (searchTerm === "") {
      showCustomAlert("Masukkan kata kunci pencarian!")
      return
    }
    filterTiketItems(searchTerm)
  }
}

function filterTiketItems(searchTerm) {
  console.log("Mulai filterTiketItems untuk:", searchTerm)
  const items = document.querySelectorAll(".grid-interactive .item-gastro")

  // 1. Hapus highlight dari semua item
  items.forEach((item) => {
    item.classList.remove("highlight-tiket")
    item.style.display = "block" // Pastikan semua item selalu terlihat
  })

  const matchingItems = []

  // 2. Iterasi untuk mencari item yang cocok
  items.forEach((item) => {
    const keywords = item.getAttribute("data-keywords") || ""
    const title = item.querySelector("h4") ? item.querySelector("h4").textContent.toLowerCase() : ""
    const description = item.querySelector("p") ? item.querySelector("p").textContent.toLowerCase() : ""

    if (keywords.includes(searchTerm) || title.includes(searchTerm) || description.includes(searchTerm)) {
      matchingItems.push(item)
    }
  })

  console.log("Jumlah item yang cocok ditemukan:", matchingItems.length)

  // 3. Tentukan apakah ada item yang cocok dan lakukan aksi
  if (matchingItems.length > 0) {
    const firstFoundItem = matchingItems[0]

    // Gulir ke item pertama yang ditemukan
    if (firstFoundItem) {
      firstFoundItem.scrollIntoView({
        behavior: "smooth",
        block: "center",
      })
    }

    // Tambahkan kelas highlight sementara untuk semua item yang cocok
    matchingItems.forEach((item) => {
      item.classList.add("highlight-tiket")
    })

    // Hapus highlight setelah beberapa detik
    setTimeout(() => {
      matchingItems.forEach((item) => {
        item.classList.remove("highlight-tiket")
      })
    }, 3000)

    // Clear search input setelah berhasil
    const searchInput = document.getElementById("searchInputTiket")
    if (searchInput) searchInput.value = ""
  } else {
    // Jika tidak ada item yang cocok sama sekali
    showCustomAlert(
      `Tidak ditemukan hasil untuk "${searchTerm}" di halaman ini.<br><br>Coba kata kunci lain seperti:<br>• candi, kedaton, mahligai<br>• tikar, anyaman, madu<br>• wisata, umkm, kerajinan`,
    )
  }
}

// Fungsi untuk mereset highlight saat input kosong di halaman tiket
document.addEventListener("DOMContentLoaded", () => {
  const searchInputTiket = document.getElementById("searchInputTiket")
  if (searchInputTiket) {
    searchInputTiket.addEventListener("input", () => {
      if (searchInputTiket.value.trim() === "") {
        document.querySelectorAll(".grid-interactive .item-gastro").forEach((item) => {
          item.classList.remove("highlight-tiket")
        })
      }
    })
  }
})

// ==============
// INIT FUNCTION
// ==============
document.addEventListener("DOMContentLoaded", () => {
  console.log("DOM loaded, initializing...")

  initSmoothScrolling()
  document.addEventListener("click", handleOutsideClick)

  // Menyesuaikan tampilan menu saat resize
  window.addEventListener("resize", () => {
    if (window.innerWidth >= 992) {
      closeMobileMenu() // Tutup menu mobile jika beralih ke desktop view
      const navElement = document.querySelector("nav")
      if (navElement && navElement.classList.contains("navbar")) {
        const navLinks = document.querySelector(".navbar .nav-links")
        if (navLinks) {
          // Hapus gaya inline yang mungkin diterapkan untuk mobile
          navLinks.style.transform = ""
          navLinks.style.position = ""
          navLinks.style.background = ""
          navLinks.style.backdropFilter = ""
          navLinks.style.width = ""
          navLinks.style.height = ""
          navLinks.style.justifyContent = ""
          navLinks.style.alignItems = ""
          navLinks.style.padding = ""
          navLinks.style.flexDirection = ""
          navLinks.style.marginTop = ""
          navLinks.style.overflowY = ""
          navLinks.style.marginLeft = ""
          navLinks.style.display = "flex" // Pastikan ditampilkan sebagai flex di desktop
        }
      } else {
        const oldNavMenu = document.querySelector("nav .menu")
        if (oldNavMenu) {
          // Hapus gaya inline untuk navbar lama (index.php)
          oldNavMenu.style.transform = ""
          oldNavMenu.style.position = ""
          oldNavMenu.style.background = ""
          oldNavMenu.style.backdropFilter = ""
          oldNavMenu.style.width = ""
          oldNavMenu.style.height = ""
          oldNavMenu.style.justifyContent = ""
          oldNavMenu.style.alignItems = ""
          oldNavMenu.style.padding = ""
          oldNavMenu.style.flexDirection = ""
          oldNavMenu.style.marginTop = ""
          oldNavMenu.style.overflowY = ""
          oldNavMenu.style.marginLeft = ""
          oldNavMenu.style.display = "flex"
        }
      }
    }
    setMenuDisplay()
  })

  // Handle search term dari localStorage setelah redirect ke index.php
  if (window.location.pathname.endsWith("index.php")) {
    const storedSearchTerm = localStorage.getItem("searchTermForIndex")
    if (storedSearchTerm) {
      localStorage.removeItem("searchTermForIndex")
      setTimeout(() => {
        performSearchOnIndex(storedSearchTerm)
      }, 500) // Beri sedikit delay untuk memastikan halaman selesai di-render
    }
  }

  klikMenu() // Pastikan klikMenu dipanggil untuk inisialisasi semua event listener search
})

// =========================
// RESPONSIVE BEHAVIOR
// =========================
window.addEventListener("resize", setMenuDisplay)

// =========================
// NAVBAR BACKGROUND ON SCROLL
// =========================
window.addEventListener("scroll", () => {
  const nav = document.querySelector("nav")
  const newNavbar = document.querySelector(".navbar")

  if (newNavbar) {
    if (window.scrollY > 0) {
      newNavbar.style.boxShadow = "0 4px 20px rgba(0, 0, 0, 0.15)"
    } else {
      newNavbar.style.boxShadow = "0 2px 20px rgba(0, 0, 0, 0.1)"
    }
  } else if (nav) {
    // Untuk navbar di index.php
    if (window.scrollY > 50) {
      nav.style.backgroundColor = "rgba(75, 46, 36, 0.98)"
    } else {
      nav.style.backgroundColor = "rgba(75, 46, 36, 0.95)"
    }
  }
})
