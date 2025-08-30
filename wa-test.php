<?php
$userCode  = "KM864J825"; // User Code dari dashboard
$secretKey = "dc7d7c58e43d24d438f23da75d2b26baf80a087f72c58f906591d86d95316cb5"; // Secret Key dari dashboard
$deviceId  = "D-KRAQE"; // ⚠️ ambil dari dashboard Kirimi.id
$receiver  = "6285722423000"; // bisa juga "+6285722423000"
$message   = "Halo, ini notifikasi WA dari Kirimi.id (localhost PHP)!";

$url = "https://api.kirimi.id/v1/send-message";

$data = [
    "user_code"  => $userCode,
    "device_id"  => $deviceId,
    "receiver"   => $receiver,
    "message"    => $message,
    "secret"     => $secretKey,
    "enableTypingEffect" => true,
    "typingSpeedMs" => 350
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
