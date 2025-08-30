<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User - SIVAST</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-user-cog"></i> Manajemen User</h1>
            <p>Kelola akun pengguna sistem SIVAST</p>
        </div>

        <div class="content-actions">
            <div class="action-buttons">
                <button class="btn btn-primary" onclick="addUser()">
                    <i class="fas fa-user-plus"></i> Tambah User
                </button>
            </div>
            
            <div class="search-filter">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari user..." id="searchInput">
                </div>
                <button class="btn btn-outline" onclick="toggleFilter()">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>

        <div class="filter-panel" id="filterPanel" style="display: none;">
            <div class="filter-row">
                <select id="filterRole">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <button class="btn btn-primary" onclick="applyFilter()">Terapkan</button>
                <button class="btn btn-secondary" onclick="resetFilter()">Reset</button>
            </div>
        </div>

        <div class="table-container">
            <table class="data-table" id="usersTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama User</th>
                        <th>Role</th>
                        <th>Tanggal Dibuat</th>
                        <th>Terakhir Update</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="usersBody">
                    <!-- Data akan dimuat via AJAX -->
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal untuk form user -->
    <div class="modal" id="userModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Tambah User Baru</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form id="userForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_user">Nama User <span class="required">*</span></label>
                        <input type="text" id="nama_user" name="nama_user" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Role <span class="required">*</span></label>
                        <select id="role" name="role" required>
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="password" id="confirm_password" name="confirm_password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal untuk change password -->
    <div class="modal" id="passwordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ubah Password</h2>
                <button class="close-btn" onclick="closePasswordModal()">&times;</button>
            </div>
            <form id="passwordForm">
                <div class="form-grid">
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
                        <label for="confirm_new_password">Konfirmasi Password Baru <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="password" id="confirm_new_password" name="confirm_new_password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('confirm_new_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closePasswordModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Ubah Password</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/users.js"></script>
</body>
</html>