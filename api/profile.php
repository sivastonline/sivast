<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $action;

try {
    switch ($action) {
        case 'update_profile':
            $userId = $_SESSION['user_id'];
            $namaUser = $input['nama_user'] ?? '';
            
            if (empty($namaUser)) {
                echo json_encode(['success' => false, 'message' => 'Nama user harus diisi']);
                exit();
            }
            
            $sql = "UPDATE users SET nama_user = ? WHERE id = ?";
            $db->query($sql, [$namaUser, $userId]);
            
            // Update session
            $_SESSION['nama_user'] = $namaUser;
            
            echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
            break;
            
        case 'change_password':
            $userId = $_SESSION['user_id'];
            $currentPassword = $input['current_password'] ?? '';
            $newPassword = $input['new_password'] ?? '';
            
            if (empty($currentPassword) || empty($newPassword)) {
                echo json_encode(['success' => false, 'message' => 'Password tidak boleh kosong']);
                exit();
            }
            
            // Verify current password
            $sql = "SELECT password FROM users WHERE id = ?";
            $user = $db->fetch($sql, [$userId]);
            
            if (!$user || !password_verify($currentPassword, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Password saat ini salah']);
                exit();
            }
            
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateSql = "UPDATE users SET password = ? WHERE id = ?";
            $db->query($updateSql, [$hashedPassword, $userId]);
            
            echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>