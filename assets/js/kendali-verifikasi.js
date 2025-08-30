// Kendali Verifikasi JavaScript
document.addEventListener('DOMContentLoaded', function() {
    setupEventListeners();
    setTanggalCetak();

    // ✅ Auto load jika ada query string di URL
    const params = new URLSearchParams(window.location.search);
    const bidang = params.get('bidang');
    const tahun  = params.get('tahun');
    const bulan  = params.get('bulan');

    if (bidang && tahun && bulan) {
        // Set value ke form filter
        document.getElementById('bidang').value = bidang;
        document.getElementById('tahun').value = tahun;
        document.getElementById('bulan').value = bulan;

        // Langsung load data
        loadKendaliData();
    }
});

function setupEventListeners() {
    // Form submission
    document.getElementById('kendaliForm').addEventListener('submit', function(e) {
        e.preventDefault();
        loadKendaliData();
    });
}

function setTanggalCetak() {
    const today = new Date();
    const tanggal = today.toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById('tanggalCetak').textContent = tanggal;
}

function loadKendaliData() {
    const bidang = document.getElementById('bidang').value;
    const tahun = document.getElementById('tahun').value;
    const bulan = document.getElementById('bulan').value;
    
    if (!bidang || !tahun || !bulan) {
        showAlert('Harap pilih bidang, tahun, dan bulan terlebih dahulu', 'warning');
        return;
    }
    
    // Show loading state
    const submitBtn = document.querySelector('#kendaliForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memuat Data...';
    submitBtn.disabled = true;
    
    // Fetch data
    fetch(`api/kendali-verifikasi.php?action=get_data&bidang=${encodeURIComponent(bidang)}&tahun=${tahun}&bulan=${encodeURIComponent(bulan)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateKendaliData(data.data, bidang, tahun, bulan);
                showPreview();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading data', 'danger');
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
}

function populateKendaliData(data, bidang, tahun, bulan) {
    // Update header info
    document.getElementById('previewBidang').textContent = bidang;
    document.getElementById('previewTahun').textContent = tahun;
    document.getElementById('previewBulan').textContent = bulan;
    
    document.getElementById('headerBidang').textContent = bidang;
    document.getElementById('headerTahun').textContent = tahun;
    document.getElementById('headerBulan').textContent = bulan;
    
    // Populate table
    const tbody = document.getElementById('kendaliTableBody');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">
                    <div class="no-data">
                        <i class="fas fa-check-circle" style="color: #10b981; font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p><strong>Semua SPJ Sudah Lengkap!</strong></p>
                        <p>Tidak ada dokumen SPJ yang belum lengkap untuk periode ini.</p>
                    </div>
                </td>
            </tr>
        `;
        document.getElementById('totalBelumLengkap').textContent = '0';
    } else {
        data.forEach((item, index) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${index + 1}</td>
                <td>${item.sub_kegiatan}</td>
                <td>${item.nama_rekening_belanja}</td>
                <td><span class="status-badge status-belum-lengkap">Belum Lengkap</span></td>
                <td>${item.alasan_tidak_lengkap || '-'}</td>
                <td>${item.nomor_bku}</td>
                <td>${item.keterangan_transaksi}</td>
                <td></td>
                <td></td>
                <td></td>
            `;
            tbody.appendChild(row);
        });
        
        document.getElementById('totalBelumLengkap').textContent = data.length;
    }
}

function showPreview() {
    document.getElementById('previewSection').style.display = 'block';
    document.getElementById('cetakBtn').style.display = 'inline-flex';
    
    // Load PDF preview ke iframe
    const bidang = document.getElementById('bidang').value;
    const tahun = document.getElementById('tahun').value;
    const bulan = document.getElementById('bulan').value;

    const pdfUrl = `api/print-kendali.php?bidang=${encodeURIComponent(bidang)}&tahun=${tahun}&bulan=${encodeURIComponent(bulan)}`;
    const iframe = document.getElementById('pdfPreview');
    if (iframe) {
        iframe.src = pdfUrl;
        iframe.style.display = 'block';
    }

    // Scroll to preview
    document.getElementById('previewSection').scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

function cetakKartuKendali() {
    const bidang = document.getElementById('bidang').value;
    const tahun = document.getElementById('tahun').value;
    const bulan = document.getElementById('bulan').value;
    
    // Open print window
    const printUrl = `api/print-kendali.php?bidang=${encodeURIComponent(bidang)}&tahun=${tahun}&bulan=${encodeURIComponent(bulan)}`;
    window.open(printUrl, '_blank');
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
