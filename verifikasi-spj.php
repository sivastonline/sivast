<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi SPJ - SIVAST</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-check-square"></i> Verifikasi SPJ</h1>
            <p>Kelola proses verifikasi dokumen SPJ</p>
        </div>

        <div class="content-actions" style="margin-top: 10px; font-family: 'Poppins', sans-serif;">
    <button class="tab-btn active" data-bidang="">Semua</button>
    <button class="tab-btn" data-bidang="Sekretariat">Sekretariat</button>
    <button class="tab-btn" data-bidang="Bidang PKPA">Bidang PKPA</button>
    <button class="tab-btn" data-bidang="Bidang PPIK">Bidang PPIK</button>
    <button class="tab-btn" data-bidang="Bidang DIKLAT">Bidang DIKLAT</button>
    <button class="tab-btn" data-bidang="Bidang MPASN">Bidang MPASN</button>
</div>

        <div class="content-actions">
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="addVerifikasi()">
                    <i class="fas fa-plus"></i> Tambah Verifikasi
                </button>
                <button class="btn btn-success" onclick="exportExcel()">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
                <button class="btn btn-info" onclick="importExcel()">
                    <i class="fas fa-file-import"></i> Import Excel
                </button>
                <button class="btn btn-secondary" onclick="downloadTemplate()">
                    <i class="fas fa-download"></i> Template
                </button>
                <button class="btn btn-danger" onclick="printPDF()">
                    <i class="fas fa-file-pdf"></i> Print PDF
                </button>
                <button class="btn btn-danger" onclick="window.open('api/print-laporan-verifikasi.php', '_blank')">
                <i class="fas fa-file-pdf"></i> Cetak Laporan
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
                <select id="filterBidang">
                    <option value="">Semua Bidang</option>
                </select>
                <select id="filterTahun">
                    <option value="">Semua Tahun</option>
                </select>
                <select id="filterBulan">
                    <option value="">Semua Bulan</option>
                </select>
                <select id="filterSubKegiatan">
                    <option value="">Semua Sub Kegiatan</option>
                </select>
                <select id="filterStatus">
                    <option value="">Semua Status</option>
                    <option value="">Lengkap</option>
                    <option value="">Belum Lengkap</option>
                </select>
                <button class="btn btn-primary" onclick="applyFilter()">Terapkan</button>
                <button class="btn btn-secondary" onclick="resetFilter()">Reset</button>
            </div>
        </div>

        <div class="table-container">
            <div class="table-scroll">
            <table class="data-table" id="verifikasiTable">
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
                <tbody id="verifikasiBody">
                    <!-- Data akan dimuat via AJAX -->
                </tbody>
            </table>
        </div>
        <div id="pagination" class="pagination mt-3"></div>
    </div>
    </main>

    <!-- Modal untuk form verifikasi -->
    <div class="modal" id="verifikasiModal">
        <div class="modal-content large">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Verifikasi SPJ</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="verifikasiForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="bidang">Bidang <span class="required">*</span></label>
                        <select id="bidang" name="bidang" required onchange="loadSubKegiatan()">
                            <option value="">Pilih Bidang</option>
                            <option value="Sekretariat">Sekretariat</option>
                            <option value="Bidang PKPA">Bidang PKPA</option>
                            <option value="Bidang PPIK">Bidang PPIK</option>
                            <option value="Bidang DIKLAT">Bidang DIKLAT</option>
                            <option value="Bidang MPASN">Bidang MPASN</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tahun">Tahun <span class="required">*</span></label>
                        <select id="tahun" name="tahun" required>
                            <option value="">Pilih Tahun</option>
                            <option value="2024">2024</option>
                            <option value="2025">2025</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="bulan">Bulan <span class="required">*</span></label>
                        <select id="bulan" name="bulan" required>
                            <option value="">Pilih Bulan</option>
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
                    </div>

                    <div class="form-group">
                        <label for="sub_kegiatan">Sub Kegiatan <span class="required">*</span></label>
                        <select id="sub_kegiatan" name="sub_kegiatan" required onchange="loadRekeningBelanja()">
                            <option value="">Pilih Sub Kegiatan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nama_rekening_belanja">Nama Rekening Belanja <span class="required">*</span></label>
                        <select id="nama_rekening_belanja" name="nama_rekening_belanja" required>
                            <option value="">Pilih Rekening Belanja</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nomor_bku">Nomor BKU <span class="required">*</span></label>
                        <input type="text" id="nomor_bku" name="nomor_bku" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="keterangan_transaksi">Keterangan Transaksi <span class="required">*</span></label>
                        <textarea id="keterangan_transaksi" name="keterangan_transaksi" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status_verifikasi">Status Verifikasi <span class="required">*</span></label>
                        <select id="status_verifikasi" name="status_verifikasi" required onchange="toggleAlasanField()">
                            <option value="">Pilih Status</option>
                            <option value="Lengkap">Lengkap</option>
                            <option value="Belum Lengkap">Belum Lengkap</option>
                        </select>
                    </div>

                    <div class="form-group" id="alasanGroup" style="display: none;">
                        <label for="alasan_tidak_lengkap">Alasan Tidak Lengkap</label>
                        <textarea id="alasan_tidak_lengkap" name="alasan_tidak_lengkap" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="tanggal_verifikasi">Tanggal Verifikasi <span class="required">*</span></label>
                        <input type="date" id="tanggal_verifikasi" name="tanggal_verifikasi" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/verifikasi-spj.js"></script>
</body>
</html>