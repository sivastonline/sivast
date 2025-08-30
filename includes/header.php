<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="header">
    <div class="header-left">
        <button class="menu-toggle" onclick="toggleSidebar()" style="display: none;">
            <i class="fas fa-bars"></i>
        </button>
        <button class="menu-toggle" onclick="toggleSidebar()" style="display: none;">
            <i class="fas fa-bars"></i>
        </button>
        <img src="assets/images/logo-sivast.png" alt="BKPSDM Logo" class="header-logo">
        
    </div>
    
    <div class="header-right">
        <div class="user-menu">
            <div class="user-info" onclick="toggleUserMenu()">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <div class="user-details">
                    <span class="user-name"><?php echo $_SESSION['username']; ?></span>
                    <small class="user-role"><?php echo ucfirst($_SESSION['role']); ?></small>
                </div>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="user-dropdown" id="userDropdown" style="display: none;">
                <a href="profile.php" class="dropdown-item">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="settings.php" class="dropdown-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <div class="user-details">
                    <span class="user-name"><?php echo $_SESSION['username']; ?></span>
                    <small class="user-role"><?php echo ucfirst($_SESSION['role']); ?></small>
                </div>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="user-dropdown" id="userDropdown" style="display: none;">
                <a href="profile.php" class="dropdown-item">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="settings.php" class="dropdown-item">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>
</header>

<script>
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('open');
}

// Close user menu when clicking outside
document.addEventListener('click', function(e) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('userDropdown');
    
    if (!userMenu.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>

<style>
.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--white-color);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    min-width: 200px;
    z-index: 1000;
    border: 1px solid var(--gray-200);
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    color: var(--gray-700);
    text-decoration: none;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: var(--gray-100);
    color: var(--primary-color);
}

.dropdown-divider {
    height: 1px;
    background: var(--gray-200);
    margin: 4px 0;
}

.user-details {
    display: flex;
    flex-direction: column;
    text-align: left;
}

.user-name {
    font-weight: 500;
    color: var(--gray-900);
}

.user-role {
    color: var(--gray-500);
    font-size: 0.75rem;
}

@media (max-width: 1024px) {
    .menu-toggle {
        display: block !important;
    }
}
</style>

<script>
function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('open');
}

// Close user menu when clicking outside
document.addEventListener('click', function(e) {
    const userMenu = document.querySelector('.user-menu');
    const dropdown = document.getElementById('userDropdown');
    
    if (!userMenu.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});
</script>

<style>
.user-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--white-color);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    min-width: 200px;
    z-index: 1000;
    border: 1px solid var(--gray-200);
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    color: var(--gray-700);
    text-decoration: none;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: var(--gray-100);
    color: var(--primary-color);
}

.dropdown-divider {
    height: 1px;
    background: var(--gray-200);
    margin: 4px 0;
}

.user-details {
    display: flex;
    flex-direction: column;
    text-align: left;
}

.user-name {
    font-weight: 500;
    color: var(--gray-900);
}

.user-role {
    color: var(--gray-500);
    font-size: 0.75rem;
}

@media (max-width: 1024px) {
    .menu-toggle {
        display: block !important;
    }
}
</style>