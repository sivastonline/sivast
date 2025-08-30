<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
$nama_user = $_SESSION['nama_user'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIVAST - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">

</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
<!-- DASHBOARD BARU -->
<h2>SELAMAT DATANG di SISTEM INFORMASI VERIFIKASI SPJ ONLINE</h2>
<p>Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM) Kabupaten Bandung</p>

<style>
  .typewriter {
    white-space: nowrap;
    overflow: hidden;
    display: inline-block;
    border-right: 2px solid black; /* Simulasi cursor typewriter */
    animation: caret 0.8s steps(1) infinite;
  }

  /* Animasi untuk caret (cursor) */
  @keyframes caret {
    50% { border-color: transparent; }
  }

  /* Responsif untuk layar kecil (mobile) */
  @media (max-width: 768px) {
    #type1 {
      font-size: 1.5rem; /* Mengurangi ukuran font pada layar kecil */
    }
  }

  /* Default font-size */
  #type1 {
    font-size: 2rem;
  }
</style>


<script>
  function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }

  async function typeLoop(el, speed = 40, delay = 1500) {
    const text = el.dataset.text?.trim() || el.textContent.trim();
    while (true) {
      // Ketik huruf per huruf
      el.textContent = '';
      for (let i = 0; i < text.length; i++) {
        el.textContent += text[i];
        await sleep(speed);
      }
      await sleep(delay);
      // Hapus huruf per huruf
      for (let i = text.length; i >= 0; i--) {
        el.textContent = text.substring(0, i);
        await sleep(speed / 2);
      }
      await sleep(500);
    }
  }

  document.addEventListener('DOMContentLoaded', () => {
    // Menyesuaikan kecepatan dan delay berdasarkan ukuran layar
    const isMobile = window.innerWidth <= 768; // Jika lebar layar <= 768px, anggap sebagai mobile
    const speed = isMobile ? 30 : 40; // Lebih cepat di perangkat mobile
    const delay = isMobile ? 1000 : 2000; // Delay lebih pendek di perangkat mobile

    typeLoop(document.getElementById('type1'), speed, delay);
  });
</script>


<!-- COBA CARD -->
<style>
    .info-card2 {
  background: #E9F2FF;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  max-width: 430px;
}

.card-header2 {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.card-profil img {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  margin-right: 15px;
}

.card-text2 h2 {
  margin: 0;
  font-size: 22px;
  color: #0077a5;
}

.card-text2 p {
  margin: 2px 0 0;
  font-size: 14px;
  color: #555;
}

.card-body2 {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-stats {
  flex: 1;
}

.stat-block {
  margin-bottom: 15px;
}

.stat-label {
  display: block;
  font-size: 14px;
  color: #666;
  margin-bottom: 5px; /* beri jarak ke angka + ikon */
}

.stat-value-row {
  display: flex;
  align-items: center;
  gap: 8px; /* jarak antara ikon dan angka */
  margin-top: 5px;
}

.stat-value-row img {
  width: 30px;
  height: 30px;
  color:#0077a5;
}

.stat-value {
  font-size: 30px;
  font-weight: bold;
  color: #0077a5;
}

.card-gauge {
  width: 200px;
  text-align: center;
  position: relative;
}

.card-gauge .stat-label {
  margin-bottom: 0px; /* Atur jarak tulisan ke gauge */
  font-weight: 600;
}

.gauge-value {
  position: absolute;
  top: 50%; /* biar persis di tengah */
  left: 50%;
  transform: translate(-50%, -50%);
  font-size: 22px;
  font-weight: bold;
  color: #0077a5;
  margin-top:20px;
}

.gauge-subtext {
  position: absolute;
  top: 70%; /* geser ke bawah angka */
  left: 50%;
  transform: translateX(-50%);
  font-size: 0.85rem;
  color: #666;
}
.gauge-min,
.gauge-max {
  position: absolute;
  bottom: 20px;
  font-size: 0.8rem;
  color: #333;
}

.gauge-min {
  left: 5px;   /* posisi kiri */
}

.gauge-max {
  right: 5px;  /* posisi kanan */
}

</style>
<div class="card-grid" style="margin-top: 10px;">
<!-- Card Sekretariat -->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/sekretariat.jpg" alt="PPIK" class="profile-image">
    </div>
    <div class="card-text2">
      <h2>Sekretariat</h2>
      <p>Sekretariat BKPSDM Kabupaten Bandung</p>
    </div>
  </div>

  <!-- Body -->
  <div class="card-body2">
    <!-- Statistik kiri -->
    <div class="card-stats">
      <div class="stat-block">
            <span class="stat-label">Progres SPJ Bulanan</span>
            <div class="stat-value-row">
            <img src="assets/images/benar.svg" alt="icon">
            <span class="stat-value" id="sekretariat-complete">100%</span>
    </div>
</div>
      <div class="stat-block">
            <span class="stat-label" style="color: #991b1b;">SPJ Belum Lengkap</span>
            <div class="stat-value-row">
            <img src="assets/images/salah.svg" alt="icon">
            <span class="stat-value" id="sekretariat-incomplete">100</span>
    </div>
    </div>
</div>

    <!-- Grafik kanan -->
    <div class="card-gauge">
  <canvas id="gauge-sekretariat"></canvas>
  <div class="gauge-value" id="sekretariat-gauge-text">0%</div>
  <div class="gauge-subtext">Progres Rata-rata</div>
  <!-- Tambahan indikator angka -->
  <div class="gauge-min">0</div>
  <div class="gauge-max">100</div>
</div>
  </div>
</div>

<!-- Card Bidang PPIK -->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/ppik.jpg" alt="PPIK" class="profile-image">
    </div>
    <div class="card-text2">
      <h2>Bidang PPIK</h2>
      <p>Pengadaan, Pemberhentian dan Informasi Kepegawaian ASN</p>
    </div>
  </div>

  <!-- Body -->
  <div class="card-body2">
    <!-- Statistik kiri -->
    <div class="card-stats">
      <div class="stat-block">
            <span class="stat-label">Progres SPJ Bulanan</span>
            <div class="stat-value-row">
            <img src="assets/images/benar.svg" alt="icon">
            <span class="stat-value" id="ppik-complete">100%</span>
    </div>
</div>
      <div class="stat-block">
            <span class="stat-label" style="color: #991b1b;">SPJ Belum Lengkap</span>
            <div class="stat-value-row">
            <img src="assets/images/salah.svg" alt="icon">
            <span class="stat-value"id="ppik-incomplete">100</span>
    </div>
    </div>
</div>

    <!-- Grafik kanan -->
    <div class="card-gauge">
  <canvas id="gauge-ppik"></canvas>
  <div class="gauge-value" id="ppik-gauge-text">0%</div>
  <div class="gauge-subtext">Progres Rata-rata</div>
  <!-- Tambahan indikator angka -->
  <div class="gauge-min">0</div>
  <div class="gauge-max">100</div>
</div>
  </div>
</div>

<!-- Card Bidang PKPA -->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/pkpa.jpg" alt="Bidang PKPA" class="profile-image">
    </div>
    <div class="card-text2">
      <h2>Bidang PKPA</h2>
      <p>Penilaian Kinerja dan Pengembangan ASN</p>
    </div>
  </div>

  <!-- Body -->
  <div class="card-body2">
    <!-- Statistik kiri -->
    <div class="card-stats">
      <div class="stat-block">
            <span class="stat-label">Progres SPJ Bulanan</span>
            <div class="stat-value-row">
            <img src="assets/images/benar.svg" alt="icon">
            <span class="stat-value"id="pkpa-complete">100%</span>
    </div>
</div>
      <div class="stat-block">
            <span class="stat-label" style="color: #991b1b;">SPJ Belum Lengkap</span>
            <div class="stat-value-row">
            <img src="assets/images/salah.svg" alt="icon">
            <span class="stat-value" id="pkpa-incomplete">100</span>
    </div>
    </div>
</div>

    <!-- Grafik kanan -->
    <div class="card-gauge">
  <canvas id="gauge-pkpa"></canvas>
  <div class="gauge-value" id="pkpa-gauge-text">0%</div>
  <div class="gauge-subtext">Progres Rata-rata</div>
  <!-- Tambahan indikator angka -->
  <div class="gauge-min">0</div>
  <div class="gauge-max">100</div>
</div>
  </div>
</div>

<!-- Card Bidang Diklat-->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/diklat.jpg" alt="Bidang Diklat" class="profile-image">
    </div>
    <div class="card-text2">
      <h2>Bidang DIKLAT</h2>
      <p>Pendidikan dan Pelatihan ASN</p>
    </div>
  </div>

  <!-- Body -->
  <div class="card-body2">
    <!-- Statistik kiri -->
    <div class="card-stats">
      <div class="stat-block">
            <span class="stat-label">Progres SPJ Bulanan</span>
            <div class="stat-value-row">
            <img src="assets/images/benar.svg" alt="icon">
            <span class="stat-value" id="diklat-complete">100%</span>
    </div>
</div>
      <div class="stat-block">
            <span class="stat-label" style="color: #991b1b;">SPJ Belum Lengkap</span>
            <div class="stat-value-row">
            <img src="assets/images/salah.svg" alt="icon">
            <span class="stat-value" id="diklat-incomplete">100</span>
    </div>
    </div>
</div>

    <!-- Grafik kanan -->
    <div class="card-gauge">
  <canvas id="gauge-diklat"></canvas>
  <div class="gauge-value" id="diklat-gauge-text">0%</div>
  <div class="gauge-subtext">Progres Rata-rata</div>
  <!-- Tambahan indikator angka -->
  <div class="gauge-min">0</div>
  <div class="gauge-max">100</div>
</div>
  </div>
</div>

<!-- Card Bidang MPASN-->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/mpasn.jpg" alt="Bidang MPASN" class="profile-image">
    </div>
    <div class="card-text2">
      <h2>Bidang MPASN</h2>
      <p>Mutasi dan Promosi ASN</p>
    </div>
  </div>

  <!-- Body -->
  <div class="card-body2">
    <!-- Statistik kiri -->
    <div class="card-stats">
      <div class="stat-block">
            <span class="stat-label">Progres SPJ Bulanan</span>
            <div class="stat-value-row">
            <img src="assets/images/benar.svg" alt="icon">
            <span class="stat-value" id="mpasn-complete">100%</span>
    </div>
</div>
      <div class="stat-block">
            <span class="stat-label" style="color: #991b1b;">SPJ Belum Lengkap</span>
            <div class="stat-value-row">
            <img src="assets/images/salah.svg" alt="icon">
            <span class="stat-value" id="mpasn-incomplete">100</span>
    </div>
    </div>
</div>

    <!-- Grafik kanan -->
    <div class="card-gauge">
  <canvas id="gauge-mpasn"></canvas>
  <div class="gauge-value" id="mpasn-gauge-text">0%</div>
  <div class="gauge-subtext">Progres Rata-rata</div>
  <!-- Tambahan indikator angka -->
  <div class="gauge-min">0</div>
  <div class="gauge-max">100</div>
</div>
  </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="assets/js/dashboard.js"></script>

<!-- JUDUL REKAP TABEL -->
        <div class="recap-table"style="margin-top: 20px;">
            <div style="display: flex; align-items: center; margin-top: 20px;">
    <!-- Logo BKPSDM -->
    <img src="assets/images/logo-kab.png" alt="Logo BKPSDM" style="height: 70px; margin-right: 10px;">
    
    <!-- Judul -->
    <div>
        <h2 style="margin: 0;">Rekapitulasi Pengumpulan SPJ</h2>
        <p style="margin: 0; font-size: 16px;">
            Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM) Kabupaten Bandung
        </p>
    </div>
</div>

<!-- FILTER BULAN TAHUN -->
<div class="filter-container" id="filter-section">
    <h4 style="margin-top: 10px;">Filter Bulan</h4>

    <div class="dropdown">
        <button type="button" class="dropdown-toggle" onclick="toggleDropdown()">Pilih Bulan ▼</button>
        <div id="month-dropdown" class="dropdown-menu">
            <?php
            $monthNames = [
                1 => "JAN", 2 => "FEB", 3 => "MAR",
                4 => "APR", 5 => "MEI", 6 => "JUN",
                7 => "JUL", 8 => "AGS", 9 => "SEP",
                10 => "OKT", 11 => "NOV", 12 => "DES"
            ];
            $years = [2024, 2025];
            foreach ($years as $year) {
                for ($m = 1; $m <= 12; $m++) {
                    $val = sprintf("%04d-%02d", $year, $m);
                    $label = $monthNames[$m] . " " . substr($year, 2, 2);
                    echo "<label><input type='checkbox' class='month-filter' value='$val'> $label</label><br>";
                }
            }
            ?>
        </div>
    </div>
    <button class="btn btn-primary" id="apply-filter">Terapkan Filter</button>
    <button class="btn btn-primary" type="button" onclick="selectAllMonths()">Pilih Semua</button>
    <button class="btn btn-primary" type="button" onclick="clearAllMonths()">Hapus Semua</button>
</div>

<style>
/* dropdown style */
.dropdown {
    position: relative;
    display: inline-block;
}
.dropdown-toggle {
    padding: 6px 12px;
    border: 1px solid #ccc;
    background: #f8f8f8;
    cursor: pointer;
}
.dropdown-menu {
    display: none;
    position: absolute;
    background: var(--white-color);
    border-radius: 8px;
    border: 1px solid #ccc;
    padding: 8px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 100;
}
.dropdown-menu label {
    display: block;
    cursor: pointer;
}
.dropdown.open .dropdown-menu {
    display: block;
}
</style>

<script>
function toggleDropdown() {
    document.querySelector(".dropdown").classList.toggle("open");
}

// Tutup dropdown kalau klik di luar
document.addEventListener("click", function(e) {
    if (!e.target.closest(".dropdown")) {
        document.querySelector(".dropdown").classList.remove("open");
    }
});

// Pilih semua / hapus semua
function selectAllMonths() {
    document.querySelectorAll(".month-filter").forEach(cb => cb.checked = true);
}
function clearAllMonths() {
    document.querySelectorAll(".month-filter").forEach(cb => cb.checked = false);
}

// Apply filter
document.addEventListener("DOMContentLoaded", () => {
    const filterBtn = document.getElementById("apply-filter");
    filterBtn.addEventListener("click", () => {
        const selected = Array.from(document.querySelectorAll(".month-filter:checked"))
            .map(cb => cb.value);
        setMonthFilter(selected); // panggil fungsi dari dashboard.js
    });
});
</script>


<!-- TULISAN TERAKHIR UPDATE + TOMBOL HIDE/SHOW -->
<div style="display: flex; justify-content: flex-end; align-items: center; margin-bottom: 5px; gap: 10px;">
    <span id="last-update" style="font-style: italic; font-size: 14px; color: #333;">
        Terakhir Update : -
    </span>
    <button class="btn btn-secondary btn-sm" onclick="toggleFilter()">Hide Filter</button>
</div>

<script>
function updateLastUpdate() {
    const now = new Date();
    const hari = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
    const bulan = ["Januari","Februari","Maret","April","Mei","Juni",
                   "Juli","Agustus","September","Oktober","November","Desember"];
    const dayName = hari[now.getDay()];
    const date = now.getDate();
    const monthName = bulan[now.getMonth()];
    const year = now.getFullYear();
    const time = now.toLocaleTimeString('id-ID');

    document.getElementById("last-update").textContent =
        `Terakhir Update : ${dayName}, ${date} ${monthName} ${year} ${time}`;
}

// Jalankan sekali saat load
updateLastUpdate();
// Update tiap detik
setInterval(updateLastUpdate, 1000);

// Fungsi hide/unhide filter
function toggleFilter() {
    const filter = document.getElementById("filter-section");
    const btn = event.target;
    if (filter.style.display === "none") {
        filter.style.display = "block";
        btn.textContent = "Hide Filter";
    } else {
        filter.style.display = "none";
        btn.textContent = "Show Filter";
    }
}
</script>


<!-- TABEL REKAPITULASI -->
            <div class="table-container"style="margin-top: 10px;">
                <table class="data-table-recap">
                    <thead>
            <tr id="recap-header-row">
                <th>Bidang</th>
                <!-- Kolom bulan akan diisi oleh JS sesuai filter -->
                <th>Progres Bulanan</th>
                <th>Progres Dokumen</th>
            </tr>
        </thead>
                    <tbody id="recap-table-body">
                        <!-- Data akan dimuat via AJAX -->
                    </tbody>
                </table>
        </div>
    </div>


    <!-- KETERANGAN STATUS -->
   <table style="margin-top:10px; border-collapse:collapse;">
  <tr>
    <td style="background-color:#92d050; width:30px;"></td>
    <td style="padding:0 30px 0 10px;">Lengkap, Selesai Verifikasi</td>

    <td style="background-color:#ffc000; width:30px;"></td>
    <td style="padding:0 30px 0 10px;">Sudah Masuk, Perlu Dilengkapi</td>

    <td style="background-color:#c00000; width:30px;"></td>
    <td style="padding-left:10px";>Belum Masuk</td>
  </tr>
</table>
    </main>

    <script src="assets/js/dashboard.js"></script>
<script>
    // Tombol pilih semua / hapus semua
    function selectAllMonths() {
        const select = document.getElementById('month-select');
        Array.from(select.options).forEach(opt => opt.selected = true);
    }
    function clearAllMonths() {
        const select = document.getElementById('month-select');
        Array.from(select.options).forEach(opt => opt.selected = false);
    }

    // Mapping bulan untuk header
    document.addEventListener("DOMContentLoaded", () => {
        const monthNames = {
            "2024-01": "JAN 24", "2024-02": "FEB 24", "2024-03": "MAR 24",
            "2024-04": "APR 24", "2024-05": "MEI 24", "2024-06": "JUN 24",
            "2024-07": "JUL 24", "2024-08": "AGS 24", "2024-09": "SEP 24",
            "2024-10": "OKT 24", "2024-11": "NOV 24", "2024-12": "DES 24",
            "2025-01": "JAN 25", "2025-02": "FEB 25", "2025-03": "MAR 25",
            "2025-04": "APR 25", "2025-05": "MEI 25", "2025-06": "JUN 25",
            "2025-07": "JUL 25", "2025-08": "AGS 25", "2025-09": "SEP 25",
            "2025-10": "OKT 25", "2025-11": "NOV 25", "2025-12": "DES 25"
        };

        // observer: setiap kali dashboard.js menggambar ulang header
        const observer = new MutationObserver(() => {
            document.querySelectorAll("#recap-header-row th").forEach(th => {
                const val = th.textContent.trim();
                if (monthNames[val]) {
                    th.textContent = monthNames[val]; // ganti langsung
                }
            });
        });

        observer.observe(document.getElementById("recap-header-row"), { childList: true, subtree: true });
    });
    </script>
</body>
</html>