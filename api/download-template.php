<?php
$type = $_GET['type'] ?? '';

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="template_' . $type . '.xls"');
header('Cache-Control: max-age=0');

switch ($type) {
    case 'master-data':
        echo '<table border="1">';
        echo '<tr>';
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
        echo '<tr>';
        echo '<td>Sekretariat</td>';
        echo '<td>Nama KPA</td>';
        echo '<td>Nama Bendahara</td>';
        echo '<td>bendahara@email.com</td>';
        echo '<td>Nama PPTK</td>';
        echo '<td>pptk@email.com</td>';
        echo '<td>Kegiatan Contoh</td>';
        echo '<td>Sub Kegiatan Contoh</td>';
        echo '<td>Rekening Belanja Contoh</td>';
        echo '</tr>';
        echo '</table>';
        break;
        
    case 'verifikasi-spj':
        echo '<table border="1">';
        echo '<tr>';
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
        echo '<tr>';
        echo '<td>Sekretariat</td>';
        echo '<td>2024</td>';
        echo '<td>Januari</td>';
        echo '<td>Sub Kegiatan Contoh</td>';
        echo '<td>Rekening Belanja Contoh</td>';
        echo '<td></td>';
        echo '<td>BKU001</td>';
        echo '<td>Keterangan transaksi contoh</td>';
        echo '<td>Lengkap</td>';
        echo '<td>2024-01-15</td>';
        echo '</tr>';
        echo '</table>';
        break;
        
    case 'realisasi-anggaran':
        echo '<table border="1">';
        echo '<tr>';
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
        echo '</tr>';
        echo '<tr>';
        echo '<td>Sekretariat</td>';
        echo '<td>Kegiatan Contoh</td>';
        echo '<td>Sub Kegiatan Contoh</td>';
        echo '<td>Rekening Belanja Contoh</td>';
        echo '<td>100000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '<td>5000000</td>';
        echo '</tr>';
        echo '</table>';
        break;
        
    default:
        echo 'Invalid template type';
}
?>