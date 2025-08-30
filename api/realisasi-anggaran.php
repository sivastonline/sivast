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
            $sql = "SELECT * FROM realisasi_anggaran ORDER BY bidang, sub_kegiatan";
            $data = $db->fetchAll($sql);

            // Ambil waktu update terakhir
            $sql_last_update = "SELECT MAX(updated_at) AS last_update FROM realisasi_anggaran";
            $lastUpdate = $db->fetch($sql_last_update);

            // Kirim data beserta waktu update terakhir
            echo json_encode([
                'success' => true, 
                'data' => $data,
                'last_update' => $lastUpdate['last_update'] ?? 'Tidak ada data update'
            ]);
    break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $sql = "SELECT * FROM realisasi_anggaran WHERE id = ?";
            $data = $db->fetch($sql, [$id]);
            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Data not found']);
            }
            break;
            
        case 'update':
            if ($_SESSION['role'] !== 'admin') {
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                exit();
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? 0;
            
            $sql = "UPDATE realisasi_anggaran SET 
                    pagu_anggaran = ?, 
                    realisasi_januari = ?, realisasi_februari = ?, realisasi_maret = ?, 
                    realisasi_april = ?, realisasi_mei = ?, realisasi_juni = ?,
                    realisasi_juli = ?, realisasi_agustus = ?, realisasi_september = ?,
                    realisasi_oktober = ?, realisasi_november = ?, realisasi_desember = ?
                    WHERE id = ?";
            
            $params = [
                $input['pagu_anggaran'] ?? 0,
                $input['realisasi_januari'] ?? 0,
                $input['realisasi_februari'] ?? 0,
                $input['realisasi_maret'] ?? 0,
                $input['realisasi_april'] ?? 0,
                $input['realisasi_mei'] ?? 0,
                $input['realisasi_juni'] ?? 0,
                $input['realisasi_juli'] ?? 0,
                $input['realisasi_agustus'] ?? 0,
                $input['realisasi_september'] ?? 0,
                $input['realisasi_oktober'] ?? 0,
                $input['realisasi_november'] ?? 0,
                $input['realisasi_desember'] ?? 0,
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
            
            $sql = "DELETE FROM realisasi_anggaran WHERE id = ?";
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