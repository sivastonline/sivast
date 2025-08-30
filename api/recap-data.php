<?php
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $bidangs = ['Sekretariat', 'Bidang PKPA', 'Bidang PPIK', 'Bidang DIKLAT', 'Bidang MPASN'];
    $months = [
        '2024-01', '2024-02', '2024-03', '2024-04', '2024-05', '2024-06',
        '2024-07', '2024-08', '2024-09', '2024-10', '2024-11', '2024-12',
        '2025-01', '2025-02', '2025-03', '2025-04', '2025-05', '2025-06',
        '2025-07', '2025-08', '2025-09', '2025-10', '2025-11', '2025-12'
    ];
    
    $recapData = [];
    
    foreach ($bidangs as $bidang) {
        foreach ($months as $month) {
            list($year, $monthNum) = explode('-', $month);
            $monthNames = [
                '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
            ];
            $monthName = $monthNames[$monthNum];
            
            // Get verification data for this bidang and month
            $sql = "SELECT 
                COUNT(*) as total_count,
                SUM(CASE WHEN status_verifikasi = 'Lengkap' THEN 1 ELSE 0 END) as complete_count
                FROM verifikasi_spj 
                WHERE bidang = ? AND tahun = ? AND bulan = ?";
            
            $stats = $db->fetch($sql, [$bidang, $year, $monthName]);
            
            $totalCount = $stats['total_count'] ?? 0;
            $completeCount = $stats['complete_count'] ?? 0;
            $allComplete = $totalCount > 0 && $completeCount == $totalCount;
            
            if ($totalCount > 0) {
                $recapData[] = [
                    'bidang' => $bidang,
                    'month' => $month,
                    'year' => $year,
                    'month_name' => $monthName,
                    'total_count' => $totalCount,
                    'complete_count' => $completeCount,
                    'all_complete' => $allComplete
                ];
            }
        }
    }
    
    echo json_encode(['success' => true, 'data' => $recapData]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>