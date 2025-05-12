<?php
// Hata raporlamayı etkinleştir
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantısını içe aktar
require_once('../../config/db.php');

// POST isteği kontrolü
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // JSON cevap formatını ayarla
    header('Content-Type: application/json');
    
    // Form verilerini al
    $tcNumber = $_POST['tc'] ?? '';
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Basit doğrulama
    if (empty($tcNumber) || empty($name) || empty($surname) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Tüm alanlar zorunludur.'
        ]);
        exit;
    }
    
    // TC Kimlik numarası doğrulama
    if (!preg_match('/^\d{11}$/', $tcNumber)) {
        echo json_encode([
            'success' => false,
            'message' => 'Geçerli bir TC kimlik numarası giriniz (11 haneli).'
        ]);
        exit;
    }
    
    try {
        // Önce TC numarasının veritabanında olup olmadığını kontrol et
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM admins WHERE tc_no = ?");
        $checkStmt->execute([$tcNumber]);
        $exists = $checkStmt->fetchColumn();
        
        if ($exists > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Bu TC kimlik numarası ile kayıtlı bir admin zaten var.'
            ]);
            exit;
        }
        
        // Şifreyi hashleme
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Admini veritabanına ekle
        $stmt = $conn->prepare("INSERT INTO admins (tc_no, password, name, surname) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$tcNumber, $hashedPassword, $name, $surname]);
        
        if ($result) {
            echo json_encode([
                'success' => true,
                'message' => 'Admin başarıyla eklendi.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Admin eklenirken bir hata oluştu.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Veritabanı hatası: ' . $e->getMessage()
        ]);
    }
} else {
    // POST isteği değilse hata döndür
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode([
        'success' => false,
        'message' => 'Geçersiz istek yöntemi.'
    ]);
}
?> 