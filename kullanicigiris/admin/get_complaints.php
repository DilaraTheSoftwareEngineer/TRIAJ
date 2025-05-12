<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Oturum kontrolü
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Yetkisiz erişim']);
    exit;
}

require_once('../../config/db.php');

try {
    // Şikayetleri çek - patients tablosunu kullan
    $query = "SELECT c.*, p.name, p.surname, p.birthdate
              FROM complaints c 
              LEFT JOIN patients p ON c.tc_no = p.tc_no 
              ORDER BY c.created_at DESC";
    
    $stmt = $conn->query($query);
    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // JSON olarak döndür
    header('Content-Type: application/json');
    echo json_encode($complaints);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?>