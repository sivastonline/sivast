<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_data':
            $bidang = $_GET['bidang'] ?? '';
            $tahun = $_GET['tahun'] ?? '';
            $bulan = $_GET['bulan'] ?? '';
            
            if (empty($bidang) || empty($tahun) || empty($bulan)) {
                echo json_encode(['success' => false, 'message' => 'Bidang, tahun, dan bulan harus diisi']);
                exit();
            }
            
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
            
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>