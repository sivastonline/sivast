<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Parameter
$case   = $_GET['case'] ?? ($_GET['type'] ?? '');
$bidang = $_GET['bidang'] ?? '';
$tahun  = $_GET['tahun'] ?? '';
$bulan  = $_GET['bulan'] ?? '';

if (empty($case)) {
    die('Parameter case tidak ditemukan');
}

$data = [];
$title = '';
$totalData = 0;

try {
    switch ($case) {
        case 'master-data':
            $sql = "SELECT id, bidang, kuasa_pengguna_anggaran, nama_bendahara, email_bendahara, nama_pptk, email_pptk, kegiatan, sub_kegiatan, nama_rekening_belanja 
                    FROM master_data 
                    ORDER BY bidang, sub_kegiatan";
            $data = $db->fetchAll($sql);
            $title = "Data Master";
            break;

        case 'verifikasi-spj':
            $sql  = "SELECT id, bidang, tahun, bulan, sub_kegiatan, nama_rekening_belanja, alasan_tidak_lengkap, nomor_bku, keterangan_transaksi, status_verifikasi, tanggal_verifikasi FROM verifikasi_spj ORDER BY tanggal_verifikasi DESC";
            $data = $db->fetchAll($sql);
            $title = "Hasil Verifikasi SPJ";
            break;

        case 'realisasi-anggaran':
                $params = [];
                $where = [];

                if (!empty($bidang)) {
                    $where[] = "bidang = ?";
                    $params[] = $bidang;
                }
                if (!empty($tahun)) {
                    $where[] = "tahun = ?";
                    $params[] = $tahun;
                }
                if (!empty($bulan)) {
                    $where[] = "bulan = ?";
                    $params[] = $bulan;
                }

                $sql = "SELECT bidang, kegiatan, sub_kegiatan, nama_rekening_belanja, pagu_anggaran,
                               realisasi_januari, realisasi_februari, realisasi_maret, realisasi_april,
                               realisasi_mei, realisasi_juni, realisasi_juli, realisasi_agustus,
                               realisasi_september, realisasi_oktober, realisasi_november, realisasi_desember
                        FROM realisasi_anggaran";

                if (!empty($where)) {
                    $sql .= " WHERE " . implode(" AND ", $where);
                }

                $sql .= " ORDER BY bidang, kegiatan, sub_kegiatan";

                $data = $db->fetchAll($sql, $params);
                $title = "Realisasi Anggaran";
                break;

        case 'hasil-verifikasi':
            if (empty($bidang)) {
                die('Parameter bidang tidak ditemukan untuk hasil-verifikasi');
            }
            $sql = "SELECT id, bidang, tahun, bulan, sub_kegiatan, nama_rekening_belanja, alasan_tidak_lengkap, nomor_bku, keterangan_transaksi, status_verifikasi, tanggal_verifikasi 
                    FROM verifikasi_spj 
                    WHERE bidang = ? 
                    ORDER BY tahun, bulan, sub_kegiatan";
            $data = $db->fetchAll($sql, [$bidang]);
            $title = "Rekapitulasi Hasil Verifikasi SPJ - " . htmlspecialchars($bidang);
            break;

        default:
            die('Case tidak valid');
    }

    $totalData = count($data);

} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}

// Tanggal cetak
$tanggalCetak = date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Dokumen SIVAST Online</title>
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
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #000000;
        }
        
        .header-logo {
            margin-right: 15px;
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

    <h2 style="text-align:center; text-transform:uppercase;"><?php echo $title; ?></h2>

    <table class="kendali-table">
        <thead>
            <?php if ($case == 'master-data'): ?>
                <tr>
                    <th>No</th>
                    <th>Bidang</th>
                    <th>KPA</th>
                    <th>Bendahara</th>
                    <th>Email Bendahara</th>
                    <th>PPTK</th>
                    <th>Email PPTK</th>
                    <th>Kegiatan</th>
                    <th>Sub Kegiatan</th>
                    <th>Rekening Belanja</th>
                </tr>
            <?php elseif ($case == 'verifikasi-spj'): ?>
                <tr>
                    <th>No</th>
                    <th>Bidang</th>
                    <th>Tahun</th>
                    <th>Bulan</th>
                    <th>Sub Kegiatan</th>
                    <th>Rekening Belanja</th>
                    <th>Alasan Tidak Lengkap</th>
                    <th>Nomor BKU</th>
                    <th>Keterangan Transaksi</th>
                    <th>Status</th>
                    <th>Tanggal Verifikasi</th>
                </tr>
                <?php elseif ($case == 'realisasi-anggaran'): ?>
                <tr>
                    <th>No</th>
                    <th>Bidang</th>
                    <th>Kegiatan</th>
                    <th>Sub Kegiatan</th>
                    <th>Rekening Belanja</th>
                    <th>Pagu Anggaran</th>
                    <th>Jan</th>
                    <th>Feb</th>
                    <th>Mar</th>
                    <th>Apr</th>
                    <th>Mei</th>
                    <th>Jun</th>
                    <th>Jul</th>
                    <th>Agu</th>
                    <th>Sep</th>
                    <th>Okt</th>
                    <th>Nov</th>
                    <th>Des</th>
                    <th>Semester 1</th>
                    <th>Semester 2</th>
                    <th>Total Realisasi</th>
                    <th>Sisa Anggaran</th>
                    <th>%</th>
                </tr>
            <?php else: ?>
                <tr>
                    <th>No</th>
                    <th>Bidang</th>
                    <th>Tahun</th>
                    <th>Bulan</th>
                    <th>Sub Kegiatan</th>
                    <th>Rekening Belanja</th>
                    <th>Alasan Tidak Lengkap</th>
                    <th>Nomor BKU</th>
                    <th>Keterangan Transaksi</th>
                    <th>Status</th>
                    <th>Tanggal Verifikasi</th>
                </tr>
            <?php endif; ?>
        </thead>
        <tbody>
            <?php if (!empty($data)): ?>
                <?php foreach ($data as $i => $row): ?>
                    <?php if ($case == 'master-data'): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= htmlspecialchars($row['bidang']) ?></td>
                            <td><?= htmlspecialchars($row['kuasa_pengguna_anggaran']) ?></td>
                            <td><?= htmlspecialchars($row['nama_bendahara']) ?></td>
                            <td><?= htmlspecialchars($row['email_bendahara']) ?></td>
                            <td><?= htmlspecialchars($row['nama_pptk']) ?></td>
                            <td><?= htmlspecialchars($row['email_pptk']) ?></td>
                            <td><?= htmlspecialchars($row['kegiatan']) ?></td>
                            <td><?= htmlspecialchars($row['sub_kegiatan']) ?></td>
                            <td><?= htmlspecialchars($row['nama_rekening_belanja']) ?></td>
                        </tr>
                    <?php elseif ($case == 'verifikasi-spj'): ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= htmlspecialchars($row['bidang']) ?></td>
                            <td><?= htmlspecialchars($row['tahun']) ?></td>
                            <td><?= htmlspecialchars($row['bulan']) ?></td>
                            <td><?= htmlspecialchars($row['sub_kegiatan']) ?></td>
                            <td><?= htmlspecialchars($row['nama_rekening_belanja']) ?></td>
                            <td><?= htmlspecialchars($row['alasan_tidak_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['nomor_bku']) ?></td>
                            <td><?= htmlspecialchars($row['keterangan_transaksi']) ?></td>
                            <td><?= htmlspecialchars($row['status_verifikasi']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_verifikasi']) ?></td>
                        </tr>
                        <?php elseif ($case == 'realisasi-anggaran'): ?>
                            <?php
                                $semester1 = ($row['realisasi_januari'] ?? 0) + ($row['realisasi_februari'] ?? 0) +
                                             ($row['realisasi_maret'] ?? 0) + ($row['realisasi_april'] ?? 0) +
                                             ($row['realisasi_mei'] ?? 0) + ($row['realisasi_juni'] ?? 0);
                                $semester2 = ($row['realisasi_juli'] ?? 0) + ($row['realisasi_agustus'] ?? 0) +
                                             ($row['realisasi_september'] ?? 0) + ($row['realisasi_oktober'] ?? 0) +
                                             ($row['realisasi_november'] ?? 0) + ($row['realisasi_desember'] ?? 0);
                                $totalRealisasi = $semester1 + $semester2;
                                $pagu = $row['pagu_anggaran'] ?? 0;
                                $sisa = $pagu - $totalRealisasi;
                                $persen = $pagu > 0 ? ($totalRealisasi / $pagu * 100) : 0;
                            ?>
                                <tr>
                                    <td><?= $i+1 ?></td>
                                    <td><?= htmlspecialchars($row['bidang']) ?></td>
                                    <td><?= htmlspecialchars($row['kegiatan']) ?></td>
                                    <td><?= htmlspecialchars($row['sub_kegiatan']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_rekening_belanja']) ?></td>
                                    <td><?= number_format($pagu, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_januari'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_februari'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_maret'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_april'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_mei'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_juni'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_juli'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_agustus'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_september'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_oktober'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_november'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= number_format($row['realisasi_desember'] ?? 0, 0, ',', '.') ?></td>
                                    <td><strong><?= number_format($semester1, 0, ',', '.') ?></strong></td>
                                    <td><strong><?= number_format($semester2, 0, ',', '.') ?></strong></td>
                                    <td><strong><?= number_format($totalRealisasi, 0, ',', '.') ?></strong></td>
                                    <td><strong><?= number_format($sisa, 0, ',', '.') ?></strong></td>
                                    <td><strong><?= number_format($persen, 2, ',', '.') ?>%</strong></td>
                                </tr>
                    <?php else: ?>
                        <tr>
                            <td><?= $i+1 ?></td>
                            <td><?= htmlspecialchars($row['bidang']) ?></td>
                            <td><?= htmlspecialchars($row['tahun']) ?></td>
                            <td><?= htmlspecialchars($row['bulan']) ?></td>
                            <td><?= htmlspecialchars($row['sub_kegiatan']) ?></td>
                            <td><?= htmlspecialchars($row['nama_rekening_belanja']) ?></td>
                            <td><?= htmlspecialchars($row['alasan_tidak_lengkap']) ?></td>
                            <td><?= htmlspecialchars($row['nomor_bku']) ?></td>
                            <td><?= htmlspecialchars($row['keterangan_transaksi']) ?></td>
                            <td><?= htmlspecialchars($row['status_verifikasi']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_verifikasi']) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="no-data">Tidak ada data ditemukan</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>>

    <div class="kendali-footer">
        <div class="footer-info">
            <p><strong>Tanggal Cetak:</strong> <?php echo $tanggalCetak; ?></p>
            <p>Dokumen ini dicetak melalui SIVAST (Sistem Informasi Verifikasi Surat Pertanggungjawaban) Online</p>
        </div>
        <div class="footer-signature">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p><strong>Kepala Sub Bagian Keuangan</strong></p>
                <br><br><br>
                <p><strong><u>MOHAMMAD SALMAN FARISI, S.E., M.Ak</u></strong></p>
                <p>NIP. 19851118 201101 1 001</p>
            </div>
            <div class="signature-box">
                <br>
                <p>Pengolah Data dan Informasi (Verifikator),</p>
                <br><br><br>
                <p><strong><u>SATRIA DWI PUTRA, A.Md.Kom</u></strong></p>
                <p>NIP. 19970920 202203 1 007</p>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            setTimeout(function(){ window.print(); }, 1000);
        }
    </script>
</body>
</html>
