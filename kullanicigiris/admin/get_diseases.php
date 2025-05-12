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
    // Hastalıkları çek - patients tablosunu kullan
    $query = "SELECT d.*, p.name, p.surname, p.birthdate
              FROM diseases d 
              LEFT JOIN patients p ON d.tc_no = p.tc_no 
              ORDER BY d.created_at DESC";
    
    $stmt = $conn->query($query);
    $diseases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // JSON olarak döndür
    header('Content-Type: application/json');
    echo json_encode($diseases);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Veritabanı hatası: ' . $e->getMessage()]);
}
?> 