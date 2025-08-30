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
    <title>Setting Notifikasi - SIVAST</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/sidebar.php'; ?>
    
    <main class="main-content">
        <div class="content-header">
            <h1><i class="fas fa-envelope-open-text"></i> Setting Notifikasi Email</h1>
            <p>Konfigurasi pengaturan email dan template notifikasi</p>
        </div>

        <div class="settings-container">
            <div class="settings-tabs">
                <button class="tab-btn active" onclick="showTab('smtp')">
                    <i class="fas fa-server"></i> Pengaturan SMTP
                </button>
                <button class="tab-btn" onclick="showTab('template')">
                    <i class="fas fa-file-alt"></i> Template Email
                </button>
                <button class="tab-btn" onclick="showTab('test')">
                    <i class="fas fa-paper-plane"></i> Test Email
                </button>
            </div>

            <!-- SMTP Settings Tab -->
            <div class="tab-content active" id="smtp-tab">
                <div class="settings-card">
                    <div class="card-header">
                        <h3><i class="fas fa-cog"></i> Konfigurasi SMTP Gmail</h3>
                        <p>Atur pengaturan SMTP untuk mengirim notifikasi email</p>
                    </div>
                    <form id="smtpForm">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="smtp_host">SMTP Host</label>
                                <input type="text" id="smtp_host" name="smtp_host" >
                            </div>
                            <div class="form-group">
                                <label for="smtp_port">SMTP Port</label>
                                <input type="number" id="smtp_port" name="smtp_port">
                            </div>
                            <div class="form-group">
                                <label for="smtp_username">SMTP Username<span class="required">*</span></label>
                                <input type="text" id="smtp_username" name="smtp_username" required>
                            </div>
                            <div class="form-group">
                                <label for="smtp_password">SMTP Key <span class="required">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="smtp_password" name="smtp_password" required>
                                    <button type="button" class="toggle-password" onclick="togglePassword('smtp_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="help-text">Gunakan App Password, bukan password Gmail biasa</small>
                            </div>
                            <div class="form-group">
                                <label for="from_email">Email Pengirim <span class="required">*</span></label>
                                <input type="email" id="from_email" name="from_email" required>
                            </div>
                            <div class="form-group">
                                <label for="from_name">Nama Pengirim</label>
                                <input type="text" id="from_name" name="from_name" value="SIVAST - BKPSDM Kabupaten Bandung">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Template Email Tab -->
            <div class="tab-content" id="template-tab">
                <div class="settings-card">
                    <div class="card-header">
                        <h3><i class="fas fa-edit"></i> Template Notifikasi Email</h3>
                        <p>Customize template email yang akan dikirim ke PPTK dan Bendahara</p>
                    </div>
                    <form id="templateForm">
                        <div class="form-group">
                            <label for="template_subject">Subject Email <span class="required">*</span></label>
                            <input type="text" id="template_subject" name="template_subject" required>
                        </div>
                        <div class="form-group">
                            <label for="template_body">Isi Email <span class="required">*</span></label>
                            <textarea id="template_body" name="template_body" rows="15" required></textarea>
                        </div>
                        <div class="template-variables">
                            <h4>Variabel yang Tersedia:</h4>
                            <div class="variables-grid">
                                <span class="variable">{bidang}</span>
                                <span class="variable">{nama_pptk}</span>
                                <span class="variable">{nama_bendahara}</span>
                                <span class="variable">{sub_kegiatan}</span>
                                <span class="variable">{nama_rekening_belanja}</span>
                                <span class="variable">{keterangan_transaksi}</span>
                                <span class="variable">{nomor_bku}</span>
                                <span class="variable">{bulan}</span>
                                <span class="variable">{tahun}</span>
                                <span class="variable">{status_verifikasi}</span>
                                <span class="variable">{tanggal_verifikasi}</span>
                                <span class="variable">{alasan_tidak_lengkap}</span>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="resetTemplate()">
                                <i class="fas fa-undo"></i> Reset ke Default
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Test Email Tab -->
            <div class="tab-content" id="test-tab">
                <div class="settings-card">
                    <div class="card-header">
                        <h3><i class="fas fa-flask"></i> Test Pengiriman Email</h3>
                        <p>Test konfigurasi email dengan mengirim email percobaan</p>
                    </div>
                    <form id="testForm">
                        <div class="form-group">
                            <label for="test_email">Email Tujuan Test <span class="required">*</span></label>
                            <input type="email" id="test_email" name="test_email" required>
                        </div>
                        <div class="form-group">
                            <label for="test_subject">Subject Test</label>
                            <input type="text" id="test_subject" name="test_subject" value="Test Email SIVAST">
                        </div>
                        <div class="form-group">
                            <label for="test_message">Pesan Test</label>
                            <textarea id="test_message" name="test_message" rows="5">Ini adalah email test dari sistem SIVAST. Jika Anda menerima email ini, berarti konfigurasi email sudah benar.</textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-info">
                                <i class="fas fa-paper-plane"></i> Kirim Test Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script src="assets/js/setting-notifikasi.js"></script>
</body>
</html>