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
    <title>Kendali Verifikasi - SIVAST</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <style>
        .header-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 10px;
        }
        
        .header-info p {
            margin: 0;
            font-size: 14px;
        }

       .kendali-footer {
    display: flex;
    justify-content: space-between; /* jarak antara info & signature */
    align-items: flex-start;        /* sejajarkan bagian atas */
    margin-top: 30px;
}

.footer-info {
    flex: 1; /* biar fleksibel lebarnya */
}

.footer-signature {
    flex: 1;
    display: flex;
    justify-content: space-around; /* sejajarkan tanda tangan kiri-kanan */
    text-align: center;
}

.signature-box {
    width: 45%; /* masing-masing kotak tanda tangan */
}


.signature-box p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
    color: var(--gray-700);
}
.footer-info p {
    margin: 0.25rem 0;
    font-size: 0.875rem;
    color: var(--gray-700);
}

.preview-section {
    background: var(--white-color);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.preview-header {
    background: var(--white-color);
    color: #000000;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-print"></i> Cetak Kendali Verifikasi</h1>
            <p>Cetak kartu kendali verifikasi SPJ yang belum lengkap</p>
        </div>

        <div class="kendali-container">
            <div class="filter-card">
                <div class="card-header">
                    <h2><i class="fas fa-filter"></i> Filter Data Verifikasi</h2>
                    <p>Pilih bidang, tahun, dan bulan untuk menampilkan data SPJ yang belum lengkap</p>
                </div>
                
                <form id="kendaliForm" class="kendali-form">
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
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tampilkan Data
                        </button>
                        <button type="button" class="btn btn-success" id="cetakBtn" style="display: none;" onclick="cetakKartuKendali()">
                            <i class="fas fa-print"></i> Cetak Kartu Kendali
                        </button>
                    </div>
                </form>
            </div>

            <div class="preview-section" id="previewSection" style="display: none;">
                <div class="preview-header">
                    <h3><i class="fas fa-eye"></i> Preview Kartu Kendali Verifikasi</h3>
                    <div class="preview-info">
                        <span id="previewBidang"></span> - <span id="previewBulan"></span> <span id="previewTahun"></span>
                    </div>
                </div>
                
                <div class="pdf-preview" id="pdfPreview">
                    <div class="kendali-header">
                        <!-- Kop Surat -->
<div style="display: flex; align-items: center; justify-content: center; margin-bottom: 20px;">
    <!-- Logo -->
    <div style="flex: 0 0 auto; margin-right: 15px;">
        <img src="assets/images/logo-kab.png" alt="Logo" style="height: 80px; width: auto;">
    </div>
    <!-- Teks Kop -->
    <div style="text-align: center; flex: 1;">
        <h2 style="margin: 0; font-size: 25px;">PEMERINTAH KABUPATEN BANDUNG</h2>
        <div style="font-weight: bold; font-size: 16px;">
            BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA (BKPSDM)
        </div>
        <div style="font-size: 14px; margin-top: 3px;">
            Jalan Raya Soreang KM.17 Pamekaran Soreang Kab Bandung 40912
        </div>
    </div>
</div>
<hr style="border: 1px solid #000; margin: 10px 0;">

                            <h2 style="margin: 0; font-size: 25px; text-align: center">KARTU KENDALI VERIFIKASI SPJ</h2>
                            <div class="header-info">
                                <p><strong>Bidang:</strong> <span id="headerBidang"></span></p>
                                <p><strong>Periode:</strong> <span id="headerBulan"></span> <span id="headerTahun"></span></p>
                                <p><strong>Status:</strong> Belum Lengkap</p>
                            </div>
                    </div>
                    
                    <div class="kendali-table" style="margin-top:10px">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Sub Kegiatan</th>
                                    <th>Nama Rekening Belanja</th>
                                    <th>Status</th>
                                    <th>Alasan Tidak Lengkap</th>
                                    <th>Nomor BKU</th>
                                    <th>Keterangan Transaksi</th>
                                    <th>Paraf Verifikator</th>
                                    <th>Paraf Kasubag Keuangan/PPK</th>
                                    <th>Paraf Bendahara Pengeluaran Pembatu</th>
                                </tr>
                            </thead>
                            <tbody id="kendaliTableBody">
                                <!-- Data akan dimuat via AJAX -->
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="kendali-footer">
                        <div class="footer-info">
                            <p><strong>Total SPJ Belum Lengkap:</strong> <span id="totalBelumLengkap">0</span> dokumen</p>
                            <p><strong>Tanggal Cetak:</strong> <span id="tanggalCetak"></span></p>
                            <p>Kartu Kendali ini dicetak melalui SIVAST </p>
                            <p>(Sistem Informasi Verifikasi Surat Pertanggungjawaban) Online</p>
                        </div>
                        <div class="footer-signature">
                            <div class="signature-box">
                                <p>Mengetahui,</p>
                                <p><strong>Kepala Sub Bagian Keuangan</strong></p>
                                <br><br><br>
                                <p><strong>MOHAMMAD SALMAN FARISI, S.E., M.Ak</strong></p>
                                <p>NIP. 19851118 201101 1 001</p>
                            </div>
                            <div class="signature-box">
                                <p>Pengolah Data dan Informasi (Verifikator),</p>
                                <br><br><br><br>
                                <p><strong>SATRIA DWI PUTRA, A.Md.Kom</strong></p>
                                <p>NIP. 19970920 202203 1 007</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/kendali-verifikasi.js"></script>
</body>
</html>