<nav class="sidebar">
    <div class="sidebar-menu">
        <a href="index.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            Dashboard
        </a>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="master-data.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'master-data.php' ? 'active' : ''; ?>">
            <i class="fas fa-database"></i>
            Master Data
        </a>
        
        <a href="verifikasi-spj.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'verifikasi-spj.php' ? 'active' : ''; ?>">
            <i class="fas fa-check-square"></i>
            Verifikasi SPJ
        </a>
         <a href="kendali-verifikasi.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'kendali-verifikasi.php' ? 'active' : ''; ?>">
            <i class="fa fa-print"></i>
            Cetak Kendali Verifikasi
        </a>
        <?php endif; ?>
        
        <div class="menu-section">
            <h4 class="menu-section-title">Hasil Verifikasi</h4>
            <a href="hasil-verifikasi.php?bidang=sekretariat" class="menu-item <?php echo isset($_GET['bidang']) && $_GET['bidang'] == 'sekretariat' ? 'active' : ''; ?>">
                <i class="fas fa-building"></i>
                Sekretariat
            </a>
            <a href="hasil-verifikasi.php?bidang=pkpa" class="menu-item <?php echo isset($_GET['bidang']) && $_GET['bidang'] == 'pkpa' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                Bidang PKPA
            </a>
            <a href="hasil-verifikasi.php?bidang=ppik" class="menu-item <?php echo isset($_GET['bidang']) && $_GET['bidang'] == 'ppik' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                Bidang PPIK
            </a>
            <a href="hasil-verifikasi.php?bidang=diklat" class="menu-item <?php echo isset($_GET['bidang']) && $_GET['bidang'] == 'diklat' ? 'active' : ''; ?>">
                <i class="fas fa-graduation-cap"></i>
                Bidang DIKLAT
            </a>
            <a href="hasil-verifikasi.php?bidang=mpasn" class="menu-item <?php echo isset($_GET['bidang']) && $_GET['bidang'] == 'mpasn' ? 'active' : ''; ?>">
                <i class="fas fa-award"></i>
                Bidang MPASN
            </a>
        </div>

        <a href="realisasi-anggaran.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'realisasi-anggaran.php' ? 'active' : ''; ?>">
            <i class="fas fa-money-bill-wave"></i>
            Realisasi Anggaran
        </a>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="setting-notifikasi.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'setting-notifikasi.php' ? 'active' : ''; ?>">
            <i class="fas fa-envelope-open-text"></i>
            Setting Notifikasi
        </a>
        
        <a href="users.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-user-cog"></i>
            Manajemen User
        </a>
        
        <a href="settings.php" class="menu-item <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i>
            Pengaturan
        </a>
        <?php endif; ?>
    </div>
</nav>

<style>
.menu-section {
    margin: 1rem 0;
}

.menu-section-title {
    padding: 8px 24px;
    color: var(--gray-500);
    font-size: 0.75rem;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
    border-bottom: 1px solid var(--gray-200);
    margin-bottom: 0.5rem;
}

.menu-section .menu-item {
    padding-left: 40px;
    font-size: 0.875rem;
}
</style>