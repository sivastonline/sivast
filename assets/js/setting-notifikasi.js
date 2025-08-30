// Setting Notifikasi JavaScript
document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
    setupEventListeners();
});

function setupEventListeners() {
    // Tab switching
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const tab = this.getAttribute('onclick').match(/'([^']+)'/)[1];
            showTab(tab);
        });
    });
    
    // Form submissions
    document.getElementById('smtpForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveSmtpSettings();
    });
    
    document.getElementById('templateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveTemplateSettings();
    });
    
    document.getElementById('testForm').addEventListener('submit', function(e) {
        e.preventDefault();
        sendTestEmail();
    });
}

function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    document.querySelector(`[onclick="showTab('${tabName}')"]`).classList.add('active');
}

function loadSettings() {
    fetch('api/setting-notifikasi.php?action=get')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const settings = data.data;
                
                // Populate SMTP form
                document.getElementById('smtp_host').value = settings.smtp_host || '';
                document.getElementById('smtp_port').value = settings.smtp_port || '';
                document.getElementById('smtp_username').value = settings.smtp_username || '';
                document.getElementById('smtp_password').value = settings.smtp_password || '';
                document.getElementById('from_email').value = settings.from_email || '';
                document.getElementById('from_name').value = settings.from_name || 'SIVAST - BKPSDM Kabupaten Bandung';
                
                // Populate template form
                document.getElementById('template_subject').value = settings.template_subject || '';
                document.getElementById('template_body').value = settings.template_body || '';
            }
        })
        .catch(error => {
            console.error('Error loading settings:', error);
        });
}

function saveSmtpSettings() {
    const formData = new FormData(document.getElementById('smtpForm'));
    const data = Object.fromEntries(formData);
    data.action = 'save_smtp';
    
    fetch('api/setting-notifikasi.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Pengaturan SMTP berhasil disimpan', 'success');
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving SMTP settings', 'danger');
    });
}

function saveTemplateSettings() {
    const formData = new FormData(document.getElementById('templateForm'));
    const data = Object.fromEntries(formData);
    data.action = 'save_template';
    
    fetch('api/setting-notifikasi.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Template email berhasil disimpan', 'success');
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving template', 'danger');
    });
}

function sendTestEmail() {
    const formData = new FormData(document.getElementById('testForm'));
    const data = Object.fromEntries(formData);
    data.action = 'test_email';
    
    const submitBtn = document.querySelector('#testForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    submitBtn.disabled = true;
    
    fetch('api/setting-notifikasi.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Test email berhasil dikirim', 'success');
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error sending test email', 'danger');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function resetTemplate() {
    if (confirm('Reset template ke pengaturan default?')) {
        const defaultSubject = 'Notifikasi Verifikasi SPJ - {bidang}';
        const defaultBody = `Yth. Bapak/Ibu {nama_pptk} dan {nama_bendahara},

Dengan hormat,

Berikut adalah informasi verifikasi SPJ untuk:
- Bidang: {bidang}
- Sub Kegiatan: {sub_kegiatan}
- Rekening Belanja: {nama_rekening_belanja}
- Bulan: {bulan} {tahun}
- Status: {status_verifikasi}
- Tanggal Verifikasi: {tanggal_verifikasi}

{alasan_tidak_lengkap}

Mohon segera ditindaklanjuti sesuai dengan ketentuan yang berlaku.

Terima kasih atas perhatiannya.

Hormat kami,
SIVAST - BKPSDM Kabupaten Bandung`;
        
        document.getElementById('template_subject').value = defaultSubject;
        document.getElementById('template_body').value = defaultBody;
    }
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