<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$bidang = $_GET['bidang'] ?? '';

try {
    // Map bidang parameter to database values
    $bidangMap = [
        'sekretariat' => 'Sekretariat',
        'pkpa' => 'Bidang PKPA',
        'ppik' => 'Bidang PPIK',
        'diklat' => 'Bidang DIKLAT',
        'mpasn' => 'Bidang MPASN'
    ];
    
    $bidangName = $bidangMap[$bidang] ?? $bidang;

    // Ambil semua bulan dari Jan 2024 - Des 2025
    $months = [
        '2024-01','2024-02','2024-03','2024-04','2024-05','2024-06',
        '2024-07','2024-08','2024-09','2024-10','2024-11','2024-12',
        '2025-01','2025-02','2025-03','2025-04','2025-05','2025-06',
        '2025-07','2025-08','2025-09','2025-10','2025-11','2025-12'
    ];

    $monthNames = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];

    $now = new DateTime();
    $currentMonth = (int)$now->format('m');
    $currentYear = (int)$now->format('Y');

    $completedMonths = 0;
    $totalProgressMonths = 0;

    foreach ($months as $month) {
        [$year, $mon] = explode('-', $month);
        $year = (int)$year;
        $monInt = (int)$mon;

        // Hitung hanya bulan sampai bulan kemarin
        if (
            ($year > 2024 || ($year === 2024 && $monInt >= 1)) &&
            ($year < $currentYear || ($year === $currentYear && $monInt < $currentMonth))
        ) {
            $totalProgressMonths++;

            // Ambil data untuk bulan ini
            $monthName = $monthNames[$mon];
            $sql = "SELECT COUNT(*) as total_count,
                           SUM(CASE WHEN status_verifikasi = 'Lengkap' THEN 1 ELSE 0 END) as complete_count
                    FROM verifikasi_spj 
                    WHERE bidang = ? AND tahun = ? AND bulan = ?";
            $stats = $db->fetch($sql, [$bidangName, $year, $monthName]);

            $totalCount = $stats['total_count'] ?? 0;
            $completeCount = $stats['complete_count'] ?? 0;

            if ($totalCount > 0 && $completeCount == $totalCount) {
                $completedMonths++;
            }
        }
    }

    // Persentase berdasarkan bulan lengkap
    $completePercentage = $totalProgressMonths > 0
        ? round(($completedMonths / $totalProgressMonths) * 100, 2)
        : 0;

    // Tetap ambil data untuk kolom lain (tidak diubah)
    $verifikasiSql = "SELECT 
        COUNT(*) as total_verifikasi,
        SUM(CASE WHEN status_verifikasi = 'Lengkap' THEN 1 ELSE 0 END) as lengkap_count,
        SUM(CASE WHEN status_verifikasi = 'Belum Lengkap' THEN 1 ELSE 0 END) as belum_lengkap_count
        FROM verifikasi_spj WHERE bidang = ?";
    $verifikasiStats = $db->fetch($verifikasiSql, [$bidangName]);

    $totalVerifikasi = $verifikasiStats['total_verifikasi'] ?? 0;
    $lengkapCount = $verifikasiStats['lengkap_count'] ?? 0;
    $belumLengkapCount = $verifikasiStats['belum_lengkap_count'] ?? 0;

    // Budget realization tetap sama
    $budgetSql = "SELECT 
        SUM(pagu_anggaran) as total_pagu,
        SUM(realisasi_januari + realisasi_februari + realisasi_maret + realisasi_april + 
            realisasi_mei + realisasi_juni + realisasi_juli + realisasi_agustus + 
            realisasi_september + realisasi_oktober + realisasi_november + realisasi_desember) as total_realisasi
        FROM realisasi_anggaran WHERE bidang = ?";
    $budgetStats = $db->fetch($budgetSql, [$bidangName]);

    $totalPagu = $budgetStats['total_pagu'] ?? 0;
    $totalRealisasi = $budgetStats['total_realisasi'] ?? 0;
    $budgetRealization = $totalPagu > 0 ? round(($totalRealisasi / $totalPagu) * 100, 2) : 0;

    $incompletePercentage = $totalVerifikasi > 0 
    ? round(($lengkapCount / $totalVerifikasi) * 100, 2) 
    : 0;

    $response = [
        'success' => true,
        'data' => [
            'complete_percentage' => $completePercentage, // sekarang pakai rumus bulan
            'incomplete_count' => $belumLengkapCount,
            'incomplete_percentage' => $incompletePercentage, // PATCH
            'budget_realization' => $budgetRealization,
            'total_verifikasi' => $totalVerifikasi,
            'lengkap_count' => $lengkapCount,
            'total_pagu' => $totalPagu,
            'total_realisasi' => $totalRealisasi
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
