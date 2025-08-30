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
            $sql = "SELECT id, username, nama_user, role, created_at, updated_at FROM users ORDER BY created_at DESC";
            $data = $db->fetchAll($sql);
            echo json_encode(['success' => true, 'data' => $data]);
            break;
            
        case 'get':
            $id = $_GET['id'] ?? 0;
            $sql = "SELECT id, username, nama_user, role, created_at, updated_at FROM users WHERE id = ?";
            $data = $db->fetch($sql, [$id]);
            if ($data) {
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                echo json_encode(['success' => false, 'message' => 'User not found']);
            }
            break;
            
        case 'create':
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validation
            $required = ['username', 'nama_user', 'role', 'password'];
            
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    exit();
                }
            }
            
            // Check if username already exists
            $checkSql = "SELECT id FROM users WHERE username = ?";
            $existing = $db->fetch($checkSql, [$input['username']]);
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Username already exists']);
                exit();
            }
            
            // Validate role
            if (!in_array($input['role'], ['admin', 'user'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid role']);
                exit();
            }
            
            // Hash password
            $hashedPassword = password_hash($input['password'], PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, password, nama_user, role) VALUES (?, ?, ?, ?)";
            $params = [
                $input['username'],
                $hashedPassword,
                $input['nama_user'],
                $input['role']
            ];
            
            $db->query($sql, $params);
            echo json_encode(['success' => true, 'message' => 'User created successfully']);
            break;
            
        case 'update':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? 0;
            
            // Validation
            $required = ['username', 'nama_user', 'role'];
            
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field $field is required"]);
                    exit();
                }
            }
            
            // Check if username already exists (excluding current user)
            $checkSql = "SELECT id FROM users WHERE username = ? AND id != ?";
            $existing = $db->fetch($checkSql, [$input['username'], $id]);
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Username already exists']);
                exit();
            }
            
            // Validate role
            if (!in_array($input['role'], ['admin', 'user'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid role']);
                exit();
            }
            
            $sql = "UPDATE users SET username = ?, nama_user = ?, role = ? WHERE id = ?";
            $params = [
                $input['username'],
                $input['nama_user'],
                $input['role'],
                $id
            ];
            
            $db->query($sql, $params);
            echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            break;
            
        case 'change_password':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? 0;
            
            if (empty($input['new_password'])) {
                echo json_encode(['success' => false, 'message' => 'New password is required']);
                exit();
            }
            
            // Hash new password
            $hashedPassword = password_hash($input['new_password'], PASSWORD_DEFAULT);
            
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $db->query($sql, [$hashedPassword, $id]);
            
            echo json_encode(['success' => true, 'message' => 'Password changed successfully']);
            break;
            
        case 'delete':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $input['id'] ?? 0;
            
            // Check if trying to delete admin user
            $userSql = "SELECT username FROM users WHERE id = ?";
            $user = $db->fetch($userSql, [$id]);
            
            if ($user && $user['username'] === 'admin') {
                echo json_encode(['success' => false, 'message' => 'Cannot delete admin user']);
                exit();
            }
            
            $sql = "DELETE FROM users WHERE id = ?";
            $db->query($sql, [$id]);
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>