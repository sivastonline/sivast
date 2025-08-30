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
    <title>Master Data - SIVAST</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-database"></i> Master Data</h1>
            <p>Kelola data master sistem verifikasi SPJ</p>
        </div>

        <div class="content-actions">
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="addData()">
                    <i class="fas fa-plus"></i> Tambah Data
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
                    <option value="Sekretariat">Sekretariat</option>
                    <option value="Bidang PKPA">Bidang PKPA</option>
                    <option value="Bidang PPIK">Bidang PPIK</option>
                    <option value="Bidang DIKLAT">Bidang DIKLAT</option>
                    <option value="Bidang MPASN">Bidang MPASN</option>
                </select>
                <select id="filterKPA">
                    <option value="">-- Semua KPA --</option>
                </select>
                <select id="filterKegiatan">
                    <option value="">-- Semua Kegiatan --</option>
                </select>
                <button class="btn btn-primary" onclick="applyFilter()">Terapkan</button>
                <button class="btn btn-secondary" onclick="resetFilter()">Reset</button>
            </div>
        </div>

        <div class="table-container">
            <div class="table-scroll">
            <table class="data-table" id="masterDataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Bidang</th>
                        <th>Nama Kuasa Pengguna Anggaran</th>
                        <th>Nama Bendahara</th>
                        <th>Email Bendahara</th>
                        <th>Whatsapp Bendahara</th>
                        <th>Nama PPTK</th>
                        <th>Email PPTK</th>
                        <th>Whatsapp PPTK</th>
                        <th>Kegiatan</th>
                        <th>Sub Kegiatan</th>
                        <th>Nama Rekening Belanja</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="masterDataBody">
                    <!-- Data akan dimuat via AJAX -->
                </tbody>
                </table>
        </div>
        <div id="pagination" class="pagination mt-3"></div>
    </div>
    </main>

    <!-- Modal untuk form data -->
    <div class="modal" id="dataModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah Data Master</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="dataForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="bidang">Bidang <span class="required">*</span></label>
                        <select id="bidang" name="bidang" required>
                            <option value="">Pilih Bidang</option>
                            <option value="Sekretariat">Sekretariat</option>
                            <option value="Bidang PKPA">Bidang PKPA</option>
                            <option value="Bidang PPIK">Bidang PPIK</option>
                            <option value="Bidang DIKLAT">Bidang DIKLAT</option>
                            <option value="Bidang MPASN">Bidang MPASN</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="kuasa_pengguna_anggaran">Nama Kuasa Pengguna Anggaran <span class="required">*</span></label>
                        <input type="text" id="kuasa_pengguna_anggaran" name="kuasa_pengguna_anggaran" required>
                    </div>

                    <div class="form-group">
                        <label for="nama_bendahara">Nama Bendahara <span class="required">*</span></label>
                        <input type="text" id="nama_bendahara" name="nama_bendahara" required>
                    </div>

                    <div class="form-group">
                        <label for="email_bendahara">Email Bendahara <span class="required">*</span></label>
                        <input type="email" id="email_bendahara" name="email_bendahara" required>
                    </div>

                    <div class="form-group">
                        <label for="nama_bendahara">Whatsapp Bendahara <span class="required">*</span></label>
                        <input type="text" id="wa_bendahara" name="wa_bendahara" required>
                    </div>

                    <div class="form-group">
                        <label for="nama_pptk">Nama PPTK <span class="required">*</span></label>
                        <input type="text" id="nama_pptk" name="nama_pptk" required>
                    </div>

                    <div class="form-group">
                        <label for="email_pptk">Email PPTK <span class="required">*</span></label>
                        <input type="email" id="email_pptk" name="email_pptk" required>
                    </div>

                    <div class="form-group">
                        <label for="nama_pptk">Whatsapp PPTK <span class="required">*</span></label>
                        <input type="text" id="wa_pptk" name="wa_pptk" required>
                    </div>

                    <div class="form-group">
                        <label for="kegiatan">Kegiatan <span class="required">*</span></label>
                        <input type="text" id="kegiatan" name="kegiatan" required>
                    </div>

                    <div class="form-group">
                        <label for="sub_kegiatan">Sub Kegiatan <span class="required">*</span></label>
                        <input type="text" id="sub_kegiatan" name="sub_kegiatan" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="nama_rekening_belanja">Nama Rekening Belanja <span class="required">*</span></label>
                        <input type="text" id="nama_rekening_belanja" name="nama_rekening_belanja" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/master-data.js"></script>
</body>
</html>