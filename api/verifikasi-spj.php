<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
// Cek jika action belum ditemukan, coba ambil dari body JSON
if (empty($action)) {
    $rawInput = json_decode(file_get_contents('php://input'), true);
    if (isset($rawInput['action'])) {
        $action = $rawInput['action'];
        $_POST = $rawInput; // Supaya $_POST bisa digunakan juga
    }
}

try {
    switch ($action) {
        case 'list':
            $bidang = $_GET['bidang'] ?? '';
            $sql = "SELECT * FROM verifikasi_spj";
            $params = [];
            
            if ($bidang) {
                $sql .= " WHERE bidang = ?";
                $params[] = $bidang;
            }
            
            $sql .= " ORDER BY tanggal_verifikasi DESC, bidang, tahun, bulan";
            $data = $db->fetchAll($sql, $params);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $sql = "SELECT * FROM verifikasi_spj WHERE id = ?";
            $data = $db->fetch($sql, [$id]);
            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Data not found']);
            }
            break;
            
        case 'create':
            if ($_SESSION['role'] !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            $required = ['bidang', 'tahun', 'bulan', 'sub_kegiatan', 'nama_rekening_belanja', 
                        'nomor_bku', 'keterangan_transaksi', 'status_verifikasi', 'tanggal_verifikasi'];
            
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    exit();
                }
            }
            
            // Check if alasan_tidak_lengkap is required
            if ($input['status_verifikasi'] === 'Belum Lengkap' && empty($input['alasan_tidak_lengkap'])) {
                echo json_encode(['success' => false, 'message' => 'Alasan tidak lengkap harus diisi']);
                exit();
            }
            
            $sql = "INSERT INTO verifikasi_spj (bidang, tahun, bulan, sub_kegiatan, nama_rekening_belanja, 
                    alasan_tidak_lengkap, nomor_bku, keterangan_transaksi, status_verifikasi, tanggal_verifikasi) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $input['bidang'],
                $input['tahun'],
                $input['bulan'],
                $input['sub_kegiatan'],
                $input['nama_rekening_belanja'],
                $input['alasan_tidak_lengkap'] ?? null,
                $input['nomor_bku'],
                $input['keterangan_transaksi'],
                $input['status_verifikasi'],
                $input['tanggal_verifikasi']
            ];
            
            $db->query($sql, $params);
            echo json_encode(['success' => true, 'message' => 'Data created successfully']);
            break;
            
        case 'update':
            if ($_SESSION['role'] !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? 0;
            
            // Validation
            $required = ['bidang', 'tahun', 'bulan', 'sub_kegiatan', 'nama_rekening_belanja', 
                        'nomor_bku', 'keterangan_transaksi', 'status_verifikasi', 'tanggal_verifikasi'];
            
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    exit();
                }
            }
            
            // Check if alasan_tidak_lengkap is required
            if ($input['status_verifikasi'] === 'Belum Lengkap' && empty($input['alasan_tidak_lengkap'])) {
                echo json_encode(['success' => false, 'message' => 'Alasan tidak lengkap harus diisi']);
                exit();
            }
            
            $sql = "UPDATE verifikasi_spj SET bidang = ?, tahun = ?, bulan = ?, sub_kegiatan = ?, 
                    nama_rekening_belanja = ?, alasan_tidak_lengkap = ?, nomor_bku = ?, keterangan_transaksi = ?, 
                    status_verifikasi = ?, tanggal_verifikasi = ? WHERE id = ?";
            
            $params = [
                $input['bidang'],
                $input['tahun'],
                $input['bulan'],
                $input['sub_kegiatan'],
                $input['nama_rekening_belanja'],
                $input['alasan_tidak_lengkap'] ?? null,
                $input['nomor_bku'],
                $input['keterangan_transaksi'],
                $input['status_verifikasi'],
                $input['tanggal_verifikasi'],
                $id
            ];
            
            $db->query($sql, $params);
            echo json_encode(['success' => true, 'message' => 'Data updated successfully']);
            break;
            
        case 'delete':
            if ($_SESSION['role'] !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? 0;
            
            $sql = "DELETE FROM verifikasi_spj WHERE id = ?";
            $db->query($sql, [$id]);
            echo json_encode(['success' => true, 'message' => 'Data deleted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>