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
    <title>Settings - SIVAST</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-cog"></i> Pengaturan Sistem</h1>
            <p>Kelola pengaturan aplikasi SIVAST</p>
        </div>

        <div class="settings-container">
            <div class="settings-tabs">
                <button class="tab-btn active" onclick="showTab('general')">
                    <i class="fas fa-cog"></i> Umum
                </button>
                <button class="tab-btn" onclick="showTab('backup')">
                    <i class="fas fa-database"></i> Backup
                </button>
                <button class="tab-btn" onclick="showTab('logs')">
                    <i class="fas fa-file-alt"></i> Logs
                </button>
            </div>

            <!-- General Settings Tab -->
            <div class="tab-content active" id="general-tab">
                <div class="settings-card">
                    <div class="card-header">
                        <h3><i class="fas fa-info-circle"></i> Informasi Sistem</h3>
                        <p>Informasi tentang aplikasi SIVAST</p>
                    </div>
                    <div class="view-data">
                        <div class="row">
                            <div class="col"><strong>Nama Aplikasi:</strong></div>
                            <div class="col">SIVAST - Sistem Informasi Verifikasi SPJ Online</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Instansi:</strong></div>
                            <div class="col">BKPSDM Kabupaten Bandung</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Versi:</strong></div>
                            <div class="col">2.0</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Database:</strong></div>
                            <div class="col">MySQL</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>PHP Version:</strong></div>
                            <div class="col"><?php echo phpversion(); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Backup Tab -->
            <div class="tab-content" id="backup-tab">
                <div class="settings-card">
                    <div class="card-header">
                        <h3><i class="fas fa-download"></i> Backup Database</h3>
                        <p>Download backup database untuk keamanan data</p>
                    </div>
                    <div class="form-actions">
                        <button class="btn btn-success" onclick="backupDatabase()">
                            <i class="fas fa-download"></i> Download Backup
                        </button>
                    </div>
                </div>
            </div>

            <!-- Logs Tab -->
            <div class="tab-content" id="logs-tab">
                <div class="settings-card">
                    <div class="card-header">
                        <h3><i class="fas fa-list"></i> System Logs</h3>
                        <p>Log aktivitas sistem</p>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>User</th>
                                    <th>Aktivitas</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="logsBody">
                                <tr>
                                    <td><?php echo date('Y-m-d H:i:s'); ?></td>
                                    <td><?php echo $_SESSION['username']; ?></td>
                                    <td>Login ke sistem</td>
                                    <td><span class="status-badge status-lengkap">Success</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/settings.js"></script>
</body>
</html>