// Master Data JavaScript
let currentEditId = null;
let currentPage = 1;
const itemsPerPage = 20;
let allData = [];
let filteredData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadMasterData();
    setupEventListeners();
});

function setupEventListeners() {
    document.getElementById('searchInput').addEventListener('input', function() {
        filterTable();
    });

    document.getElementById('filterKPA').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('filterKegiatan').addEventListener('change', function() {
        filterTable();
    });

    document.getElementById('dataForm').addEventListener('submit', function(e) {
        e.preventDefault();
        saveMasterData();
    });
}

function loadMasterData() {
    fetch('api/master-data.php?action=list')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allData = data.data;
                filteredData = allData;
                populateFilterDropdowns();
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

function populateFilterDropdowns() {
    const kpaSet = new Set();
    const kegiatanSet = new Set();

    allData.forEach(item => {
        kpaSet.add(item.kuasa_pengguna_anggaran);
        kegiatanSet.add(item.kegiatan);
    });

    const kpaSelect = document.getElementById('filterKPA');
    const kegiatanSelect = document.getElementById('filterKegiatan');

    kpaSelect.innerHTML = '<option value="">-- Semua KPA --</option>';
    kegiatanSelect.innerHTML = '<option value="">-- Semua Kegiatan --</option>';

    kpaSet.forEach(kpa => {
        const option = document.createElement('option');
        option.value = kpa;
        option.textContent = kpa;
        kpaSelect.appendChild(option);
    });

    kegiatanSet.forEach(kegiatan => {
        const option = document.createElement('option');
        option.value = kegiatan;
        option.textContent = kegiatan;
        kegiatanSelect.appendChild(option);
    });
}

function renderTable(data = filteredData) {
    const tbody = document.getElementById('masterDataBody');
    tbody.innerHTML = '';

    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const currentData = data.slice(start, end);

    currentData.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${start + index + 1}</td>
            <td>${item.bidang}</td>
            <td>${item.kuasa_pengguna_anggaran}</td>
            <td>${item.nama_bendahara}</td>
            <td>${item.email_bendahara}</td>
            <td>${item.wa_bendahara}</td>
            <td>${item.nama_pptk}</td>
            <td>${item.email_pptk}</td>
            <td>${item.wa_pptk}</td>
            <td>${item.kegiatan}</td>
            <td>${item.sub_kegiatan}</td>
            <td>${item.nama_rekening_belanja}</td>
            <td>
                <button class="btn btn-info btn-sm" onclick="viewData(${item.id})"><i class="fas fa-eye"></i></button>
                <button class="btn btn-warning btn-sm" onclick="editData(${item.id})"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="deleteData(${item.id})"><i class="fas fa-trash"></i></button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function renderPagination(data = filteredData) {
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
    const tbody = document.getElementById('masterDataBody');
    tbody.innerHTML = '';
    
    data.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${item.bidang}</td>
            <td>${item.kuasa_pengguna_anggaran}</td>
            <td>${item.nama_bendahara}</td>
            <td>${item.email_bendahara}</td>
            <td>${item.wa_bendahara}</td>
            <td>${item.nama_pptk}</td>
            <td>${item.email_pptk}</td>
            <td>${item.wa_pptk}</td>
            <td>${item.kegiatan}</td>
            <td>${item.sub_kegiatan}</td>
            <td>${item.nama_rekening_belanja}</td>
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
            </td>
        `;
        tbody.appendChild(row);
    });
}

function addData() {
    currentEditId = null;
    document.getElementById('modalTitle').textContent = 'Tambah Data Master';
    document.getElementById('dataForm').reset();
    showModal();
}

function editData(id) {
    currentEditId = id;
    document.getElementById('modalTitle').textContent = 'Edit Data Master';
    
    fetch(`api/master-data.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                document.getElementById('bidang').value = item.bidang;
                document.getElementById('kuasa_pengguna_anggaran').value = item.kuasa_pengguna_anggaran;
                document.getElementById('nama_bendahara').value = item.nama_bendahara;
                document.getElementById('email_bendahara').value = item.email_bendahara;
                document.getElementById('wa_bendahara').value = item.wa_bendahara;
                document.getElementById('nama_pptk').value = item.nama_pptk;
                document.getElementById('email_pptk').value = item.email_pptk;
                document.getElementById('wa_pptk').value = item.wa_pptk;
                document.getElementById('kegiatan').value = item.kegiatan;
                document.getElementById('sub_kegiatan').value = item.sub_kegiatan;
                document.getElementById('nama_rekening_belanja').value = item.nama_rekening_belanja;
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
    fetch(`api/master-data.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                const content = `
                    <div class="view-data">
                        <div class="row">
                            <div class="col"><strong>Bidang:</strong></div>
                            <div class="col">${item.bidang}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Kuasa Pengguna Anggaran:</strong></div>
                            <div class="col">${item.kuasa_pengguna_anggaran}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Nama Bendahara:</strong></div>
                            <div class="col">${item.nama_bendahara}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Email Bendahara:</strong></div>
                            <div class="col">${item.email_bendahara}</div>
                        </div>
                    <div class="row">
                            <div class="col"><strong>Whatsapp Bendahara:</strong></div>
                            <div class="col">${item.wa_bendahara}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Nama PPTK:</strong></div>
                            <div class="col">${item.nama_pptk}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Email PPTK:</strong></div>
                            <div class="col">${item.email_pptk}</div>
                        </div>
                    <div class="row">
                            <div class="col"><strong>Whatsapp PPTK:</strong></div>
                            <div class="col">${item.wa_pptk}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Kegiatan:</strong></div>
                            <div class="col">${item.kegiatan}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Sub Kegiatan:</strong></div>
                            <div class="col">${item.sub_kegiatan}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Nama Rekening Belanja:</strong></div>
                            <div class="col">${item.nama_rekening_belanja}</div>
                        </div>
                    </div>
                `;
                showInfoModal('Detail Data Master', content);
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
        fetch('api/master-data.php', {
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
                loadMasterData();
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

function saveMasterData() {
    const formData = new FormData(document.getElementById('dataForm'));
    const data = Object.fromEntries(formData);
    
    data.action = currentEditId ? 'update' : 'create';
    if (currentEditId) {
        data.id = currentEditId;
    }
    
    fetch('api/master-data.php', {
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
            loadMasterData();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving data', 'danger');
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
    document.getElementById('filterBidang').value = '';
    document.getElementById('filterKPA').value = '';
    document.getElementById('filterKegiatan').value = '';
    document.getElementById('searchInput').value = '';
    filterTable();
    filteredData = allData;
    currentPage = 1;
    renderTable();
    renderPagination();
}

function filterTable() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const bidangFilter = document.getElementById('filterBidang').value.toLowerCase();
    const kpaFilter = document.getElementById('filterKPA').value.toLowerCase();
    const kegiatanFilter = document.getElementById('filterKegiatan').value.toLowerCase();

    filteredData = allData.filter(item => {
        const bidang = item.bidang.toLowerCase();
        const kpa = item.kuasa_pengguna_anggaran.toLowerCase();
        const kegiatan = item.kegiatan.toLowerCase();

        const matchesSearch = searchValue === '' ||
            Object.values(item).some(val =>
                String(val).toLowerCase().includes(searchValue)
            );
        const matchesBidang = bidangFilter === '' || bidang.includes(bidangFilter);
        const matchesKPA = kpaFilter === '' || kpa.includes(kpaFilter);
        const matchesKegiatan = kegiatanFilter === '' || kegiatan.includes(kegiatanFilter);

        return matchesSearch && matchesBidang && matchesKPA && matchesKegiatan;
    });

    currentPage = 1; // reset ke halaman pertama
    renderTable();
    renderPagination();
}

function exportExcel() {
    window.open('api/export-excel.php?type=master-data', '_blank');
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
            formData.append('type', 'master-data');
            
            fetch('api/import-excel.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Data berhasil diimport', 'success');
                    loadMasterData();
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
    window.open('api/download-template.php?type=master-data', '_blank');
}

function printPDF() {
    window.open('api/print-pdf.php?type=master-data', '_blank');
}

function showModal() {
    document.getElementById('dataModal').classList.add('show');
}

function closeModal() {
    document.getElementById('dataModal').classList.remove('show');
}

function showAlert(message, type) {
    // Create alert element
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <span>${message}</span>
        <button type="button" class="close" onclick="this.parentElement.remove()">×</button>
    `;
    
    // Add to page
    document.body.insertBefore(alert, document.body.firstChild);
    
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