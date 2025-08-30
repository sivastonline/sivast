<?php
require_once '../config/database.php';
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_FILES['file']) || !isset($_POST['type'])) {
    echo json_encode(['success' => false, 'message' => 'File or type not provided']);
    exit();
}

$type = $_POST['type'];
$fileTmpPath = $_FILES['file']['tmp_name'];

try {
    $spreadsheet = IOFactory::load($fileTmpPath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    $imported = 0;
    $errors = [];

    for ($i = 1; $i < count($rows); $i++) { // skip header
        $data = array_map(fn($v) => mb_convert_encoding(trim((string)$v), 'UTF-8', 'auto'), $rows[$i]);
        try {
            switch ($type) {
                case 'master-data':
                    if (count($data) < 9) continue 2;
                    $sql = "INSERT INTO master_data 
                        (bidang, kuasa_pengguna_anggaran, nama_bendahara, email_bendahara,
                         nama_pptk, email_pptk, kegiatan, sub_kegiatan, nama_rekening_belanja)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $params = array_slice($data, 0, 9);
                    break;

                case 'verifikasi-spj':
                    if (count($data) < 10) continue 2;
                    $sql = "INSERT INTO verifikasi_spj 
                        (bidang, tahun, bulan, sub_kegiatan, nama_rekening_belanja, alasan_tidak_lengkap,
                         nomor_bku, keterangan_transaksi, status_verifikasi, tanggal_verifikasi)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $params = array_slice($data, 0, 10);
                    break;

                case 'realisasi-anggaran':
                    if (count($data) < 18) continue 2;
                    $existing = $db->fetch("SELECT id FROM realisasi_anggaran 
                        WHERE bidang = ? AND sub_kegiatan = ? AND nama_rekening_belanja = ?", [
                        $data[0], $data[2], $data[3]
                    ]);
                    if ($existing) {
                        $sql = "UPDATE realisasi_anggaran SET pagu_anggaran = ?,
                            realisasi_januari = ?, realisasi_februari = ?, realisasi_maret = ?,
                            realisasi_april = ?, realisasi_mei = ?, realisasi_juni = ?,
                            realisasi_juli = ?, realisasi_agustus = ?, realisasi_september = ?,
                            realisasi_oktober = ?, realisasi_november = ?, realisasi_desember = ?
                            WHERE id = ?";
                        $params = array_map('floatval', array_slice($data, 5, 13));
                        $params[] = $existing['id'];
                    } else {
                        continue 2;
                    }
                    break;

                default:
                    throw new Exception("Unknown import type: $type");
            }

            $db->query($sql, $params);
            $imported++;
        } catch (Exception $e) {
            $errors[] = "Row " . ($i+1) . ": " . $e->getMessage();
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Import selesai. $imported baris berhasil." . (!empty($errors) ? " Beberapa gagal: " . implode(', ', array_slice($errors, 0, 5)) : "")
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengimpor: ' . $e->getMessage()]);
}
