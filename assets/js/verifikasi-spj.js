// Verifikasi SPJ JavaScript
let currentEditId = null;
let currentPage = 1;
const itemsPerPage = 20 ;
let allData = [];
let filteredData = [];
let activeBidang = ""; // default semua

document.addEventListener('DOMContentLoaded', function() {
    loadVerifikasiData();
    setupEventListeners();
    setDefaultDate();
    setupTabMenu(); // <--- Tambah ini
});

function setupTabMenu() {
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            activeBidang = tab.dataset.bidang; 
            filterTable(); // refresh data
        });
    });
}

function setupEventListeners() {
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterTable();
    });
    
    // Dropdown filter change event
    ['filterBidang', 'filterTahun', 'filterBulan', 'filterSubKegiatan', 'filterStatus'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('change', filterTable);
        }
    });
    
    // Form submission
    document.getElementById('verifikasiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveVerifikasiData();
    });
}

function setDefaultDate() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tanggal_verifikasi').value = today;
}

currentPage = 1;
function loadVerifikasiData() {
    fetch('api/verifikasi-spj.php?action=list')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allData = data.data;
                populateFilterOptions(); // ⬅️ Tambahkan ini
                renderTable();
                renderPagination();
            } else {
                showAlert('Error loading data: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading data', 'danger');
        });
}
function renderTable(data = filteredData.length ? filteredData : allData) {
    const tbody = document.getElementById('verifikasiBody');
    tbody.innerHTML = '';

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const currentData = data.slice(start, end);

    currentData.forEach((item, index) => {
        const statusClass = item.status_verifikasi === 'Lengkap' ? 'status-lengkap' : 'status-belum-lengkap';
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${start + index + 1}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="viewData(${item.id})"><i class="fas fa-eye"></i></button>
                <button class="btn btn-warning btn-sm" onclick="editData(${item.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="deleteData(${item.id})"><i class="fas fa-trash"></i></button>
                <button class="btn btn-success btn-sm" onclick="sendNotification(${item.id})"><i class="fas fa-envelope"></i></button>
                <button class="btn btn-primary btn-sm" onclick="sendWhatsApp(${item.id})"><i class="fab fa-whatsapp"></i></button>
            </td>
            <td><span class="status-badge ${statusClass}">${item.status_verifikasi}</span></td>
            <td>${formatDate(item.tanggal_verifikasi)}</td>
            <td>${item.bidang}</td>
            <td>${item.tahun}</td>
            <td>${item.bulan}</td>
            <td>${item.sub_kegiatan}</td>
            <td>${item.nama_rekening_belanja}</td>
            <td>${item.alasan_tidak_lengkap || '-'}</td>
            <td>${item.nomor_bku}</td>
            <td>${item.keterangan_transaksi}</td>  
        `;
        tbody.appendChild(row);
    });
}


function renderPagination(data = filteredData.length ? filteredData : allData) {
    const container = document.getElementById('pagination');
    if (!container) return;

    container.innerHTML = '';
    const totalPages = Math.ceil(data.length / itemsPerPage);

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = 'btn btn-sm ' + (i === currentPage ? 'btn-primary' : 'btn-light');
        btn.onclick = () => {
            currentPage = i;
            renderTable();
            renderPagination();
        };
        container.appendChild(btn);
    }
}

function populateTable(data) {
    const tbody = document.getElementById('verifikasiBody');
    tbody.innerHTML = '';
    
    data.forEach((item, index) => {
        const row = document.createElement('tr');
        const statusClass = item.status_verifikasi === 'Lengkap' ? 'status-lengkap' : 'status-belum-lengkap';
        
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.bidang}</td>
            <td>${item.tahun}</td>
            <td>${item.bulan}</td>
            <td>${item.sub_kegiatan}</td>
            <td>${item.nama_rekening_belanja}</td>
            <td>${item.alasan_tidak_lengkap || '-'}</td>
            <td>${item.nomor_bku}</td>
            <td>${item.keterangan_transaksi}</td>
            <td><span class="status-badge ${statusClass}">${item.status_verifikasi}</span></td>
            <td>${formatDate(item.tanggal_verifikasi)}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="viewData(${item.id})" title="View">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-warning btn-sm" onclick="editData(${item.id})" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="deleteData(${item.id})" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="btn btn-success btn-sm" onclick="sendNotification(${item.id})" title="Kirim Notifikasi">
                    <i class="fas fa-envelope"></i>
                </button>
            <button class="btn btn-primary btn-sm" onclick="sendWhatsApp(${item.id})" title="Kirim WhatsApp">
    <i class="fab fa-whatsapp"></i>
</button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function addVerifikasi() {
    currentEditId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Verifikasi SPJ';
    document.getElementById('verifikasiForm').reset();
    setDefaultDate();
    showModal();
}

function editData(id) {
    currentEditId = id;
    document.getElementById('modalTitle').textContent = 'Edit Verifikasi SPJ';
    
    fetch(`api/verifikasi-spj.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                document.getElementById('bidang').value = item.bidang;
                document.getElementById('tahun').value = item.tahun;
                document.getElementById('bulan').value = item.bulan;
                document.getElementById('nomor_bku').value = item.nomor_bku;
                document.getElementById('keterangan_transaksi').value = item.keterangan_transaksi;
                document.getElementById('status_verifikasi').value = item.status_verifikasi;
                document.getElementById('alasan_tidak_lengkap').value = item.alasan_tidak_lengkap || '';
                document.getElementById('tanggal_verifikasi').value = item.tanggal_verifikasi;
                
                // Load sub kegiatan and rekening belanja
                loadSubKegiatan().then(() => {
                    document.getElementById('sub_kegiatan').value = item.sub_kegiatan;
                    loadRekeningBelanja().then(() => {
                        document.getElementById('nama_rekening_belanja').value = item.nama_rekening_belanja;
                    });
                });
                
                toggleAlasanField();
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

function viewData(id) {
    fetch(`api/verifikasi-spj.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                const statusClass = item.status_verifikasi === 'Lengkap' ? 'status-lengkap' : 'status-belum-lengkap';
                
                const content = `
                    <div class="view-data">
                        <div class="row">
                            <div class="col"><strong>Bidang:</strong></div>
                            <div class="col">${item.bidang}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Tahun:</strong></div>
                            <div class="col">${item.tahun}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Bulan:</strong></div>
                            <div class="col">${item.bulan}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Sub Kegiatan:</strong></div>
                            <div class="col">${item.sub_kegiatan}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Nama Rekening Belanja:</strong></div>
                            <div class="col">${item.nama_rekening_belanja}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Nomor BKU:</strong></div>
                            <div class="col">${item.nomor_bku}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Keterangan Transaksi:</strong></div>
                            <div class="col">${item.keterangan_transaksi}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Status Verifikasi:</strong></div>
                            <div class="col"><span class="status-badge ${statusClass}">${item.status_verifikasi}</span></div>
                        </div>
                        ${item.alasan_tidak_lengkap ? `
                        <div class="row">
                            <div class="col"><strong>Alasan Tidak Lengkap:</strong></div>
                            <div class="col">${item.alasan_tidak_lengkap}</div>
                        </div>
                        ` : ''}
                        <div class="row">
                            <div class="col"><strong>Tanggal Verifikasi:</strong></div>
                            <div class="col">${formatDate(item.tanggal_verifikasi)}</div>
                        </div>
                    </div>
                `;
                showInfoModal('Detail Verifikasi SPJ', content);
            } else {
                showAlert('Error loading data: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading data', 'danger');
        });
}

function deleteData(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        fetch('api/verifikasi-spj.php', {
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
                showAlert('Data berhasil dihapus', 'success');
                loadVerifikasiData();
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error deleting data', 'danger');
        });
    }
}

function saveVerifikasiData() {
    const formData = new FormData(document.getElementById('verifikasiForm'));
    const data = Object.fromEntries(formData);
    
    data.action = currentEditId ? 'update' : 'create';
    if (currentEditId) {
        data.id = currentEditId;
    }
    
    // Get selected values
    data.sub_kegiatan = document.getElementById('sub_kegiatan').value;
    data.nama_rekening_belanja = document.getElementById('nama_rekening_belanja').value;
    
    fetch('api/verifikasi-spj.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(currentEditId ? 'Data berhasil diupdate' : 'Data berhasil ditambahkan', 'success');
            closeModal();
            loadVerifikasiData();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving data', 'danger');
    });
}


function loadSubKegiatan() {
    const bidang = document.getElementById('bidang').value;
    const subKegiatanSelect = document.getElementById('sub_kegiatan');
    
    if (!bidang) {
        subKegiatanSelect.innerHTML = '<option value="">Pilih Sub Kegiatan</option>';
        return Promise.resolve();
    }
    
    return fetch(`api/master-data.php?action=get_sub_kegiatan&bidang=${encodeURIComponent(bidang)}`)
        .then(response => response.json())
        .then(data => {
            subKegiatanSelect.innerHTML = '<option value="">Pilih Sub Kegiatan</option>';
            if (data.success) {
                data.data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.sub_kegiatan;
                    option.textContent = item.sub_kegiatan;
                    subKegiatanSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading sub kegiatan:', error);
        });
}

function loadRekeningBelanja() {
    const bidang = document.getElementById('bidang').value;
    const subKegiatan = document.getElementById('sub_kegiatan').value;
    const rekeningSelect = document.getElementById('nama_rekening_belanja');
    
    if (!bidang || !subKegiatan) {
        rekeningSelect.innerHTML = '<option value="">Pilih Rekening Belanja</option>';
        return Promise.resolve();
    }
    
    return fetch(`api/master-data.php?action=get_rekening_belanja&bidang=${encodeURIComponent(bidang)}&sub_kegiatan=${encodeURIComponent(subKegiatan)}`)
        .then(response => response.json())
        .then(data => {
            rekeningSelect.innerHTML = '<option value="">Pilih Rekening Belanja</option>';
            if (data.success) {
                data.data.forEach(item => {
                    const option = document.createElement('option');
                    option.value = item.nama_rekening_belanja;
                    option.textContent = item.nama_rekening_belanja;
                    rekeningSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading rekening belanja:', error);
        });
}

function toggleAlasanField() {
    const status = document.getElementById('status_verifikasi').value;
    const alasanGroup = document.getElementById('alasanGroup');
    const alasanField = document.getElementById('alasan_tidak_lengkap');
    
    if (status === 'Belum Lengkap') {
        alasanGroup.style.display = 'block';
        alasanField.required = true;
    } else {
        alasanGroup.style.display = 'block'; // tetap kelihatan
        alasanField.required = false;
        alasanField.readOnly = true; // riwayat hanya dibaca
    }
}

function sendNotification(id) {
    if (confirm('Kirim notifikasi email ke PPTK dan Bendahara?')) {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;
        
        fetch('api/send-notification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'send',
                verifikasi_id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Notifikasi berhasil dikirim', 'success');
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error sending notification', 'danger');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
}

function sendWhatsApp(id) {
    if (confirm('Kirim notifikasi WhatsApp ke PPTK dan Bendahara?')) {
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        fetch('api/send-whatsapp.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                verifikasi_id: id
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Notifikasi WhatsApp berhasil dikirim', 'success');
            } else {
                showAlert('Error: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error sending WhatsApp notification', 'danger');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
}



function toggleFilter() {
    const filterPanel = document.getElementById('filterPanel');
    filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
}

function applyFilter() {
    filterTable();
}

function resetFilter() {
    document.getElementById('filterBidang').value = '';
    document.getElementById('filterTahun').value = '';
    document.getElementById('filterBulan').value='';
    document.getElementById('filterSubKegiatan').value='';
    document.getElementById('filterStatus').value = '';
    document.getElementById('searchInput').value = '';
    filterTable();
}

function filterTable() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const bidangFilter = document.getElementById('filterBidang').value;
    const tahunFilter = document.getElementById('filterTahun').value;
    const bulanFilter = document.getElementById('filterBulan').value;
    const subKegiatanFilter = document.getElementById('filterSubKegiatan').value;
    const statusFilter = document.getElementById('filterStatus').value;

    filteredData = allData.filter(item => {
        const matchesSearch = searchValue === '' || Object.values(item).some(val =>
            val && val.toString().toLowerCase().includes(searchValue)
        );
        const matchesBidang = (activeBidang === "" || item.bidang === activeBidang) &&
                            (!bidangFilter || item.bidang === bidangFilter);
        const matchesTahun = tahunFilter === '' || item.tahun === tahunFilter;
        const matchesBulan = bulanFilter === '' || item.bulan === bulanFilter;
        const matchesSubKegiatan = subKegiatanFilter === '' || item.sub_kegiatan === subKegiatanFilter;
        const matchesStatus = statusFilter === '' || item.status_verifikasi === statusFilter;

        return matchesSearch && matchesBidang && matchesTahun && matchesBulan && matchesSubKegiatan && matchesStatus;
    });

    currentPage = 1; // Reset ke halaman pertama saat filter berubah
    renderTable(); // ✅ Gunakan renderTable() dengan filteredData
    renderPagination();
}


function renderFilteredTable(data) {
    const tbody = document.getElementById('verifikasiBody');
    tbody.innerHTML = '';

    data.forEach((item, index) => {
        const row = document.createElement('tr');
        const statusClass = item.status_verifikasi === 'Lengkap' ? 'status-lengkap' : 'status-belum-lengkap';

        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.bidang}</td>
            <td>${item.tahun}</td>
            <td>${item.bulan}</td>
            <td>${item.sub_kegiatan}</td>
            <td>${item.nama_rekening_belanja}</td>
            <td>${item.alasan_tidak_lengkap || '-'}</td>
            <td>${item.nomor_bku}</td>
            <td>${item.keterangan_transaksi}</td>
            <td><span class="status-badge ${statusClass}">${item.status_verifikasi}</span></td>
            <td>${formatDate(item.tanggal_verifikasi)}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="viewData(${item.id})"><i class="fas fa-eye"></i></button>
                <button class="btn btn-warning btn-sm" onclick="editData(${item.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="deleteData(${item.id})"><i class="fas fa-trash"></i></button>
                <button class="btn btn-success btn-sm" onclick="sendNotification(${item.id})"><i class="fas fa-envelope"></i></button>
            </td>
        `;
        tbody.appendChild(row);
    });
}


function exportExcel() {
    window.open('api/export-excel.php?type=verifikasi-spj', '_blank');
}

function importExcel() {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.xlsx,.xls';
    input.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', 'verifikasi-spj');
            
            fetch('api/import-excel.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Data berhasil diimport', 'success');
                    loadVerifikasiData();
                } else {
                    showAlert('Error: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error importing data', 'danger');
            });
        }
    };
    input.click();
}

function downloadTemplate() {
    window.open('api/download-template.php?type=verifikasi-spj', '_blank');
}

function printPDF() {
    window.open('api/print-pdf.php?type=verifikasi-spj', '_blank');
}

function showModal() {
    document.getElementById('verifikasiModal').classList.add('show');
}

function closeModal() {
    document.getElementById('verifikasiModal').classList.remove('show');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
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
function populateFilterOptions() {
    const bidangSet = new Set();
    const tahunSet = new Set();
    const bulanSet = new Set();
    const subKegiatanSet = new Set();

    allData.forEach(item => {
        bidangSet.add(item.bidang);
        tahunSet.add(item.tahun);
        bulanSet.add(item.bulan);
        subKegiatanSet.add(item.sub_kegiatan);
    });

    populateSelect('filterBidang', bidangSet);
    populateSelect('filterTahun', tahunSet);
    populateSelect('filterBulan', bulanSet);
    populateSelect('filterSubKegiatan', subKegiatanSet);
}

function populateSelect(elementId, dataSet) {
    const select = document.getElementById(elementId);
    select.innerHTML = '<option value="">Semua</option>';
    Array.from(dataSet).sort().forEach(value => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        select.appendChild(option);
    });
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