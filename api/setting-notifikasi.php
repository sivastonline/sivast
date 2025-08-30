<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require_once '../config/database.php';
session_start();

header('Content-Type: application/json');

// Cek login & role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Ambil action (dari GET, POST, atau JSON body)
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if (empty($action)) {
    $rawInput = json_decode(file_get_contents('php://input'), true);
    if (isset($rawInput['action'])) {
        $action = $rawInput['action'];
        $_POST = $rawInput; // supaya $_POST juga bisa dipakai
    }
}

// Helper function: ambil input JSON atau POST biasa
function getInput() {
    $json = json_decode(file_get_contents('php://input'), true);
    if ($json && is_array($json)) {
        return $json;
    }
    return $_POST; // fallback kalau dikirim via form biasa
}

try {
    switch ($action) {

        case 'get':
            $sql = "SELECT * FROM setting_notifikasi ORDER BY id DESC LIMIT 1";
            $data = $db->fetch($sql);

            if ($data) {
                unset($data['smtp_password']); // jangan kirim password
                echo json_encode(['success' => true, 'data' => $data]);
            } else {
                // default value
                $defaultData = [
                    'smtp_host' => '',
                    'smtp_port' => '',
                    'smtp_username' => '',
                    'from_email' => '',
                    'from_name' => 'SIVAST - BKPSDM Kabupaten Bandung',
                    'template_subject' => 'Notifikasi Verifikasi SPJ - {bidang}',
                    'template_body' => "Yth. Bapak/Ibu {nama_pptk} dan {nama_bendahara},<br><br>" .
                                    "Dengan hormat,<br><br>" .
                                    "Berikut adalah informasi verifikasi SPJ untuk:<br>" .
                                    "- Bidang: {bidang}<br>" .
                                    "- Sub Kegiatan: {sub_kegiatan}<br>" .
                                    "- Rekening Belanja: {nama_rekening_belanja}<br>" .
                                    "- Nomor BKU: {nomor_bku}<br>" .                        // <-- Tambahan
                                    "- Keterangan Transaksi: {keterangan_transaksi}<br>" .  // <-- Tambahan
                                    "- Bulan: {bulan} {tahun}<br>" .
                                    "- Status: {status_verifikasi}<br>" .
                                    "- Tanggal Verifikasi: {tanggal_verifikasi}<br><br>" .
                                    "{alasan_tidak_lengkap}<br><br>" .
                                    "Mohon segera ditindaklanjuti sesuai dengan ketentuan yang berlaku.<br><br>" .
                                    "Terima kasih atas perhatiannya.<br><br>" .
                                    "Hormat kami,<br>SIVAST - BKPSDM Kabupaten Bandung"
                ];
                echo json_encode(['success' => true, 'data' => $defaultData]);
            }
            break;

        case 'save_smtp':
            $input = getInput();
            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Data tidak terkirim']);
                exit();
            }

            $existing = $db->fetch("SELECT id FROM setting_notifikasi LIMIT 1");

            if ($existing) {
                $sql = "UPDATE setting_notifikasi 
                        SET smtp_host = ?, smtp_port = ?, smtp_username = ?, 
                            smtp_password = ?, from_email = ?, from_name = ? 
                        WHERE id = ?";
                $params = [
                    $input['smtp_host'] ?? '',
                    $input['smtp_port'] ?? '',
                    $input['smtp_username'] ?? '',
                    $input['smtp_password'] ?? '',
                    $input['from_email'] ?? '',
                    $input['from_name'] ?? '',
                    $existing['id']
                ];
            } else {
                $sql = "INSERT INTO setting_notifikasi 
                        (smtp_host, smtp_port, smtp_username, smtp_password, from_email, from_name) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $params = [
                    $input['smtp_host'] ?? '',
                    $input['smtp_port'] ?? '',
                    $input['smtp_username'] ?? '',
                    $input['smtp_password'] ?? '',
                    $input['from_email'] ?? '',
                    $input['from_name'] ?? ''
                ];
            }

            try {
                $db->query($sql, $params);
                echo json_encode(['success' => true, 'message' => 'SMTP settings saved successfully']);
            } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Email could not be sent.',
                        'error' => $e->getMessage(),
                        'mailer_error' => $mail->ErrorInfo
                    ]);
                }
            break;

        case 'save_template':
            $input = getInput();
            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Data tidak terkirim']);
                exit();
            }

            $existing = $db->fetch("SELECT id FROM setting_notifikasi LIMIT 1");

            if ($existing) {
                $sql = "UPDATE setting_notifikasi 
                        SET template_subject = ?, template_body = ? 
                        WHERE id = ?";
                $params = [
                    $input['template_subject'] ?? '',
                    $input['template_body'] ?? '',
                    $existing['id']
                ];
            } else {
                $sql = "INSERT INTO setting_notifikasi (template_subject, template_body) VALUES (?, ?)";
                $params = [
                    $input['template_subject'] ?? '',
                    $input['template_body'] ?? ''
                ];
            }

            try {
                $db->query($sql, $params);
                echo json_encode(['success' => true, 'message' => 'Template saved successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'SQL Error: ' . $e->getMessage()]);
            }
            break;

        case 'test_email':
            $input = getInput();
            if (!$input) {
                echo json_encode(['success' => false, 'message' => 'Data tidak terkirim']);
                exit();
            }

            $settings = $db->fetch("SELECT * FROM setting_notifikasi ORDER BY id DESC LIMIT 1");

            if (!$settings || empty($settings['smtp_username']) || empty($settings['smtp_password'])) {
                echo json_encode(['success' => false, 'message' => 'SMTP settings not configured']);
                exit();
            }

            require_once '../vendor/autoload.php';
            
            $mail = new PHPMailer(true);   // <-- WAJIB ada

            try {
                // Server settings
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                    $mail->Debugoutput = function($str, $level) {
                        error_log("SMTP DEBUG [$level]: $str");
                    };
                $mail->isSMTP();
                $mail->Host = $settings['smtp_host'];
                $mail->SMTPAuth = true;
                $mail->Username = $settings['smtp_username'];
                $mail->Password = $settings['smtp_password'];
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
                $mail->Port = $settings['smtp_port'];

                // Recipients
                $mail->setFrom($settings['from_email'], $settings['from_name']);
                $mail->addAddress($input['test_email']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = $input['test_subject'] ?? 'Test Email';
                $mail->Body = $input['test_message'] ?? 'This is a test email from SIVAST.';

                $mail->send();
                echo json_encode(['success' => true, 'message' => 'Test email sent successfully']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Email could not be sent. Error: ' . $mail->ErrorInfo]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Fatal Error: ' . $e->getMessage()]);
}
