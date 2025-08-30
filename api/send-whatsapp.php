<?php
header('Content-Type: application/json');

// Koneksi ke database menggunakan PDO
$host = 'localhost'; // ganti dengan host database Anda
$dbname = 'sivast'; // ganti dengan nama database Anda
$username = 'root'; // ganti dengan username database Anda
$password = ''; // ganti dengan password database Anda

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Koneksi gagal: " . $e->getMessage()]);
    exit;
}

// Ambil data POST
$data = json_decode(file_get_contents("php://input"), true);
$verifikasiId = $data['verifikasi_id'] ?? null;

if (!$verifikasiId) {
    echo json_encode(["success" => false, "message" => "ID verifikasi tidak ada"]);
    exit;
}

// Ambil data dari verifikasi_spj berdasarkan verifikasiId
$sql = "SELECT * FROM verifikasi_spj WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$verifikasiId]);
$verifikasiData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$verifikasiData) {
    echo json_encode(["success" => false, "message" => "Data verifikasi tidak ditemukan"]);
    exit;
}

// Ambil nomor hp PPTK dan Bendahara berdasarkan sub_kegiatan dan nama_rekening_belanja
$subKegiatan = $verifikasiData['sub_kegiatan'];
$rekeningBelanja = $verifikasiData['nama_rekening_belanja'];

// Query master_data untuk mendapatkan wa_pptk, wa_bendahara, nama_pptk, dan nama_bendahara
$sql = "SELECT wa_pptk, wa_bendahara, nama_pptk, nama_bendahara 
        FROM master_data 
        WHERE sub_kegiatan = ? AND nama_rekening_belanja = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$subKegiatan, $rekeningBelanja]);
$masterData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$masterData) {
    echo json_encode(["success" => false, "message" => "Data master tidak ditemukan"]);
    exit;
}

// Ambil nomor WA PPTK dan Bendahara
$pptkPhone = $masterData['wa_pptk'];
$bendaharaPhone = $masterData['wa_bendahara'];

// Ambil nama PPTK dan Bendahara
$namaPptk = $masterData['nama_pptk'] ?? 'PPTK Tidak Diketahui';
$namaBendahara = $masterData['nama_bendahara'] ?? 'Bendahara Tidak Diketahui';

// Template pesan
$message = "
Yth. Bapak/Ibu {$namaPptk} dan {$namaBendahara},

Dengan hormat,

Berikut adalah informasi verifikasi SPJ untuk:

- *Bidang :* {$verifikasiData['bidang']}
- *Sub Kegiatan :* {$verifikasiData['sub_kegiatan']}
- *Rekening Belanja :* {$verifikasiData['nama_rekening_belanja']}
- *Nomor BKU :* {$verifikasiData['nomor_bku']}
- *Keterangan Transaksi :* {$verifikasiData['keterangan_transaksi']}
- *Bulan:* {$verifikasiData['bulan']} {$verifikasiData['tahun']}
- *Status:* {$verifikasiData['status_verifikasi']}
- *Tanggal Verifikasi:* {$verifikasiData['tanggal_verifikasi']}

*Alasan Belum Lengkap:*
{$verifikasiData['alasan_tidak_lengkap']}

Mohon segera ditindaklanjuti sesuai dengan ketentuan yang berlaku.

Terima kasih atas perhatiannya.

Hormat kami,
Sub Bagian Keuangan Sekretariat BKPSDM Kab Bandung

*Pesan ini dikirim menggunakan SIVAST Online - BKPSDM Kabupaten Bandung (Tidak untuk dibalas)*";

// Kirim WA via Kirimi.id
function sendWA($phone, $message) {
    $url = "https://api.kirimi.id/v1/send-message";
    $payload = [
        "user_code"  => "KM864J825",   // ganti dengan milikmu
        "device_id"  => "D-KRAQE",     // dari dashboard Kirimi.id
        "receiver"   => $phone,
        "message"    => $message,
        "secret"     => "dc7d7c58e43d24d438f23da75d2b26baf80a087f72c58f906591d86d95316cb5"  // ganti dengan secret key kamu
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

// Kirim ke dua orang
$res1 = sendWA($pptkPhone, $message);
$res2 = sendWA($bendaharaPhone, $message);

echo json_encode([
    "success" => true,
    "message" => "WhatsApp terkirim",
    "res_pptk" => $res1,
    "res_bendahara" => $res2
]);
?>
