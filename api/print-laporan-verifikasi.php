<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

try {
    // Hitung total berdasarkan status
    $totalBelumLengkap = $db->fetch("SELECT COUNT(*) as total FROM verifikasi_spj WHERE status_verifikasi = 'Belum Lengkap'")['total'];
    $totalLengkap      = $db->fetch("SELECT COUNT(*) as total FROM verifikasi_spj WHERE status_verifikasi = 'Lengkap'")['total'];
    $totalVerifikasi   = $db->fetch("SELECT COUNT(*) as total FROM verifikasi_spj")['total'];

    // Data detail SPJ Belum Lengkap
    $dataBelumLengkap = $db->fetchAll("SELECT bidang, tahun, bulan, sub_kegiatan, nama_rekening_belanja, 
                                              alasan_tidak_lengkap, nomor_bku, keterangan_transaksi, tanggal_verifikasi
                                       FROM verifikasi_spj
                                       WHERE status_verifikasi = 'Belum Lengkap'
                                       ORDER BY bidang, tahun, bulan");

    // === UPDATE: Ambil alasan + bidang untuk analisis per bidang
    $alasanBidangList = $db->fetchAll("
        SELECT bidang, alasan_tidak_lengkap
        FROM verifikasi_spj
        WHERE alasan_tidak_lengkap IS NOT NULL 
          AND alasan_tidak_lengkap != ''
    ");

    // === UPDATE: Mapping kata kunci ke kategori
    $mapping = [
        'tanda tangan' => 'Tanda Tangan Pejabat Terkait Belum Ada',
        'kwitansi'               => 'Kwitansi tidak dilampirkan atau perlu diperbaiki',
        'nota'                      => 'Bukti transaksi/nota tidak dilampirkan atau perlu diperbaiki',
        'bukti pembelian'         => 'Bukti transaksi/nota/tagihan tidak dilampirkan atau perlu diperbaiki',
        'nota pembelian'         => 'Bukti transaksi/nota/tagihan tidak dilampirkan atau perlu diperbaiki',
        'tagihan'         => 'Bukti transaksi/nota/tagihan tidak dilampirkan atau perlu diperbaiki',
        'invoice'         => 'Bukti transaksi/nota/tagihan tidak dilampirkan atau perlu diperbaiki',
        'telat'        => 'Dokumen diserahkan terlambat',
        'rangkap'      => 'Dokumen rangkap tidak ada',
        'surat perintah'      => 'SPJ Perjalanan Dinas Belum Lengkap atau perlu diperbaiki',
        'sp'      => 'SPJ Perjalanan Dinas Belum Lengkap/Perlu diperbaiki',
        'laporan'      => 'Laporan Hasil Kegiatan belum dilampirkan',
        'transfer'      => 'Bukti Transfer Bank BJB belum dilampirkan',
        'pajak'      => 'Bukti Bayar, Id Billing, atau Rekap Pajak belum lengkap/belum dilampirkan',
        'narasumber'      => 'Dokumen Kelengkapan SPJ Narasumber/Pendamping/Rohaniwan atau yang serupa, belum lengkap/belum dilampirkan',
        'pendamping'      => 'Dokumen Kelengkapan SPJ Narasumber/Pendamping/Rohaniwan atau yang serupa, belum lengkap/belum dilampirkan',
        'rohaniwan'      => 'Dokumen Kelengkapan SPJ Narasumber/Pendamping/Rohaniwan atau yang serupa, belum lengkap/belum dilampirkan',
        'permohonan'      => 'Dokumen Pendukung SPJ seperti Surat Permohonan, Surat Tugas, Daftar Hadir, Biodata, Susunan Acara, dsb belum lengkap/belum dilampirkan',
        'tugas'      => 'Dokumen Pendukung SPJ seperti Surat Permohonan, Surat Tugas, Daftar Hadir, Biodata, Susunan Acara, dsb belum lengkap/belum dilampirkan',
        'daftar hadir'      => 'Dokumen Pendukung SPJ seperti Surat Permohonan, Surat Tugas, Daftar Hadir, Biodata, Susunan Acara, dsb belum lengkap/belum dilampirkan',
        'biodata'      => 'Dokumen Pendukung SPJ seperti Surat Permohonan, Surat Tugas, Daftar Hadir, Biodata, Susunan Acara, dsb belum lengkap/belum dilampirkan',
        'susunan acara'      => 'Dokumen Pendukung SPJ seperti Surat Permohonan, Surat Tugas, Daftar Hadir, Biodata, Susunan Acara, dsb belum lengkap/belum dilampirkan',
        'pesanan'      => 'Dokumen Pendukung SPJ seperti Surat Permohonan, Surat Tugas, Daftar Hadir, Biodata, Susunan Acara, dsb belum lengkap/belum dilampirkan',
'undangab'      => 'Dokumen Pendukung SPJ seperti Surat Permohonan, Surat Tugas, Daftar Hadir, Biodata, Susunan Acara, dsb belum lengkap/belum dilampirkan',


    ];

    // === UPDATE: Proses analisis per bidang
    $analisisPerBidang = [];
    foreach ($alasanBidangList as $row) {
        $alasan = strtolower($row['alasan_tidak_lengkap']);
        $bidang = $row['bidang'];
        $kategori = 'Lainnya';
        foreach ($mapping as $keyword => $kat) {
            if (strpos($alasan, $keyword) !== false) {
                $kategori = $kat;
                break;
            }
        }
        if (!isset($analisisPerBidang[$kategori])) {
            $analisisPerBidang[$kategori] = [];
        }
        if (!isset($analisisPerBidang[$kategori][$bidang])) {
            $analisisPerBidang[$kategori][$bidang] = 0;
        }
        $analisisPerBidang[$kategori][$bidang]++;
    }

// --- Hitung persentase kelengkapan ---
$totalLengkap      = $db->fetch("SELECT COUNT(*) as total FROM verifikasi_spj WHERE status_verifikasi = 'Lengkap'")['total'];
$totalBelumLengkap = $db->fetch("SELECT COUNT(*) as total FROM verifikasi_spj WHERE status_verifikasi = 'Belum Lengkap'")['total'];
$totalVerifikasi   = $totalLengkap + $totalBelumLengkap;

$completePercentage = $totalVerifikasi > 0 
    ? round(($totalLengkap / $totalVerifikasi) * 100, 2) 
    : 0;

$dataBidang = $db->fetchAll("
    SELECT bidang,
        COUNT(*) as total,
        SUM(CASE WHEN status_verifikasi = 'Lengkap' THEN 1 ELSE 0 END) as total_lengkap,
        SUM(CASE WHEN status_verifikasi = 'Belum Lengkap' THEN 1 ELSE 0 END) as total_belum
    FROM verifikasi_spj
    GROUP BY bidang
    ORDER BY bidang ASC
");

} catch (Exception $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}

$tanggalCetak = date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Verifikasi SPJ</title>
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
            font-size: 20px;
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
            gap: 350px;
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
    <button class="print-btn no-print" onclick="window.print()">Cetak</button>

<!-- KOP SURAT -->
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



<!-- JUDUL LAPORAN -->
    <h2 style="text-align:center; text-transform:uppercase;text-decoration:underline; margin-bottom: 5px;">LAPORAN VERIFIKASI SURAT PERTANGGUNGJAWABAN (SPJ)</h2>
    <h3 style="text-align:center; margin-bottom: 5px;">Untuk Periode s/d Tanggal <?php echo $tanggalCetak; ?></h3>
    


<!-- IDENTITAS PEJABAT VERIFIKATOR -->

<h3 style="text-align:left; margin-bottom: 10px; margin-top: 20px;">I. IDENTITAS VERIFIKATOR</h3>
<div style="display: flex; gap: 4px; margin-bottom:7px;text-indent: 13px;">
  <div style="min-width: 250px; text-align: left;">Nama</div>
  <div>: SATRIA DWI PUTRA, A.Md.Kom</div>
</div>
<div style="display: flex; gap: 4px;margin-bottom:7px;text-indent: 13px;">
  <div style="min-width: 250px; text-align: left;">NIP</div>
  <div>: 19970920 202203 1 007</div>
</div>
<div style="display: flex; gap: 4px;margin-bottom:7px;text-indent: 13px;">
  <div style="min-width: 250px; text-align: left;">Jabatan</div>
  <div>: Pengolah Data dan Informasi Sub Bagian Keuangan</div>
</div>
<div style="display: flex; gap: 4px;margin-bottom:7px;text-indent: 13px;">
  <div style="min-width: 250px; text-align: left;">Unit Kerja</div>
  <div>: Sekretariat Badan Kepegawaian dan Pengembangan Sumber Daya Manusia (BKPSDM)</div>
</div>



<!-- DASAR PELAKSANAAN -->

<h3 style="text-align:left; margin-bottom: 10px;margin-top: 20px;">II. DASAR PELAKSANAAN VERIFIKASI</h3>

<div style="display: flex; gap: 4px; margin-bottom:10px;">
  <div style="min-width: 10px; text-align: left;text-indent: 13px;">1.</div>
  <div>Undang-Undang Nomor 1 Tahun 2004 tentang Perbendaharaan Negara</div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px;text-indent: 13px;">2.</div>
  <div>Peraturan Pemerintah Nomor 12 Tahun 2019 Tentang Pengelolaan Keuangan Daerah </div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px;text-indent: 13px;">3.</div>
  <div>Peraturan Menteri Dalam Negeri Nomor 77 Tahun 2020 Tentang Pedoman Teknis Pengelolaan Keuangan Daerah</div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px;text-indent: 13px;">4.</div>
  <div>Peraturan Menteri Keuangan (PMK) Nomor 113/PMK.05/2012 Tentang Mekanisme Pelaksanaan Anggaran oleh Satuan Kerja</div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px;text-indent: 13px;">5.</div>
  <div>Peraturan Bupati Bandung Nomor 241 Tahun 2024 Tentang Sistem dan Prosedur Pengelolaan Keuangan Daerah</div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px;text-indent: 13px;">6.</div>
  <div>Peraturan Bupati Bandung Nomor 111 Tahun 2023 tentang Tugas, Fungsi dan Tata Kerja Badan Kepegawaian dan Pengembangan Sumber Daya Manusia</div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px;text-indent: 13px;">7.</div>
  <div>Standar Operasional Prosedur (SOP) VERIFIKASI KELENGKAPAN SURAT PERTANGGUNGJAWABAN (SPJ) KEUANGAN Nomor_____  </div>
</div>

<!-- MAKSUD DAN TUJUAN -->

<h3 style="text-align:left; margin-bottom: 10px;margin-top: 20px;">III. MAKSUD DAN TUJUAN</h3>
<p style="text-indent: 13px;text-align: justify;">
Maksud pelaksanaan verifikasi adalah untuk memastikan bahwa dokumen Surat Pertanggungjawaban (SPJ) dari masing-masing bidang telah sesuai dengan ketentuan administrasi, prosedur akuntansi, dan peraturan perundang-undangan yang berlaku. Dengan Tujuan sebagai berikut :</p>
<div style="display: flex; gap: 4px;text-indent: 13px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px">1.</div>
  <div>Memastikan kebenaran administrasi dan kelengkapan dokumen SPJ</div>
</div>
<div style="display: flex; gap: 4px;text-indent: 13px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px">2.</div>
  <div>Memastikan realisasi anggaran sesuai dengan rencana dan ketentuan</div>
</div>
<div style="display: flex; gap: 4px;text-indent: 13px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px">3.</div>
  <div>Memberikan rekomendasi tindak lanjut untuk perbaikan jika ditemukan ketidaksesuaian</div>
</div>


<!-- METODE VERIFIKASI -->

<h3 style="text-align:left; margin-bottom: 10px;margin-top: 20px;">IV. METODE VERIFIKASI</h3>
<p style="text-indent: 13px;text-align: justify;">Verifikasi dilakukan dengan metode sebagai berikut :</p>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px">1.</div>
  <div>Pemeriksaan fisik dokumen (hardcopy)</div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px">2.</div>
  <div>Pencocokan dengan data realisasi anggaran di aplikasi Penatausahaan SIPD (Sistem Informasi Pemerintahan Daerah) Kemendagri</div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 10px; text-align: left;margin-bottom:7px">3.</div>
  <div>Klarifikasi dengan pejabat pengelola keuangan bidang terkait dalam hal ini Bendahara</div>
</div>


<!-- HASIL VERIFIKASI -->

<h3 style="text-align:left; margin-bottom: 10px;margin-top: 20px;">V. HASIL VERIFIKASI</h3>
    <p style="text-indent: 13px;text-align: justify;">Berdasarkan Hasil Pelaksanaan Verifikasi SPJ untuk periode s/d tanggal <?php echo $tanggalCetak; ?>, diperoleh data sebagai berikut:</p>

<div style="display: flex; gap: 4px;">
  <div style="min-width: 250px; text-align: left;">Total Dokumen SPJ Diverifikasi</div>
  <div>: <strong><?= $totalVerifikasi ?> Dokumen</strong></div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 250px; text-align: left;">Total Dokumen SPJ Lengkap</div>
  <div>: <strong><?= $totalLengkap ?> Dokumen</strong></div>
</div>
<div style="display: flex; gap: 4px;">
  <div style="min-width: 250px; text-align: left;">Total SPJ Belum Lengkap</div>
  <div>: <strong><?= $totalBelumLengkap ?> Dokumen</strong></div>
</div>
<p style="margin-top:15px; font-size:14px;">
    Persentase Dokumen Lengkap Keseluruhan: <strong><?= $completePercentage ?>%</strong>
</p>


<h3 style="text-align:left;">a. Rekapitulasi Hasil Verifikasi per Bidang/Sekretariat</h3>
<p style="text-indent: 13px;text-align: justify;" >Dengan rekapitulasi hasil verifikasi per Bidang/Sekretariat pada tabel berikut :</p>
<table border="1" cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse; margin-top:10px; font-size:14px;">
    <thead style="background:#f2f2f2;">
        <tr>
            <th style="text-align:center;">No</th>
            <th style="text-align:left;">Bidang/Sekretariat</th>
            <th style="text-align:center;">Jumlah SPJ Tidak Lengkap</th>
            <th style="text-align:center;">SPJ Selesai Dilengkapi</th>
            <th style="text-align:center;">SPJ Belum Selesai</th>
            <th style="text-align:center;">% Selesai Dilengkapi</th>
            <th style="text-align:center;">% Belum Selesai</th>
        </tr>
    </thead>
    <tbody>
    <?php 
    $no = 1;
    foreach ($dataBidang as $row): 
        $persenLengkap = $row['total'] > 0 ? round(($row['total_lengkap'] / $row['total']) * 100, 2) : 0;
        $persenBelum   = $row['total'] > 0 ? round(($row['total_belum'] / $row['total']) * 100, 2) : 0;
    ?>
        <tr>
            <td style="text-align:center;"><?= $no++ ?></td>
            <td><?= htmlspecialchars($row['bidang']) ?></td>
            <td style="text-align:center;"><?= $row['total'] ?></td>
            <td style="text-align:center;"><?= $row['total_lengkap'] ?></td>
            <td style="text-align:center;"><?= $row['total_belum'] ?></td>
            <td style="text-align:center;"><?= $persenLengkap ?>%</td>
            <td style="text-align:center;"><?= $persenBelum ?>%</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

    <h3 style="text-align:left;">b. Daftar SPJ Belum Lengkap</h3>
    <p style="text-indent: 13px;text-align: justify;">Adapun Rincian Hasil Verifikasi SPJ per Sub Kegiatan/Kode Rekening Belanja Bidang/Sekretariat yang masih belum memenuhi kelengkapan SPJ sesuai ketentuan untuk periode s/d <?php echo $tanggalCetak; ?> disajikan dalam tabel sebagai berikut :</p>
    <table>
        <thead>
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
                <th>Tanggal Verifikasi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($dataBelumLengkap)): ?>
                <?php foreach ($dataBelumLengkap as $i => $row): ?>
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
                        <td><?= htmlspecialchars($row['tanggal_verifikasi']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="10" style="text-align:center;">Tidak ada data</td></tr>
            <?php endif; ?>
        </tbody>
    </table>


<!-- ANALISIS KEKURANGAN DOKUMEN -->
    <h3 style="text-align:left; margin-bottom: 10px;margin-top: 20px;">VI. ANALISIS KEKURANGAN DOKUMEN</h3>
    <p style="text-indent: 13px;text-align: justify;">Berdasarkan Hasil Analisis dalam proses Verifikasi SPJ untuk periode s/d tanggal <?php echo $tanggalCetak; ?>, diperoleh beberapa kategori Alasan Dokumen Tidak Lengkap per Bidang/Sekretariat sebagai berikut:</p>

<table border="1" cellspacing="0" cellpadding="6" style="width:100%; border-collapse:collapse; margin-top:10px; font-size:14px;">
        <thead style="background:#f2f2f2;">
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Kategori Alasan</th>
                <th colspan="5" style="text-align:center;">Jumlah Kasus</th>
            </tr>
            <tr>
                <th>Sekretariat</th>
                <th>Bidang DIKLAT</th>
                <th>Bidang PPIK</th>
                <th>Bidang PKPA</th>
                <th>Bidang MPASN</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $no = 1;
        $bidangList = ["Sekretariat","Bidang DIKLAT","Bidang PPIK","Bidang PKPA","Bidang MPASN"];
        foreach ($analisisPerBidang as $kategori => $dataBidang): ?>
            <tr>
                <td style="text-align:center;"><?= $no++ ?></td>
                <td><?= htmlspecialchars($kategori) ?></td>
                <?php foreach ($bidangList as $b): ?>
                    <td style="text-align:center;"><?= $dataBidang[$b] ?? 0 ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<!-- PENUTUP -->
    <h3 style="text-align:left; margin-bottom: 10px;margin-top: 20px;">VII. PENUTUP</h3>
    <p style="text-indent: 13px;text-align: justify;">Demikian laporan verifikasi SPJ bulan [Nama Bulan] Tahun [Tahun] ini disusun untuk digunakan sebagai bahan evaluasi dan tindak lanjut. Atas perhatian dan kerja sama seluruh pihak, diucapkan terima kasih.</p>


<!-- FOOTER -->
    <div class="kendali-footer">
        <div class="footer-signature" style="justify-content: center;">
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
    <p style="font-style: italic; color: gray;">
  ##Dokumen ini dicetak melalui SIVAST Online (Sistem Informasi Verifikasi SPJ Online) BKPSDM Kabupaten Bandung
</p>

    <script>
        window.onload = function() {
            setTimeout(() => window.print(), 1000);
        }
    </script>
</body>
</html>
