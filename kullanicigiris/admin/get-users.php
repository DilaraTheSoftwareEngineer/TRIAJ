<?php
require_once('../config/db.php');
header('Content-Type: application/json');

try {
    $stmt = $conn->prepare("SELECT tc_no as tc, name, surname, birthdate FROM patients ORDER BY name, surname");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(['success' => true, 'users' => $users]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Kullanıcılar yüklenirken bir hata oluştu']);
}
?> 