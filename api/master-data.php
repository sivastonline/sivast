<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
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
            $sql = "SELECT * FROM master_data ORDER BY bidang, sub_kegiatan";
            $data = $db->fetchAll($sql);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $sql = "SELECT * FROM master_data WHERE id = ?";
            $data = $db->fetch($sql, [$id]);
            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Data not found']);
            }
            break;
            
        case 'create':
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            $required = ['bidang', 'kuasa_pengguna_anggaran', 'nama_bendahara', 'email_bendahara', 'wa_bendahara',
                        'nama_pptk', 'email_pptk', 'wa_pptk','kegiatan', 'sub_kegiatan', 'nama_rekening_belanja'];
            
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    exit();
                }
            }
            
            // Validate email
            if (!filter_var($input['email_bendahara'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email bendahara format']);
                exit();
            }
            
            if (!filter_var($input['email_pptk'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email PPTK format']);
                exit();
            }
            
            $sql = "INSERT INTO master_data (bidang, kuasa_pengguna_anggaran, nama_bendahara, email_bendahara, wa_bendahara,
                    nama_pptk, email_pptk, wa_pptk, kegiatan, sub_kegiatan, nama_rekening_belanja) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $params = [
                $input['bidang'],
                $input['kuasa_pengguna_anggaran'],
                $input['nama_bendahara'],
                $input['email_bendahara'],
                $input['wa_bendahara'],
                $input['nama_pptk'],
                $input['email_pptk'],
                $input['wa_pptk'],
                $input['kegiatan'],
                $input['sub_kegiatan'],
                $input['nama_rekening_belanja']
            ];
            
            $db->query($sql, $params);
            echo json_encode(['success' => true, 'message' => 'Data created successfully']);
            break;
            
        case 'update':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? 0;
            
            // Validation
            $required = ['bidang', 'kuasa_pengguna_anggaran', 'nama_bendahara', 'email_bendahara', 'wa_bendahara',
                        'nama_pptk', 'email_pptk', 'wa_pptk', 'kegiatan', 'sub_kegiatan', 'nama_rekening_belanja'];
            
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    exit();
                }
            }
            
            // Validate email
            if (!filter_var($input['email_bendahara'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email bendahara format']);
                exit();
            }
            
            if (!filter_var($input['email_pptk'], FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Invalid email PPTK format']);
                exit();
            }
            
            $sql = "UPDATE master_data SET bidang = ?, kuasa_pengguna_anggaran = ?, nama_bendahara = ?, 
                    email_bendahara = ?,  wa_bendahara = ?, nama_pptk = ?, email_pptk = ?,  wa_pptk = ?, kegiatan = ?, sub_kegiatan = ?, 
                    nama_rekening_belanja = ? WHERE id = ?";
            
            $params = [
                $input['bidang'],
                $input['kuasa_pengguna_anggaran'],
                $input['nama_bendahara'],
                $input['email_bendahara'],
                $input['wa_bendahara'],
                $input['nama_pptk'],
                $input['email_pptk'],
                $input['wa_pptk'],
                $input['kegiatan'],
                $input['sub_kegiatan'],
                $input['nama_rekening_belanja'],
                $id
            ];
            
            $db->query($sql, $params);
            echo json_encode(['success' => true, 'message' => 'Data updated successfully']);
            break;
            
        case 'delete':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? 0;
            
            $sql = "DELETE FROM master_data WHERE id = ?";
            $db->query($sql, [$id]);
            echo json_encode(['success' => true, 'message' => 'Data deleted successfully']);
            break;
            
        case 'get_sub_kegiatan':
            $bidang = $_GET['bidang'] ?? '';
            $sql = "SELECT DISTINCT sub_kegiatan FROM master_data WHERE bidang = ? ORDER BY sub_kegiatan";
            $data = $db->fetchAll($sql, [$bidang]);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'get_rekening_belanja':
            $bidang = $_GET['bidang'] ?? '';
            $subKegiatan = $_GET['sub_kegiatan'] ?? '';
            $sql = "SELECT DISTINCT nama_rekening_belanja FROM master_data WHERE bidang = ? AND sub_kegiatan = ? ORDER BY nama_rekening_belanja";
            $data = $db->fetchAll($sql, [$bidang, $subKegiatan]);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>