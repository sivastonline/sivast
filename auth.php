<?php
require_once 'config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        header('Location: login.php?error=empty_fields');
        exit();
    }
    
    try {
        $sql = "SELECT id, username, password, nama_user, role FROM users WHERE username = ?";
        $user = $db->fetch($sql, [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_user'] = $user['nama_user'];
            $_SESSION['role'] = $user['role'];
            
            // Update last login
            $updateSql = "UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $db->query($updateSql, [$user['id']]);
            
            header('Location: index.php');
            exit();
        } else {
            header('Location: login.php?error=invalid_credentials');
            exit();
        }
    } catch (Exception $e) {
        error_log('Login error: ' . $e->getMessage());
        header('Location: login.php?error=system_error');
        exit();
    }
} else {
    header('Location: login.php');
    exit();
}
?>