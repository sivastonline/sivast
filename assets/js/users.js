// Users Management JavaScript
let currentEditId = null;
let currentPasswordId = null;

document.addEventListener('DOMContentLoaded', function() {
    loadUsersData();
    setupEventListeners();
});

function setupEventListeners() {
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterTable();
    });
    
    // Form submissions
    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveUserData();
    });
    
    document.getElementById('passwordForm').addEventListener('submit', function(e) {
        e.preventDefault();
        changePassword();
    });
}

function loadUsersData() {
    fetch('api/users.php?action=list')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateTable(data.data);
            } else {
                showAlert('Error loading data: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading data', 'danger');
        });
}

function populateTable(data) {
    const tbody = document.getElementById('usersBody');
    tbody.innerHTML = '';
    
    data.forEach((item, index) => {
        const row = document.createElement('tr');
        const roleClass = item.role === 'admin' ? 'status-lengkap' : 'status-belum-lengkap';
        
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.username}</td>
            <td>${item.nama_user}</td>
            <td><span class="status-badge ${roleClass}">${item.role.toUpperCase()}</span></td>
            <td>${formatDate(item.created_at)}</td>
            <td>${formatDate(item.updated_at)}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="viewUser(${item.id})" title="View">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-warning btn-sm" onclick="editUser(${item.id})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-secondary btn-sm" onclick="changeUserPassword(${item.id})" title="Change Password">
                    <i class="fas fa-key"></i>
                </button>
                ${item.username !== 'admin' ? `
                <button class="btn btn-danger btn-sm" onclick="deleteUser(${item.id})" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
                ` : ''}
            </td>
        `;
        tbody.appendChild(row);
    });
}

function addUser() {
    currentEditId = null;
    document.getElementById('modalTitle').textContent = 'Tambah User Baru';
    document.getElementById('userForm').reset();
    showModal();
}

function editUser(id) {
    currentEditId = id;
    document.getElementById('modalTitle').textContent = 'Edit User';
    
    fetch(`api/users.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                document.getElementById('username').value = item.username;
                document.getElementById('nama_user').value = item.nama_user;
                document.getElementById('role').value = item.role;
                
                // Hide password fields for edit
                document.getElementById('password').required = false;
                document.getElementById('confirm_password').required = false;
                document.getElementById('password').parentElement.parentElement.style.display = 'none';
                document.getElementById('confirm_password').parentElement.parentElement.style.display = 'none';
                
                showModal();
            } else {
                showAlert('Error loading data: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading data', 'danger');
        });
}

function viewUser(id) {
    fetch(`api/users.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                const roleClass = item.role === 'admin' ? 'status-lengkap' : 'status-belum-lengkap';
                
                const content = `
                    <div class="view-data">
                        <div class="row">
                            <div class="col"><strong>Username:</strong></div>
                            <div class="col">${item.username}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Nama User:</strong></div>
                            <div class="col">${item.nama_user}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Role:</strong></div>
                            <div class="col"><span class="status-badge ${roleClass}">${item.role.toUpperCase()}</span></div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Tanggal Dibuat:</strong></div>
                            <div class="col">${formatDateTime(item.created_at)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Terakhir Update:</strong></div>
                            <div class="col">${formatDateTime(item.updated_at)}</div>
                        </div>
                    </div>
                `;
                showInfoModal('Detail User', content);
            } else {
                showAlert('Error loading data: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading data', 'danger');
        });
}

function deleteUser(id) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        fetch('api/users.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('User berhasil dihapus', 'success');
                loadUsersData();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error deleting user', 'danger');
        });
    }
}

function changeUserPassword(id) {
    currentPasswordId = id;
    document.getElementById('passwordForm').reset();
    showPasswordModal();
}

function saveUserData() {
    const formData = new FormData(document.getElementById('userForm'));
    const data = Object.fromEntries(formData);
    
    // Validate passwords if creating new user
    if (!currentEditId) {
        if (data.password !== data.confirm_password) {
            showAlert('Password dan konfirmasi password tidak sama', 'danger');
            return;
        }
        
        if (data.password.length < 6) {
            showAlert('Password minimal 6 karakter', 'danger');
            return;
        }
    }
    
    data.action = currentEditId ? 'update' : 'create';
    if (currentEditId) {
        data.id = currentEditId;
    }
    
    fetch('api/users.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(currentEditId ? 'User berhasil diupdate' : 'User berhasil ditambahkan', 'success');
            closeModal();
            loadUsersData();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving user', 'danger');
    });
}

function changePassword() {
    const formData = new FormData(document.getElementById('passwordForm'));
    const data = Object.fromEntries(formData);
    
    if (data.new_password !== data.confirm_new_password) {
        showAlert('Password baru dan konfirmasi password tidak sama', 'danger');
        return;
    }
    
    if (data.new_password.length < 6) {
        showAlert('Password minimal 6 karakter', 'danger');
        return;
    }
    
    data.action = 'change_password';
    data.id = currentPasswordId;
    
    fetch('api/users.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Password berhasil diubah', 'success');
            closePasswordModal();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error changing password', 'danger');
    });
}

function toggleFilter() {
    const filterPanel = document.getElementById('filterPanel');
    filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
}

function applyFilter() {
    filterTable();
}

function resetFilter() {
    document.getElementById('filterRole').value = '';
    document.getElementById('searchInput').value = '';
    filterTable();
}

function filterTable() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const roleFilter = document.getElementById('filterRole').value;
    
    const rows = document.querySelectorAll('#usersBody tr');
    
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const role = cells[3].textContent.toLowerCase().trim();
        
        const matchesSearch = searchValue === '' || 
            Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchValue));
        const matchesRole = roleFilter === '' || role === roleFilter;
        
        if (matchesSearch && matchesRole) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function showModal() {
    // Reset password field visibility
    document.getElementById('password').required = true;
    document.getElementById('confirm_password').required = true;
    document.getElementById('password').parentElement.parentElement.style.display = 'block';
    document.getElementById('confirm_password').parentElement.parentElement.style.display = 'block';
    
    document.getElementById('userModal').classList.add('show');
}

function closeModal() {
    document.getElementById('userModal').classList.remove('show');
}

function showPasswordModal() {
    document.getElementById('passwordModal').classList.add('show');
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.remove('show');
}

function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    return date.toLocaleString('id-ID');
}

function showAlert(message, type) {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <span>${message}</span>
        <button type="button" class="close" onclick="this.parentElement.remove()">×</button>
    `;
    
    // Add styles for alert
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        min-width: 300px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    // Set background color based on type
    if (type === 'danger') {
        alert.style.backgroundColor = '#ef4444';
    } else if (type === 'warning') {
        alert.style.backgroundColor = '#f59e0b';
    } else if (type === 'success') {
        alert.style.backgroundColor = '#10b981';
    } else {
        alert.style.backgroundColor = '#3b82f6';
    }
    
    // Style close button
    const closeBtn = alert.querySelector('.close');
    closeBtn.style.cssText = `
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        padding: 0;
        margin-left: 10px;
    `;
    
    // Add to page
    document.body.appendChild(alert);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 5000);
}

function showInfoModal(title, content) {
    // Create and show info modal
    const modal = document.createElement('div');
    modal.className = 'modal show';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h2>${title}</h2>
                <button class="close-btn" onclick="this.closest('.modal').remove()">&times;</button>
            </div>
            <div class="modal-body">
                ${content}
            </div>
            <div class="form-actions">
                <button class="btn btn-secondary" onclick="this.closest('.modal').remove()">Tutup</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}