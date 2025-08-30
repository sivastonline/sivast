<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$type = $_GET['type'] ?? '';
$bidang = $_GET['bidang'] ?? '';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="' . $type . '_' . date('Y-m-d') . '.xls"');
header('Cache-Control: max-age=0');

try {
    switch ($type) {
        case 'master-data':
            echo '<table border="1">';
            echo '<tr>';
            echo '<th>No</th>';
            echo '<th>Bidang</th>';
            echo '<th>Kuasa Pengguna Anggaran</th>';
            echo '<th>Nama Bendahara</th>';
            echo '<th>Email Bendahara</th>';
            echo '<th>Nama PPTK</th>';
            echo '<th>Email PPTK</th>';
            echo '<th>Kegiatan</th>';
            echo '<th>Sub Kegiatan</th>';
            echo '<th>Nama Rekening Belanja</th>';
            echo '</tr>';
            
            $sql = "SELECT * FROM master_data ORDER BY bidang, sub_kegiatan";
            $data = $db->fetchAll($sql);
            
            foreach ($data as $index => $item) {
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td>' . htmlspecialchars($item['bidang']) . '</td>';
                echo '<td>' . htmlspecialchars($item['kuasa_pengguna_anggaran']) . '</td>';
                echo '<td>' . htmlspecialchars($item['nama_bendahara']) . '</td>';
                echo '<td>' . htmlspecialchars($item['email_bendahara']) . '</td>';
                echo '<td>' . htmlspecialchars($item['nama_pptk']) . '</td>';
                echo '<td>' . htmlspecialchars($item['email_pptk']) . '</td>';
                echo '<td>' . htmlspecialchars($item['kegiatan']) . '</td>';
                echo '<td>' . htmlspecialchars($item['sub_kegiatan']) . '</td>';
                echo '<td>' . htmlspecialchars($item['nama_rekening_belanja']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            break;
            
        case 'verifikasi-spj':
            echo '<table border="1">';
            echo '<tr>';
            echo '<th>No</th>';
            echo '<th>Bidang</th>';
            echo '<th>Tahun</th>';
            echo '<th>Bulan</th>';
            echo '<th>Sub Kegiatan</th>';
            echo '<th>Nama Rekening Belanja</th>';
            echo '<th>Alasan Tidak Lengkap</th>';
            echo '<th>Nomor BKU</th>';
            echo '<th>Keterangan Transaksi</th>';
            echo '<th>Status Verifikasi</th>';
            echo '<th>Tanggal Verifikasi</th>';
            echo '</tr>';
            
            $sql = "SELECT * FROM verifikasi_spj ORDER BY tanggal_verifikasi DESC";
            $data = $db->fetchAll($sql);
            
            foreach ($data as $index => $item) {
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td>' . htmlspecialchars($item['bidang']) . '</td>';
                echo '<td>' . htmlspecialchars($item['tahun']) . '</td>';
                echo '<td>' . htmlspecialchars($item['bulan']) . '</td>';
                echo '<td>' . htmlspecialchars($item['sub_kegiatan']) . '</td>';
                echo '<td>' . htmlspecialchars($item['nama_rekening_belanja']) . '</td>';
                echo '<td>' . htmlspecialchars($item['alasan_tidak_lengkap'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($item['nomor_bku']) . '</td>';
                echo '<td>' . htmlspecialchars($item['keterangan_transaksi']) . '</td>';
                echo '<td>' . htmlspecialchars($item['status_verifikasi']) . '</td>';
                echo '<td>' . htmlspecialchars($item['tanggal_verifikasi']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            break;
            
        case 'hasil-verifikasi':
            echo '<table border="1">';
            echo '<tr>';
            echo '<th>No</th>';
            echo '<th>Bidang</th>';
            echo '<th>Tahun</th>';
            echo '<th>Bulan</th>';
            echo '<th>Sub Kegiatan</th>';
            echo '<th>Nama Rekening Belanja</th>';
            echo '<th>Alasan Tidak Lengkap</th>';
            echo '<th>Nomor BKU</th>';
            echo '<th>Keterangan Transaksi</th>';
            echo '<th>Status Verifikasi</th>';
            echo '<th>Tanggal Verifikasi</th>';
            echo '</tr>';
            
            $sql = "SELECT * FROM verifikasi_spj";
            $params = [];
            
            if ($bidang) {
                $sql .= " WHERE bidang = ?";
                $params[] = $bidang;
            }
            
            $sql .= " ORDER BY tanggal_verifikasi DESC";
            $data = $db->fetchAll($sql, $params);
            
            foreach ($data as $index => $item) {
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td>' . htmlspecialchars($item['bidang']) . '</td>';
                echo '<td>' . htmlspecialchars($item['tahun']) . '</td>';
                echo '<td>' . htmlspecialchars($item['bulan']) . '</td>';
                echo '<td>' . htmlspecialchars($item['sub_kegiatan']) . '</td>';
                echo '<td>' . htmlspecialchars($item['nama_rekening_belanja']) . '</td>';
                echo '<td>' . htmlspecialchars($item['alasan_tidak_lengkap'] ?? '') . '</td>';
                echo '<td>' . htmlspecialchars($item['nomor_bku']) . '</td>';
                echo '<td>' . htmlspecialchars($item['keterangan_transaksi']) . '</td>';
                echo '<td>' . htmlspecialchars($item['status_verifikasi']) . '</td>';
                echo '<td>' . htmlspecialchars($item['tanggal_verifikasi']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            break;
            
        case 'realisasi-anggaran':
            echo '<table border="1">';
            echo '<tr>';
            echo '<th>No</th>';
            echo '<th>Bidang</th>';
            echo '<th>Kegiatan</th>';
            echo '<th>Sub Kegiatan</th>';
            echo '<th>Nama Rekening Belanja</th>';
            echo '<th>Pagu Anggaran</th>';
            echo '<th>Realisasi Januari</th>';
            echo '<th>Realisasi Februari</th>';
            echo '<th>Realisasi Maret</th>';
            echo '<th>Realisasi April</th>';
            echo '<th>Realisasi Mei</th>';
            echo '<th>Realisasi Juni</th>';
            echo '<th>Realisasi Juli</th>';
            echo '<th>Realisasi Agustus</th>';
            echo '<th>Realisasi September</th>';
            echo '<th>Realisasi Oktober</th>';
            echo '<th>Realisasi November</th>';
            echo '<th>Realisasi Desember</th>';
            echo '<th>Semester 1</th>';
            echo '<th>Semester 2</th>';
            echo '<th>Total Realisasi</th>';
            echo '<th>Sisa Anggaran</th>';
            echo '<th>Persentase</th>';
            echo '</tr>';
            
            $sql = "SELECT * FROM realisasi_anggaran ORDER BY bidang, sub_kegiatan";
            $data = $db->fetchAll($sql);
            
            foreach ($data as $index => $item) {
                $semester1 = floatval($item['realisasi_januari']) + floatval($item['realisasi_februari']) + 
                           floatval($item['realisasi_maret']) + floatval($item['realisasi_april']) + 
                           floatval($item['realisasi_mei']) + floatval($item['realisasi_juni']);
                
                $semester2 = floatval($item['realisasi_juli']) + floatval($item['realisasi_agustus']) + 
                           floatval($item['realisasi_september']) + floatval($item['realisasi_oktober']) + 
                           floatval($item['realisasi_november']) + floatval($item['realisasi_desember']);
                
                $totalRealisasi = $semester1 + $semester2;
                $paguAnggaran = floatval($item['pagu_anggaran']);
                $sisaAnggaran = $paguAnggaran - $totalRealisasi;
                $persentase = $paguAnggaran > 0 ? ($totalRealisasi / $paguAnggaran * 100) : 0;
                
                echo '<tr>';
                echo '<td>' . ($index + 1) . '</td>';
                echo '<td>' . htmlspecialchars($item['bidang']) . '</td>';
                echo '<td>' . htmlspecialchars($item['kegiatan']) . '</td>';
                echo '<td>' . htmlspecialchars($item['sub_kegiatan']) . '</td>';
                echo '<td>' . htmlspecialchars($item['nama_rekening_belanja']) . '</td>';
                echo '<td>' . number_format($paguAnggaran, 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_januari'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_februari'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_maret'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_april'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_mei'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_juni'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_juli'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_agustus'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_september'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_oktober'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_november'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($item['realisasi_desember'], 0, ',', '.') . '</td>';
                echo '<td>' . number_format($semester1, 0, ',', '.') . '</td>';
                echo '<td>' . number_format($semester2, 0, ',', '.') . '</td>';
                echo '<td>' . number_format($totalRealisasi, 0, ',', '.') . '</td>';
                echo '<td>' . number_format($sisaAnggaran, 0, ',', '.') . '</td>';
                echo '<td>' . number_format($persentase, 2) . '%</td>';
                echo '</tr>';
            }
            echo '</table>';
            break;
            
        default:
            echo 'Invalid export type';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>