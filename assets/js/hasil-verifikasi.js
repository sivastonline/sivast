// Hasil Verifikasi JavaScript
let currentEditId = null;
let currentPage = 1;
const itemsPerPage = 20;
let allData = [];
let filteredData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadHasilData();
    setupEventListeners();
});

function setupEventListeners() {
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        filterTable();
    });

    document.getElementById('filterSubKegiatan').addEventListener('change', function () {
    filterTable();
});

}

function loadHasilData() {
    const bidang = window.currentBidang;
    
    fetch(`api/verifikasi-spj.php?action=list&bidang=${encodeURIComponent(bidang)}`)
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

function renderPagination() {
    const pagination = document.getElementById('pagination');
    pagination.innerHTML = '';

    const totalPages = Math.ceil(filteredData.length / itemsPerPage);

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = 'btn btn-sm ' + (i === currentPage ? 'btn-primary' : 'btn-light');
        if (i === currentPage) {
            btn.classList.add('active');
        }
        btn.onclick = () => {
            currentPage = i;
            renderTablePage(currentPage);
            renderPagination();
        };
        pagination.appendChild(btn);
    }
}

function populateTable(data) {
    allData = data;
    filteredData = [...allData]; // inisialisasi awal
    populateSubKegiatanDropdown(allData); // panggil fungsi baru
    currentPage = 1;
    renderFilteredTablePage(currentPage);
    renderPagination();
}

function populateSubKegiatanDropdown(data) {
    const dropdown = document.getElementById('filterSubKegiatan');
    const subKegiatanSet = new Set(data.map(item => item.sub_kegiatan));
    dropdown.innerHTML = '<option value="">-- Semua Sub Kegiatan --</option>';
    subKegiatanSet.forEach(sk => {
        const option = document.createElement('option');
        option.value = sk;
        option.textContent = sk;
        dropdown.appendChild(option);
    });
}


function renderTablePage(page) {
    const tbody = document.getElementById('hasilBody');
    tbody.innerHTML = '';

    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = allData.slice(start, end);

    pageData.forEach((item, index) => {
        const row = document.createElement('tr');
        const statusClass = item.status_verifikasi === 'Lengkap' ? 'status-lengkap' : 'status-belum-lengkap';

        row.innerHTML = `
            <td>${start + index + 1}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="viewData(${item.id})" title="View">
                    <i class="fas fa-eye"></i>
                </button>
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
                
                document.getElementById('viewContent').innerHTML = content;
                document.getElementById('viewModal').classList.add('show');
            } else {
                showAlert('Error loading data: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading data', 'danger');
        });
}

function closeViewModal() {
    document.getElementById('viewModal').classList.remove('show');
}

function toggleFilter() {
    const filterPanel = document.getElementById('filterPanel');
    filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
}

function applyFilter() {
    filterTable();
}

function resetFilter() {
    document.getElementById('filterTahun').value = '';
    document.getElementById('filterBulan').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('searchInput').value = '';
    filterTable();
}

function filterTable() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const tahunFilter = document.getElementById('filterTahun').value;
    const bulanFilter = document.getElementById('filterBulan').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const subKegiatanFilter = document.getElementById('filterSubKegiatan').value;

    filteredData = allData.filter(item => {
        const matchesSearch =
            searchValue === '' ||
            Object.values(item).some(val =>
                String(val).toLowerCase().includes(searchValue)
            );
        const matchesTahun = tahunFilter === '' || item.tahun === tahunFilter;
        const matchesBulan = bulanFilter === '' || item.bulan === bulanFilter;
        const matchesStatus = statusFilter === '' || item.status_verifikasi === statusFilter;
        const matchesSubKegiatan = subKegiatanFilter === '' || item.sub_kegiatan === subKegiatanFilter;

        return matchesSearch && matchesTahun && matchesBulan && matchesStatus && matchesSubKegiatan;
    });

    currentPage = 1;
    renderFilteredTablePage(currentPage);
    renderPagination();
}

function renderFilteredTablePage(page) {
    const tbody = document.getElementById('hasilBody');
    tbody.innerHTML = '';

    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = filteredData.slice(start, end);

    pageData.forEach((item, index) => {
        const row = document.createElement('tr');
        const statusClass = item.status_verifikasi === 'Lengkap' ? 'status-lengkap' : 'status-belum-lengkap';

        row.innerHTML = `
            <td>${start + index + 1}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="viewData(${item.id})" title="View">
                    <i class="fas fa-eye"></i>
                </button>
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


function exportExcel() {
    const bidang = window.currentBidang;
    window.open(`api/export-excel.php?type=hasil-verifikasi&bidang=${encodeURIComponent(bidang)}`, '_blank');
}

function printPDF() {
    const bidang = window.currentBidang;
    window.open(`api/print-pdf.php?type=hasil-verifikasi&bidang=${encodeURIComponent(bidang)}`, '_blank');
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