// Realisasi Anggaran JavaScript
let currentEditId = null;
let currentPage = 1;
let itemsPerPage = 20;
let allData = [];
let filteredData = []; // ✅ Simpan hasil filter

document.addEventListener('DOMContentLoaded', function () {
    loadRealisasiData();
    setupEventListeners();
});

function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            filterTable();
        });
    }

    // Form submission for edit
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            saveRealisasiData();
        });
    }

    // Dropdown filter listeners
    ['filterBidang', 'filterKegiatan', 'filterSubKegiatan', 'filterRekening'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', filterTable);
    });
}

function loadRealisasiData() {
    fetch('api/realisasi-anggaran.php?action=list')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allData = data.data || [];
                filteredData = allData; // ✅ Awalnya semua data
                currentPage = 1;
                renderPage();
                populateFilters();

                // Menampilkan Update Terakhir
                const lastUpdateElement = document.getElementById('lastUpdate');
                if (data.last_update) {
                    const date = new Date(data.last_update);
                    lastUpdateElement.textContent = 
                        `Update Terakhir: ${date.toLocaleString('id-ID')}`;
                } else {
                    lastUpdateElement.textContent = "Update Terakhir: -";
                }
            } else {
                showAlert('Error loading data: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading data', 'danger');
        });
}

function renderPage() {
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = filteredData.slice(start, end); // ✅ pakai filteredData
    populateTable(pageData);
    renderPagination();
}

function renderPagination() {
    const totalPages = Math.ceil(filteredData.length / itemsPerPage);
    const paginationContainer = document.getElementById('pagination');
    if (!paginationContainer) return;

    paginationContainer.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement('button');
        btn.textContent = i;
        btn.className = 'btn btn-sm mx-1 ' + (i === currentPage ? 'btn-primary' : 'btn-outline-primary');
        btn.onclick = () => {
            currentPage = i;
            renderPage();
        };
        paginationContainer.appendChild(btn);
    }
}

function populateTable(data) {
    const tbody = document.getElementById('realisasiBody');
    if (!tbody) {
        console.error("#realisasiBody tidak ditemukan");
        return;
    }

    tbody.innerHTML = '';

    data.forEach((item, index) => {
        const row = document.createElement('tr');
        const rowNumber = (currentPage - 1) * itemsPerPage + index + 1;

        // Calculate totals
        const semester1 = sumValues([
            item.realisasi_januari, item.realisasi_februari, item.realisasi_maret,
            item.realisasi_april, item.realisasi_mei, item.realisasi_juni
        ]);

        const semester2 = sumValues([
            item.realisasi_juli, item.realisasi_agustus, item.realisasi_september,
            item.realisasi_oktober, item.realisasi_november, item.realisasi_desember
        ]);

        const totalRealisasi = semester1 + semester2;
        const paguAnggaran = parseFloat(item.pagu_anggaran || 0);
        const sisaAnggaran = paguAnggaran - totalRealisasi;
        const persentase = paguAnggaran > 0 ? (totalRealisasi / paguAnggaran * 100) : 0;

        const isAdmin = document.querySelector('#editModal') !== null;

        row.innerHTML = `
            ${isAdmin ? `
            <td>
                <button class="btn btn-info btn-sm" onclick="viewData(${item.id})" title="View"><i class="fas fa-eye"></i></button>
                <button class="btn btn-warning btn-sm" onclick="editData(${item.id})" title="Edit"><i class="fas fa-edit"></i></button>
                <button class="btn btn-danger btn-sm" onclick="deleteData(${item.id})" title="Delete"><i class="fas fa-trash"></i></button>
            </td>` : `
            <td>
                <button class="btn btn-info btn-sm" onclick="viewData(${item.id})" title="View"><i class="fas fa-eye"></i></button>
            </td>`}
            <td>${rowNumber}</td>
            <td>${item.bidang}</td>
            <td>${item.kegiatan}</td>
            <td>${item.sub_kegiatan}</td>
            <td>${item.nama_rekening_belanja}</td>
            <td>${formatRupiah(item.pagu_anggaran || 0)}</td>
            <td>${formatRupiah(item.realisasi_januari || 0)}</td>
            <td>${formatRupiah(item.realisasi_februari || 0)}</td>
            <td>${formatRupiah(item.realisasi_maret || 0)}</td>
            <td>${formatRupiah(item.realisasi_april || 0)}</td>
            <td>${formatRupiah(item.realisasi_mei || 0)}</td>
            <td>${formatRupiah(item.realisasi_juni || 0)}</td>
            <td>${formatRupiah(item.realisasi_juli || 0)}</td>
            <td>${formatRupiah(item.realisasi_agustus || 0)}</td>
            <td>${formatRupiah(item.realisasi_september || 0)}</td>
            <td>${formatRupiah(item.realisasi_oktober || 0)}</td>
            <td>${formatRupiah(item.realisasi_november || 0)}</td>
            <td>${formatRupiah(item.realisasi_desember || 0)}</td>
            <td><strong>${formatRupiah(semester1)}</strong></td>
            <td><strong>${formatRupiah(semester2)}</strong></td>
            <td><strong>${formatRupiah(totalRealisasi)}</strong></td>
            <td><strong>${formatRupiah(sisaAnggaran)}</strong></td>
            <td><strong>${persentase.toFixed(2)}%</strong></td>
            
        `;
        tbody.appendChild(row);
    });
}

function sumValues(values) {
    return values.reduce((total, val) => total + parseFloat(val || 0), 0);
}

// 🔥 Generate Filter Dropdown
function populateFilters() {
    const bidangSet = new Set();
    const kegiatanSet = new Set();
    const subKegiatanSet = new Set();
    const rekeningSet = new Set();

    allData.forEach(item => {
        bidangSet.add(item.bidang);
        kegiatanSet.add(item.kegiatan);
        subKegiatanSet.add(item.sub_kegiatan);
        rekeningSet.add(item.nama_rekening_belanja);
    });

    fillDropdown('filterBidang', bidangSet);
    fillDropdown('filterKegiatan', kegiatanSet);
    fillDropdown('filterSubKegiatan', subKegiatanSet);
    fillDropdown('filterRekening', rekeningSet);
}

function fillDropdown(elementId, dataSet) {
    const select = document.getElementById(elementId);
    if (!select) return;
    select.innerHTML = `<option value="">Semua</option>`;
    Array.from(dataSet).sort().forEach(value => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        select.appendChild(option);
    });
}

// 🔥 Filter Table (baru)
function filterTable() {
    const searchValue = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const bidangFilter = document.getElementById('filterBidang')?.value.toLowerCase() || '';
    const kegiatanFilter = document.getElementById('filterKegiatan')?.value.toLowerCase() || '';
    const subKegiatanFilter = document.getElementById('filterSubKegiatan')?.value.toLowerCase() || '';
    const rekeningFilter = document.getElementById('filterRekening')?.value.toLowerCase() || '';

    filteredData = allData.filter(item => {
        const bidang = item.bidang?.toLowerCase() || '';
        const kegiatan = item.kegiatan?.toLowerCase() || '';
        const subKegiatan = item.sub_kegiatan?.toLowerCase() || '';
        const rekening = item.nama_rekening_belanja?.toLowerCase() || '';

        const matchesSearch = !searchValue || JSON.stringify(item).toLowerCase().includes(searchValue);
        const matchesBidang = !bidangFilter || bidang === bidangFilter;
        const matchesKegiatan = !kegiatanFilter || kegiatan === kegiatanFilter;
        const matchesSubKegiatan = !subKegiatanFilter || subKegiatan === subKegiatanFilter;
        const matchesRekening = !rekeningFilter || rekening === rekeningFilter;

        return matchesSearch && matchesBidang && matchesKegiatan && matchesSubKegiatan && matchesRekening;
    });

    currentPage = 1;
    renderPage();
}

function editData(id) {
    currentEditId = id;
    
    fetch(`api/realisasi-anggaran.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                
                // Populate form fields
                document.getElementById('edit_bidang').value = item.bidang;
                document.getElementById('edit_kegiatan').value = item.kegiatan;
                document.getElementById('edit_sub_kegiatan').value = item.sub_kegiatan;
                document.getElementById('edit_nama_rekening_belanja').value = item.nama_rekening_belanja;
                document.getElementById('edit_pagu_anggaran').value = item.pagu_anggaran || 0;
                document.getElementById('edit_realisasi_januari').value = item.realisasi_januari || 0;
                document.getElementById('edit_realisasi_februari').value = item.realisasi_februari || 0;
                document.getElementById('edit_realisasi_maret').value = item.realisasi_maret || 0;
                document.getElementById('edit_realisasi_april').value = item.realisasi_april || 0;
                document.getElementById('edit_realisasi_mei').value = item.realisasi_mei || 0;
                document.getElementById('edit_realisasi_juni').value = item.realisasi_juni || 0;
                document.getElementById('edit_realisasi_juli').value = item.realisasi_juli || 0;
                document.getElementById('edit_realisasi_agustus').value = item.realisasi_agustus || 0;
                document.getElementById('edit_realisasi_september').value = item.realisasi_september || 0;
                document.getElementById('edit_realisasi_oktober').value = item.realisasi_oktober || 0;
                document.getElementById('edit_realisasi_november').value = item.realisasi_november || 0;
                document.getElementById('edit_realisasi_desember').value = item.realisasi_desember || 0;
                
                showEditModal();
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
    fetch(`api/realisasi-anggaran.php?action=get&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = data.data;
                
                // Calculate totals
                const semester1 = parseFloat(item.realisasi_januari || 0) + 
                                 parseFloat(item.realisasi_februari || 0) + 
                                 parseFloat(item.realisasi_maret || 0) + 
                                 parseFloat(item.realisasi_april || 0) + 
                                 parseFloat(item.realisasi_mei || 0) + 
                                 parseFloat(item.realisasi_juni || 0);
                
                const semester2 = parseFloat(item.realisasi_juli || 0) + 
                                 parseFloat(item.realisasi_agustus || 0) + 
                                 parseFloat(item.realisasi_september || 0) + 
                                 parseFloat(item.realisasi_oktober || 0) + 
                                 parseFloat(item.realisasi_november || 0) + 
                                 parseFloat(item.realisasi_desember || 0);
                
                const totalRealisasi = semester1 + semester2;
                const paguAnggaran = parseFloat(item.pagu_anggaran || 0);
                const sisaAnggaran = paguAnggaran - totalRealisasi;
                const persentase = paguAnggaran > 0 ? (totalRealisasi / paguAnggaran * 100) : 0;
                
                const content = `
                    <div class="view-data">
                        <div class="row">
                            <div class="col"><strong>Bidang:</strong></div>
                            <div class="col">${item.bidang}</div>
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
                        <div class="row">
                            <div class="col"><strong>Pagu Anggaran:</strong></div>
                            <div class="col">${formatRupiah(item.pagu_anggaran || 0)}</div>
                        </div>
                        <hr>
                        <h4>Realisasi Per Bulan:</h4>
                        <div class="row">
                            <div class="col"><strong>Januari:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_januari || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Februari:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_februari || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Maret:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_maret || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>April:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_april || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Mei:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_mei || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Juni:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_juni || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Juli:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_juli || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Agustus:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_agustus || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>September:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_september || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Oktober:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_oktober || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>November:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_november || 0)}</div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Desember:</strong></div>
                            <div class="col">${formatRupiah(item.realisasi_desember || 0)}</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col"><strong>Total Semester 1:</strong></div>
                            <div class="col"><strong>${formatRupiah(semester1)}</strong></div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Total Semester 2:</strong></div>
                            <div class="col"><strong>${formatRupiah(semester2)}</strong></div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Total Realisasi:</strong></div>
                            <div class="col"><strong>${formatRupiah(totalRealisasi)}</strong></div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Sisa Anggaran:</strong></div>
                            <div class="col"><strong>${formatRupiah(sisaAnggaran)}</strong></div>
                        </div>
                        <div class="row">
                            <div class="col"><strong>Persentase:</strong></div>
                            <div class="col"><strong>${persentase.toFixed(2)}%</strong></div>
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

function deleteData(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        fetch('api/realisasi-anggaran.php', {
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
                loadRealisasiData();
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

function saveRealisasiData() {
    const formData = new FormData(document.getElementById('editForm'));
    const data = Object.fromEntries(formData);
    
    data.action = 'update';
    data.id = currentEditId;
    
    fetch('api/realisasi-anggaran.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Data berhasil diupdate', 'success');
            closeEditModal();
            loadRealisasiData();
        } else {
            showAlert('Error: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error saving data', 'danger');
    });
}

function showEditModal() {
    document.getElementById('editModal').classList.add('show');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('show');
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
    document.getElementById('filterBidang').value = '';
    document.getElementById('filterKegiatan').value = '';
    document.getElementById('filterSubKegiatan').value = '';
    document.getElementById('filterRekening').value = '';
    document.getElementById('searchInput').value = '';
    filterTable();
}

function exportExcel() {
    window.open('api/export-excel.php?type=realisasi-anggaran', '_blank');
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
            formData.append('type', 'realisasi-anggaran');
            
            fetch('api/import-excel.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Data berhasil diimport', 'success');
                    loadRealisasiData();
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
    window.open('api/download-template.php?type=realisasi-anggaran', '_blank');
}

function printPDF() {
    const bidang = document.getElementById('filterBidang')?.value || '';
    const tahun = document.getElementById('filterTahun')?.value || '';
    const bulan = document.getElementById('filterBulan')?.value || '';

    const url = `api/print-pdf.php?type=realisasi-anggaran&bidang=${encodeURIComponent(bidang)}&tahun=${encodeURIComponent(tahun)}&bulan=${encodeURIComponent(bulan)}`;
    window.open(url, '_blank');
}

function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
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