<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$bidang = $_GET['bidang'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$bulan = $_GET['bulan'] ?? '';

if (empty($bidang) || empty($tahun) || empty($bulan)) {
    die('Parameter tidak lengkap');
}

// Set headers for PDF
header('Content-Type: text/html; charset=UTF-8');

try {
    // Get SPJ data yang belum lengkap
    $sql = "SELECT 
                sub_kegiatan,
                nama_rekening_belanja,
                alasan_tidak_lengkap,
                nomor_bku,
                keterangan_transaksi,
                status_verifikasi,
                tanggal_verifikasi
            FROM verifikasi_spj 
            WHERE bidang = ? AND tahun = ? AND bulan = ? AND status_verifikasi = 'Belum Lengkap'
            ORDER BY sub_kegiatan, nama_rekening_belanja";
    
    $data = $db->fetchAll($sql, [$bidang, $tahun, $bulan]);
    
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

$tanggalCetak = date('d F Y');
$totalBelumLengkap = count($data);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu Kendali Verifikasi - <?php echo $bidang; ?> - <?php echo $bulan; ?> <?php echo $tahun; ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        
        .kendali-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #000000;
        }
        
        .header-logo {
            margin-right: 20px;
        }
        
        .header-logo img {
            width: 80px;
            height: 80px;
        }
        
        .header-text {
            flex: 1;
            text-align: center;
        }
        
        .header-text h2 {
            margin: 0;
            font-size: 18px;
            color: #4b7f90;
            font-weight: bold;
        }
        
        .header-text h3 {
            margin: 5px 0 15px 0;
            font-size: 16px;
            color: #666;
        }
        
        .header-info {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 10px;
        }
        
        .header-info p {
            margin: 0;
            font-size: 11px;
        }
        
        .kendali-table {
            margin: 20px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #000000;
        }
        
        th, td {
            border: 1px solid #000000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #4b7f90;
            color: #000000;
            font-weight: bold;
            text-align: center;
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-belum-lengkap {
            background: #fef2f2;
            color: #991b1b;
        }
        
        .kendali-footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .footer-info {
            flex: 1;
        }
        
        .footer-info p {
            margin: 5px 0;
            font-size: 11px;
        }
        
        .footer-signature {
            display: flex;
            gap: 100px;
        }
        
        .signature-box {
            text-align: center;
            min-width: 150px;
        }
        
        .signature-box p {
            margin: 5px 0;
            font-size: 11px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #4b7f90;
        }
        
        .no-data i {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #129990;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-btn:hover {
            background: #0f7e77;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        <i class="fas fa-print"></i> Cetak
    </button>
    
    <div class="kendali-header">
        <div class="header-logo">
            <img src="../assets/images/logo-kab.png" alt="Logo BKPSDM">
        </div>
        <div class="header-text">
            <h2>PEMERINTAH KABUPATEN BANDUNG</h2>
            <h2 style="margin-top:5px;">BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA (BKPSDM)</h2>
            <h4>Jalan Raya Soreang KM.17 Pamekaran Soreang Kabupaten Bandung 40912</h4>
        </div>
    </div>
    
    <div class="kendali-table">
        <h2 style="text-align: center;">KARTU KENDALI VERIFIKASI SPJ</h2>
        <div class="header-info">
                <p><strong>Bidang:</strong> <?php echo htmlspecialchars($bidang); ?></p>
                <p><strong>Periode:</strong> <?php echo htmlspecialchars($bulan . ' ' . $tahun); ?></p>
                <p><strong>Status:</strong> Belum Lengkap</p>
            </div>
        <table style="margin-top: 20px";>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 15%;">Sub Kegiatan</th>
                    <th style="width: 10%;">Nama Rekening Belanja</th>
                    <th style="width: 5%;">Status</th>
                    <th style="width: 20%;">Alasan Tidak Lengkap</th>
                    <th style="width: 5%;">Nomor BKU</th>
                    <th style="width: 25%;">Keterangan Transaksi</th>
                    <th style="width: 5%;">Paraf Verifikator</th>
                    <th style="width: 5%;">Paraf Kasubag Keuangan/PPK</th>
                    <th style="width: 5%;">Paraf Bendahara Pengeluaran Pembantu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                <tr>
                    <td colspan="10">
                        <div class="no-data">
                            <p><strong>✓ Semua SPJ Sudah Lengkap!</strong></p>
                            <p>Tidak ada dokumen SPJ yang belum lengkap untuk periode ini.</p>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($data as $index => $item): ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($item['sub_kegiatan']); ?></td>
                        <td><?php echo htmlspecialchars($item['nama_rekening_belanja']); ?></td>
                        <td style="text-align: center;">
                            <span class="status-badge status-belum-lengkap">Belum Lengkap</span>
                        </td>
                        <td><?php echo htmlspecialchars($item['alasan_tidak_lengkap'] ?: '-'); ?></td>
                        <td><?php echo htmlspecialchars($item['nomor_bku']); ?></td>
                        <td><?php echo htmlspecialchars($item['keterangan_transaksi']); ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <div class="kendali-footer">
        <div class="footer-info">
            <p><strong>Total SPJ Belum Lengkap:</strong> <?php echo $totalBelumLengkap; ?> dokumen</p>
            <p><strong>Tanggal Cetak:</strong> <?php echo $tanggalCetak; ?></p>
            <p>Kartu Kendali ini dicetak melalui SIVAST (Sistem Informasi Verifikasi Surat Pertanggungjawaban) Online</p>
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
                <br>
                <p>Pengolah Data dan Informasi (Verifikator),</p>
                <br><br><br>
                <p><strong>SATRIA DWI PUTRA, A.Md.Kom</strong></p>
                <p>NIP. 19970920 202203 1 007</p>
            </div>
        </div>
    </div>
    
    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        };
    </script>
</body>
</html>