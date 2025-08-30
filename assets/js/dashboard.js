// Dashboard JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Load awal
    loadDashboardData();
    loadRecapTable();
    getLastUpdate(); // PATCH: ambil update terakhir realisasi anggaran


    // Pasang event listener tombol filter
    const filterBtn = document.getElementById('apply-filter');
    if (filterBtn) {
        filterBtn.addEventListener('click', () => {
            const select = document.getElementById('month-select');
            const selected = Array.from(select.selectedOptions).map(opt => opt.value);
            setMonthFilter(selected);
        });
    }
});

// Variabel global untuk filter bulan
let selectedMonths = [];

function setMonthFilter(months) {
    selectedMonths = months; // contoh: ["2024-03","2024-05"]
    loadRecapTable();
}

function loadDashboardData() {
    const bidangs = ['sekretariat', 'pkpa', 'ppik', 'diklat', 'mpasn'];
    bidangs.forEach(bidang => {
        fetchBidangStats(bidang);
    });
}

// PATCH: fungsi ambil update terakhir realisasi anggaran
function getLastUpdate() {
    fetch('api/realisasi-anggaran.php?action=list')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const lastUpdate = data.last_update;

                if (lastUpdate) {
                    const date = new Date(lastUpdate);
                    const options = {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    };
                    // hasil: "15 Agustus 2025"
                    const formattedDate = date.toLocaleDateString('id-ID', options);

                    // PATCH: update semua card yang punya class .last-update
                    document.querySelectorAll('.last-update').forEach(el => {
                        el.textContent = `(Update Realisasi terakhir: ${formattedDate})`;
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error fetching last update:', error);
        });
}


function fetchBidangStats(bidang) {
    fetch(`api/dashboard-stats.php?bidang=${bidang}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateBidangStats(bidang, data.data);
            }
        })
        .catch(error => {
            console.error('Error fetching stats:', error);
        });
}

function updateBidangStats(bidang, stats) {
    const completeElement = document.getElementById(`${bidang}-complete`);
    const incompleteElement = document.getElementById(`${bidang}-incomplete`);
    const budgetElement = document.getElementById(`${bidang}-budget`);
    const totalBudgetElement = document.getElementById(`${bidang}-totalbudget`);
    
    if (completeElement) completeElement.textContent = `${stats.complete_percentage}%`;
    if (incompleteElement) incompleteElement.textContent = stats.incomplete_count;
    if (budgetElement) budgetElement.textContent = `${stats.budget_realization}%`;
    if (totalBudgetElement) totalBudgetElement.textContent = `Rp ${stats.total_realisasi.toLocaleString('id-ID')}`; 

    // PATCH: hitung gauge value
    const gaugeValue = ((stats.complete_percentage + stats.incomplete_percentage) / 2).toFixed(2);

    const canvas = document.getElementById(`gauge-${bidang}`);
    const gaugeText = document.getElementById(`${bidang}-gauge-text`);
    if (gaugeText) gaugeText.textContent = `${gaugeValue}%`;

    if (canvas) {
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [gaugeValue, 100 - gaugeValue],
                    backgroundColor: ['#2a7f91', '#e6e6e6'],
                    borderWidth: 0
                }]
            },
            options: {
                rotation: -90,
                circumference: 180,
                cutout: '70%',
                plugins: { legend: { display: false }, tooltip: { enabled: false } }
            }
        });
    }
}

function loadRecapTable() {
    fetch('api/recap-data.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateRecapTable(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading recap table:', error);
        });
}

function populateRecapTable(data, isAscending) {

data.sort((a, b) => {
        return isAscending ? a.progres_bulanan - b.progres_bulanan : b.progres_bulanan - a.progres_bulanan;
    });

    const tbody = document.getElementById('recap-table-body');
    tbody.innerHTML = '';

    const headerRow = document.getElementById('recap-header-row');
    headerRow.innerHTML = '<th>BIDANG/SEKRETARIAT</th>'; // reset header

    const bidangs = ['Sekretariat', 'Bidang PKPA', 'Bidang PPIK', 'Bidang DIKLAT', 'Bidang MPASN'];
    const months = [
        '2024-01','2024-02','2024-03','2024-04','2024-05','2024-06',
        '2024-07','2024-08','2024-09','2024-10','2024-11','2024-12',
        '2025-01','2025-02','2025-03','2025-04','2025-05','2025-06',
        '2025-07','2025-08','2025-09','2025-10','2025-11','2025-12'
    ];

    // Render header bulan sesuai filter
    months.forEach(month => {
        if (selectedMonths.length > 0 && !selectedMonths.includes(month)) {
            return; // skip bulan tidak terpilih
        }
        headerRow.innerHTML += `<th style="font-size: 16px";>${month}</th>`;
    });

    // Tambahkan kolom progres
    headerRow.innerHTML += '<th>Progres Bulanan</th><th>Progres Dokumen</th>';

    bidangs.forEach(bidang => {
        const row = document.createElement('tr');
        let html = `<td><strong>${bidang}</strong></td>`;
        
        // Hitung progres (selalu pakai semua bulan, tidak terpengaruh filter)
        let completedMonths = 0;
        let totalDocs = 0;
        let completeDocs = 0;

        const now = new Date();
        const currentMonth = now.getMonth() + 1;
        const currentYear = now.getFullYear();

        months.forEach(month => {
            const monthData = data.find(d => d.bidang === bidang && d.month === month);
            const [year, mon] = month.split('-').map(Number);

            if (monthData) {
                if (monthData.all_complete) completedMonths++;
                totalDocs += parseInt(monthData.total_count);
                completeDocs += parseInt(monthData.complete_count);
            } 
        });

        const totalProgressMonths = months.filter(m => {
            const [y, mm] = m.split('-').map(Number);
            return (y > 2024 || (y === 2024 && mm >= 1))
                && (y < currentYear || (y === currentYear && mm < currentMonth));
        }).length;

        const monthProgress = totalProgressMonths > 0 
            ? ((completedMonths / totalProgressMonths) * 100).toFixed(2)
            : "0.00";
        const docProgress = totalDocs > 0 
            ? ((completeDocs / totalDocs) * 100).toFixed(2)
            : "0.00";

        // Render bulan (hanya yang sesuai filter)
        months.forEach(month => {
            if (selectedMonths.length > 0 && !selectedMonths.includes(month)) {
                return; // skip bulan tidak terpilih
            }

            const monthData = data.find(d => d.bidang === bidang && d.month === month);
            const [year, mon] = month.split('-').map(Number);
            let cellClass = 'month-complete';

           if (monthData) {
    if (monthData.total_count === 0) {
        // Ada data tapi kosong (anggap tidak ada dokumen)
        cellClass = 'month-missing';
    } else if (monthData.complete_count < monthData.total_count) {
        // Ada data tapi belum semua lengkap
        cellClass = 'month-partial';
    } else {
        // Semua lengkap
        cellClass = 'month-complete';
    }
} else {
    // Benar-benar tidak ada data
    cellClass = 'month-missing';
}
            html += `<td class="${cellClass}"></td>`;
        });

        // Tambahkan kolom progres
        html += `<td><strong>${monthProgress}%</strong></td>`;
        html += `<td><strong>${docProgress}%</strong></td>`;
        
        row.innerHTML = html;
        tbody.appendChild(row);
    });
}

// Auto refresh dashboard setiap 5 menit
setInterval(() => {
    loadDashboardData();
    loadRecapTable();
}, 300000);
