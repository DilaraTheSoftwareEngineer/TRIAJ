<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Hata log dosyasını ayarla
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

require_once('../config/db.php');

// Gelen isteği logla
error_log("Gelen istek metodu: " . $_SERVER['REQUEST_METHOD']);
error_log("POST verileri: " . print_r($_POST, true));

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tc_no = $_SESSION['user']['tc_no'] ?? null;
    $diseases = json_decode($_POST['diseases'], true);
    $description = $_POST['description'] ?? '';
    $urgencyLevel = $_POST['urgencyLevel'] ?? 'yesil';
    
    if (!$tc_no) {
        echo json_encode(['success' => false, 'error' => 'Oturum bulunamadı']);
        exit;
    }
    
    try {
        $conn->beginTransaction();
        
        // Her bir hastalığı kaydet
        $stmt = $conn->prepare("INSERT INTO diseases (tc_no, disease_name, urgency_level, description) VALUES (?, ?, ?, ?)");
        
        foreach ($diseases as $disease) {
            $stmt->execute([$tc_no, $disease, $urgencyLevel, $description]);
        }
        
        $conn->commit();
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => 'Kayıt sırasında bir hata oluştu']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Geçersiz istek']);
}
?>