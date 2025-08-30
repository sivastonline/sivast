<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - SIVAST</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-user"></i> Profile User</h1>
            <p>Kelola informasi profile Anda</p>
        </div>

        <div class="settings-container">
            <div class="settings-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-circle"></i> Informasi Profile</h3>
                    <p>Update informasi profile dan password Anda</p>
                </div>
                
                <form id="profileForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo $_SESSION['username']; ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="nama_user">Nama User <span class="required">*</span></label>
                            <input type="text" id="nama_user" name="nama_user" value="<?php echo $_SESSION['nama_user']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <input type="text" id="role" name="role" value="<?php echo ucfirst($_SESSION['role']); ?>" readonly>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <div class="settings-card mt-4">
                <div class="card-header">
                    <h3><i class="fas fa-key"></i> Ubah Password</h3>
                    <p>Update password untuk keamanan akun Anda</p>
                </div>
                
                <form id="passwordForm">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="current_password">Password Saat Ini <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="password" id="current_password" name="current_password" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new_password">Password Baru <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="password" id="new_password" name="new_password" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password Baru <span class="required">*</span></label>
                            <div class="input-group">
                                <input type="password" id="confirm_password" name="confirm_password" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key"></i> Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="assets/js/profile.js"></script>
</body>
</html>