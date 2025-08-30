<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$bidang = $_GET['bidang'] ?? 'sekretariat';
$bidang_names = [
    'sekretariat' => 'Sekretariat',
    'pkpa' => 'Bidang PKPA',
    'ppik' => 'Bidang PPIK',
    'diklat' => 'Bidang DIKLAT',
    'mpasn' => 'Bidang MPASN'
];

$current_bidang = $bidang_names[$bidang] ?? 'Sekretariat';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Verifikasi <?php echo $current_bidang; ?> - SIVAST</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-clipboard-check"></i> Hasil Verifikasi - <?php echo $current_bidang; ?></h1>
            <p>Data hasil verifikasi SPJ untuk <?php echo $current_bidang; ?></p>
        </div>

        <div class="content-actions">
            <div class="action-buttons">
                <button class="btn btn-success" onclick="exportExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
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
                <select id="filterTahun">
                    <option value="">Semua Tahun</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
                <select id="filterBulan">
                    <option value="">Semua Bulan</option>
                    <option value="Januari">Januari</option>
                    <option value="Februari">Februari</option>
                    <option value="Maret">Maret</option>
                    <option value="April">April</option>
                    <option value="Mei">Mei</option>
                    <option value="Juni">Juni</option>
                    <option value="Juli">Juli</option>
                    <option value="Agustus">Agustus</option>
                    <option value="September">September</option>
                    <option value="Oktober">Oktober</option>
                    <option value="November">November</option>
                    <option value="Desember">Desember</option>
                </select>
                <select id="filterSubKegiatan">
                    <option value="">-- Semua Sub Kegiatan --</option>
                    </select>
                <select id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="Lengkap">Lengkap</option>
                    <option value="Belum Lengkap">Belum Lengkap</option>
                </select>
                <button class="btn btn-primary" onclick="applyFilter()">Terapkan</button>
                <button class="btn btn-secondary" onclick="resetFilter()">Reset</button>
            </div>
        </div>

        <div class="table-container">
            <div class="table-scroll">
            <table class="data-table" id="hasilTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Aksi</th>
                        <th>Status Verifikasi</th>
                        <th>Tanggal Verifikasi</th>
                        <th>Bidang</th>
                        <th>Tahun</th>
                        <th>Bulan</th>
                        <th>Sub Kegiatan</th>
                        <th>Nama Rekening Belanja</th>
                        <th>Alasan Tidak Lengkap</th>
                        <th>Nomor BKU</th>
                        <th>Keterangan Transaksi</th>
                    </tr>
                </thead>
                <tbody id="hasilBody">
                    <!-- Data akan dimuat via AJAX -->
                </tbody>
            </table>
        </div>
        <div id="pagination" class="pagination-container mt-3"></div>
        </div>
    </main>

    <!-- Modal untuk view data -->
    <div class="modal" id="viewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Detail Verifikasi SPJ</h2>
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

    <script src="assets/js/hasil-verifikasi.js"></script>
    <script>
        // Set bidang untuk JavaScript
        window.currentBidang = '<?php echo $current_bidang; ?>';
    </script>
</body>
</html>