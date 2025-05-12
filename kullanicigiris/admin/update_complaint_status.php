<?php
require_once('../config/db.php');
header('Content-Type: application/json');

// Yönetici kontrolü
session_start();
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Yetkisiz erişim!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $status = $data['status'] ?? null;
    
    if (!$id || !$status) {
        echo json_encode(['success' => false, 'message' => 'Eksik parametre!']);
        exit;
    }
    
    // Durum değeri doğrulama
    $validStatuses = ['bekliyor', 'inceleniyor', 'tamamlandi'];
    if (!in_array($status, $validStatuses)) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz durum değeri!']);
        exit;
    }
    
    try {
        $stmt = $conn->prepare("UPDATE diseases SET status = ? WHERE id = ?");
        $result = $stmt->execute([$status, $id]);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Durum başarıyla güncellendi']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Durum güncellenemedi!']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek!']);
}
?> 