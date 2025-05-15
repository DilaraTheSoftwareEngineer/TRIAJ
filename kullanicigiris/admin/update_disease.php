<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Oturum kontrolü
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Yetkisiz erişim']);
    exit;
}

// JSON verisini al
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Geçersiz veri']);
    exit;
}

// Durum değeri doğrulama
$validStatuses = ['bekliyor', 'inceleniyor', 'tamamlandi', 'iptal'];
if (!in_array($data['status'], $validStatuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Geçersiz durum değeri: ' . $data['status']]);
    exit;
}

require_once('../../config/db.php');

try {
    // Durumu güncelle
    $stmt = $conn->prepare("UPDATE diseases SET status = ? WHERE id = ?");
    $result = $stmt->execute([$data['status'], $data['id']]);
    
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Güncelleme başarısız']);
    }
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?> 