<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realisasi Anggaran - SIVAST</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-money-bill-wave"></i> Laporan Realisasi Anggaran Tahun 2025</h1>
            <p>Kelola Data Realisasi Anggaran per Bidang</p><br>
            <p id="lastUpdate" style="font-style: italic; font-weight: bold; color: #4b7f90; margin-top: -5px;">Update Terakhir: -</p>
        </div>




<!-- CARD REALISASI -->
<style>
    .card-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr); /* 5 cards per row */
  gap: 10px; /* Reduce space between cards */
  margin-top: 10px;
}
    .info-card2 {
  background: #E9F2FF;
  border-radius: 12px;
  padding: 20px;
  box-shadow: 0 2px 6px rgba(0,0,0,0.1);
  max-width: 300px;
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
  font-size: 10px;
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
  font-size: 20px;
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

</style>

        <div class="card-grid" style="margin-top: 5px;">

<!-- Card Sekretariat -->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/sekretariat.jpg" alt="Sekretariat" class="profile-image">
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
            <span class="stat-label">Realisasi Anggaran</span>
            <div class="stat-value-row">
            <img src="assets/images/money.svg" alt="icon">
            <span class="stat-value" id="sekretariat-totalbudget">Rp 0</span>
    </div>
</div>
<div class="stat-block">
            <span class="stat-label">Persentase</span>
            <div class="stat-value-row">
            <img src="assets/images/persen.svg" alt="icon">
            <span class="stat-value" id="sekretariat-budget">100%</span>
    </div>
</div>
</div>
  </div>
</div>

<!-- Card Sekretariat -->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/ppik.jpg" alt="PPIK" class="profile-image">
    </div>
    <div class="card-text2">
      <h2>PPIK</h2>
      <p>Pengadaan, Pemberhentian dan Informasi Kepegawaian</p>
    </div>
  </div>

  <!-- Body -->
  <div class="card-body2">
    <!-- Statistik kiri -->
    <div class="card-stats">
      <div class="stat-block">
            <span class="stat-label">Realisasi Anggaran</span>
            <div class="stat-value-row">
            <img src="assets/images/money.svg" alt="icon">
            <span class="stat-value" id="ppik-totalbudget">100%</span>
    </div>
</div>
<div class="stat-block">
            <span class="stat-label">Persentase</span>
            <div class="stat-value-row">
            <img src="assets/images/persen.svg" alt="icon">
            <span class="stat-value" id="ppik-budget">100%</span>
    </div>
</div>
</div>
  </div>
</div>

<!-- Card Sekretariat -->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/pkpa.jpg" alt="PKPA" class="profile-image">
    </div>
    <div class="card-text2">
      <h2>PKPA</h2>
      <p>Penilaian Kinerja dan Pengembangan ASN</p>
    </div>
  </div>

  <!-- Body -->
  <div class="card-body2">
    <!-- Statistik kiri -->
    <div class="card-stats">
      <div class="stat-block">
            <span class="stat-label">Realisasi Anggaran</span>
            <div class="stat-value-row">
            <img src="assets/images/money.svg" alt="icon">
            <span class="stat-value" id="pkpa-totalbudget">100%</span>
    </div>
</div>
<div class="stat-block">
            <span class="stat-label">Persentase</span>
            <div class="stat-value-row">
            <img src="assets/images/persen.svg" alt="icon">
            <span class="stat-value" id="pkpa-budget">100%</span>
    </div>
</div>
</div>
  </div>
</div>

<!-- Card Diklat -->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/diklat.jpg" alt="Diklat" class="profile-image">
    </div>
    <div class="card-text2">
      <h2>DIKLAT</h2>
      <p>Pendidikan dan Pelatihan ASN</p>
    </div>
  </div>

  <!-- Body -->
  <div class="card-body2">
    <!-- Statistik kiri -->
    <div class="card-stats">
      <div class="stat-block">
            <span class="stat-label">Realisasi Anggaran</span>
            <div class="stat-value-row">
            <img src="assets/images/money.svg" alt="icon">
            <span class="stat-value" id="diklat-totalbudget">100%</span>
    </div>
</div>
<div class="stat-block">
            <span class="stat-label">Persentase</span>
            <div class="stat-value-row">
            <img src="assets/images/persen.svg" alt="icon">
            <span class="stat-value" id="diklat-budget">100%</span>
    </div>
</div>
</div>
  </div>
</div>

<!-- Card MPASN -->
<div class="info-card2" style="margin-top: 10px";>
  <!-- Header -->
  <div class="card-header2">
    <div class="card-profil">
      <img src="assets/images/mpasn.jpg" alt="MPASN" class="profile-image">
    </div>
    <div class="card-text2">
      <h2>MPASN</h2>
      <p>Mutasi dan Promosi ASN</p>
    </div>
  </div>

  <!-- Body -->
  <div class="card-body2">
    <!-- Statistik kiri -->
    <div class="card-stats">
      <div class="stat-block">
            <span class="stat-label">Realisasi Anggaran</span>
            <div class="stat-value-row">
            <img src="assets/images/money.svg" alt="icon">
            <span class="stat-value" id="mpasn-totalbudget">100%</span>
    </div>
</div>
<div class="stat-block">
            <span class="stat-label">Persentase</span>
            <div class="stat-value-row">
            <img src="assets/images/persen.svg" alt="icon">
            <span class="stat-value" id="mpasn-budget">100%</span>
    </div>
</div>
</div>
  </div>
</div>
        </div>
        <script src="assets/js/dashboard.js"></script>


        <div class="content-actions"style="margin-top: 20px;">
            <div class="action-buttons">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <button class="btn btn-success" onclick="exportExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <?php endif; ?>
                <button class="btn btn-danger" onclick="printPDF()">
                    <i class="fas fa-file-pdf"></i> Print PDF
                </button>
            </div>
            
            <div class="search-filter">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari data..." id="searchInput">
                </div>
                <button class="btn btn-outline" onclick="toggleFilter()">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>

        <div class="filter-panel" id="filterPanel" style="display: none;">
            <div class="filter-row">
                <select id="filterBidang" onchange="filterTable()"></select>
                <select id="filterKegiatan" onchange="filterTable()"></select>
                <select id="filterSubKegiatan" onchange="filterTable()"></select>
                <select id="filterRekening" onchange="filterTable()"></select>
                <button class="btn btn-primary" onclick="applyFilter()">Terapkan</button>
                <button class="btn btn-secondary" onclick="resetFilter()">Reset</button>
            </div>
        </div>

        <div class="table-container">
            <div class="table-scroll">
                <table class="data-table" id="realisasiTable">
                    <thead>
                        <tr>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                            <th rowspan="2">Aksi</th>
                            <?php endif; ?>
                            <th rowspan="2">No</th>
                            <th rowspan="2">Bidang</th>
                            <th rowspan="2">Kegiatan</th>
                            <th rowspan="2">Sub Kegiatan</th>
                            <th rowspan="2">Nama Rekening Belanja</th>
                            <th rowspan="2">Pagu Anggaran</th>
                            <th colspan="12">Realisasi Per Bulan</th>
                            <th rowspan="2">Semester 1</th>
                            <th rowspan="2">Semester 2</th>
                            <th rowspan="2">Total Realisasi</th>
                            <th rowspan="2">Sisa Anggaran</th>
                            <th rowspan="2">Persentase</th>
                        </tr>
                        <tr>
                            <th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th><th>Mei</th><th>Jun</th>
                            <th>Jul</th><th>Ags</th><th>Sep</th><th>Okt</th><th>Nov</th><th>Des</th>
                        </tr>
                    </thead>
                    <tbody id="realisasiBody">
                        <!-- Data akan dimuat via AJAX -->
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="pagination mt-3"></div>
        </div>
    </main>

    <!-- Modal untuk edit data -->
    <?php if ($_SESSION['role'] === 'admin'): ?>
    <div class="modal" id="editModal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2>Edit Realisasi Anggaran</h2>
                <button class="close-btn" onclick="closeEditModal()">&times;</button>
            </div>
            <form id="editForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Bidang</label>
                        <input type="text" id="edit_bidang" readonly>
                    </div>
                    <div class="form-group">
                        <label>Kegiatan</label>
                        <input type="text" id="edit_kegiatan" readonly>
                    </div>
                    <div class="form-group full-width">
                        <label>Sub Kegiatan</label>
                        <input type="text" id="edit_sub_kegiatan" readonly>
                    </div>
                    <div class="form-group full-width">
                        <label>Nama Rekening Belanja</label>
                        <input type="text" id="edit_nama_rekening_belanja" readonly>
                    </div>
                    <div class="form-group">
                        <label for="edit_pagu_anggaran">Pagu Anggaran</label>
                        <input type="number" id="edit_pagu_anggaran" name="pagu_anggaran" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_januari">Realisasi Januari</label>
                        <input type="number" id="edit_realisasi_januari" name="realisasi_januari" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_februari">Realisasi Februari</label>
                        <input type="number" id="edit_realisasi_februari" name="realisasi_februari" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_maret">Realisasi Maret</label>
                        <input type="number" id="edit_realisasi_maret" name="realisasi_maret" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_april">Realisasi April</label>
                        <input type="number" id="edit_realisasi_april" name="realisasi_april" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_mei">Realisasi Mei</label>
                        <input type="number" id="edit_realisasi_mei" name="realisasi_mei" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_juni">Realisasi Juni</label>
                        <input type="number" id="edit_realisasi_juni" name="realisasi_juni" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_juli">Realisasi Juli</label>
                        <input type="number" id="edit_realisasi_juli" name="realisasi_juli" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_agustus">Realisasi Agustus</label>
                        <input type="number" id="edit_realisasi_agustus" name="realisasi_agustus" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_september">Realisasi September</label>
                        <input type="number" id="edit_realisasi_september" name="realisasi_september" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_oktober">Realisasi Oktober</label>
                        <input type="number" id="edit_realisasi_oktober" name="realisasi_oktober" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_november">Realisasi November</label>
                        <input type="number" id="edit_realisasi_november" name="realisasi_november" step="0.01">
                    </div>
                    <div class="form-group">
                        <label for="edit_realisasi_desember">Realisasi Desember</label>
                        <input type="number" id="edit_realisasi_desember" name="realisasi_desember" step="0.01">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Modal untuk view data -->
    <div class="modal" id="viewModal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2>Detail Realisasi Anggaran</h2>
                <button class="close-btn" onclick="closeViewModal()">&times;</button>
            </div>
            <div class="modal-body" id="viewContent">
                <!-- Content akan dimuat via JavaScript -->
            </div>
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="closeViewModal()">Tutup</button>
            </div>
        </div>
    </div>

    <script src="assets/js/realisasi-anggaran.js"></script>
</body>
</html>