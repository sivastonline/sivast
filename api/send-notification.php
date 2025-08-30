<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$action = $_POST['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? $action;

if ($action !== 'send') {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

try {
    $verifikasiId = $input['verifikasi_id'] ?? 0;
    
    // Get verification data with master data
    $sql = "SELECT v.*, m.nama_pptk, m.email_pptk, m.nama_bendahara, m.email_bendahara
            FROM verifikasi_spj v
            LEFT JOIN master_data m ON v.bidang = m.bidang AND v.sub_kegiatan = m.sub_kegiatan 
                AND v.nama_rekening_belanja = m.nama_rekening_belanja
            WHERE v.id = ?";
    
    $verifikasi = $db->fetch($sql, [$verifikasiId]);
    
    if (!$verifikasi) {
        echo json_encode(['success' => false, 'message' => 'Verification data not found']);
        exit();
    }
    
    // Get email settings
    $settingsSql = "SELECT * FROM setting_notifikasi ORDER BY id DESC LIMIT 1";
    $settings = $db->fetch($settingsSql);
    
    if (!$settings || empty($settings['smtp_username']) || empty($settings['smtp_password'])) {
        echo json_encode(['success' => false, 'message' => 'Email settings not configured']);
        exit();
    }
    
    // Check if PHPMailer is available
    if (!file_exists('../vendor/autoload.php')) {
        // Fallback to basic mail function
        $subject = str_replace(
    ['{bidang}', '{nama_pptk}', '{nama_bendahara}', '{sub_kegiatan}', '{nama_rekening_belanja}', 
     '{bulan}', '{tahun}', '{status_verifikasi}', '{tanggal_verifikasi}', '{alasan_tidak_lengkap}', 
     '{keterangan_transaksi}', '{nomor_bku}'],
    [$verifikasi['bidang'], $verifikasi['nama_pptk'], $verifikasi['nama_bendahara'], 
     $verifikasi['sub_kegiatan'], $verifikasi['nama_rekening_belanja'], $verifikasi['bulan'], 
     $verifikasi['tahun'], $verifikasi['status_verifikasi'], $verifikasi['tanggal_verifikasi'],
     $verifikasi['alasan_tidak_lengkap'] ?? '',
     $verifikasi['keterangan_transaksi'] ?? '',   // Tambahan
     $verifikasi['nomor_bku'] ?? ''],             // Tambahan
    $settings['template_subject']
);

$body = str_replace(
    ['{bidang}', '{nama_pptk}', '{nama_bendahara}', '{sub_kegiatan}', '{nama_rekening_belanja}', 
     '{bulan}', '{tahun}', '{status_verifikasi}', '{tanggal_verifikasi}', '{alasan_tidak_lengkap}', 
     '{keterangan_transaksi}', '{nomor_bku}'],
    [$verifikasi['bidang'], $verifikasi['nama_pptk'], $verifikasi['nama_bendahara'], 
     $verifikasi['sub_kegiatan'], $verifikasi['nama_rekening_belanja'], $verifikasi['bulan'], 
     $verifikasi['tahun'], $verifikasi['status_verifikasi'], $verifikasi['tanggal_verifikasi'],
     $verifikasi['alasan_tidak_lengkap'] ? "\nAlasan: " . $verifikasi['alasan_tidak_lengkap'] : '',
     $verifikasi['keterangan_transaksi'] ?? '',   // Tambahan
     $verifikasi['nomor_bku'] ?? ''],             // Tambahan
    $settings['template_body']
);
        
        $headers = "From: " . $settings['from_email'] . "\r\n";
        $headers .= "Reply-To: " . $settings['from_email'] . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        $emailsSent = 0;
        
        // Send to PPTK
        if (!empty($verifikasi['email_pptk'])) {
            if (mail($verifikasi['email_pptk'], $subject, $body, $headers)) {
                $emailsSent++;
            }
        }
        
        // Send to Bendahara
        if (!empty($verifikasi['email_bendahara'])) {
            if (mail($verifikasi['email_bendahara'], $subject, $body, $headers)) {
                $emailsSent++;
            }
        }
        
        if ($emailsSent > 0) {
            // Update email_sent flag
            $updateSql = "UPDATE verifikasi_spj SET email_sent = 1 WHERE id = ?";
            $db->query($updateSql, [$verifikasiId]);
            
            echo json_encode(['success' => true, 'message' => "Email sent to $emailsSent recipients"]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send emails']);
        }
        
    } else {
        // Use PHPMailer
        require_once '../vendor/autoload.php';
            
            $mail = new PHPMailer(true);   // <-- WAJIB ada
        
        // Server settings
                $mail->isSMTP();
                $mail->Host = $settings['smtp_host'];
                $mail->SMTPAuth = true;
                $mail->Username = $settings['smtp_username'];
                $mail->Password = $settings['smtp_password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
                $mail->Port = $settings['smtp_port'];
        
        // Sender
        $mail->setFrom($settings['from_email'], $settings['from_name']);
        
        // Prepare email content
        $subject = str_replace(
    ['{bidang}', '{nama_pptk}', '{nama_bendahara}', '{sub_kegiatan}', '{nama_rekening_belanja}', 
     '{bulan}', '{tahun}', '{status_verifikasi}', '{tanggal_verifikasi}', '{alasan_tidak_lengkap}', 
     '{keterangan_transaksi}', '{nomor_bku}'],
    [$verifikasi['bidang'], $verifikasi['nama_pptk'], $verifikasi['nama_bendahara'], 
     $verifikasi['sub_kegiatan'], $verifikasi['nama_rekening_belanja'], $verifikasi['bulan'], 
     $verifikasi['tahun'], $verifikasi['status_verifikasi'], $verifikasi['tanggal_verifikasi'],
     $verifikasi['alasan_tidak_lengkap'] ?? '',
     $verifikasi['keterangan_transaksi'] ?? '',   // Tambahan
     $verifikasi['nomor_bku'] ?? ''],             // Tambahan
    $settings['template_subject']
);

$body = str_replace(
    ['{bidang}', '{nama_pptk}', '{nama_bendahara}', '{sub_kegiatan}', '{nama_rekening_belanja}', 
     '{bulan}', '{tahun}', '{status_verifikasi}', '{tanggal_verifikasi}', '{alasan_tidak_lengkap}', 
     '{keterangan_transaksi}', '{nomor_bku}'],
    [$verifikasi['bidang'], $verifikasi['nama_pptk'], $verifikasi['nama_bendahara'], 
     $verifikasi['sub_kegiatan'], $verifikasi['nama_rekening_belanja'], $verifikasi['bulan'], 
     $verifikasi['tahun'], $verifikasi['status_verifikasi'], $verifikasi['tanggal_verifikasi'],
     $verifikasi['alasan_tidak_lengkap'] ? "\nAlasan: " . $verifikasi['alasan_tidak_lengkap'] : '',
     $verifikasi['keterangan_transaksi'] ?? '',   // Tambahan
     $verifikasi['nomor_bku'] ?? ''],             // Tambahan
    $settings['template_body']
);

        $body = nl2br($body);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        $emailsSent = 0;
        $errors = [];
        
        // Send to PPTK
        if (!empty($verifikasi['email_pptk'])) {
            try {
                $mail->clearAddresses();
                $mail->addAddress($verifikasi['email_pptk'], $verifikasi['nama_pptk']);
                $mail->send();
                $emailsSent++;
            } catch (Exception $e) {
                $errors[] = "PPTK: " . $mail->ErrorInfo;
            }
        }
        
        // Send to Bendahara
        if (!empty($verifikasi['email_bendahara'])) {
            try {
                $mail->clearAddresses();
                $mail->addAddress($verifikasi['email_bendahara'], $verifikasi['nama_bendahara']);
                $mail->send();
                $emailsSent++;
            } catch (Exception $e) {
                $errors[] = "Bendahara: " . $mail->ErrorInfo;
            }
        }
        
        if ($emailsSent > 0) {
            // Update email_sent flag
            $updateSql = "UPDATE verifikasi_spj SET email_sent = 1 WHERE id = ?";
            $db->query($updateSql, [$verifikasiId]);
            
            $message = "Email sent to $emailsSent recipients";
            if (!empty($errors)) {
                $message .= ". Errors: " . implode(", ", $errors);
            }
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to send emails: ' . implode(", ", $errors)]);
        }
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>