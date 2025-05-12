<?php
require_once('../config/db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tc = $_POST['tc'];
    
    try {
        $stmt = $conn->prepare("DELETE FROM patients WHERE tc_no = ?");
        $stmt->execute([$tc]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Kullanıcı bulunamadı']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Kullanıcı silinirken bir hata oluştu']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Geçersiz istek']);
}
?> 