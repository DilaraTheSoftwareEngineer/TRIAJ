<?php
require_once('../config/db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tcNumber = $_POST['tc'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $birthdate = $_POST['birthdate'];
    
    // TC Kimlik kontrolü
    if (!validateTCNumber($tcNumber)) {
        echo json_encode(['success' => false, 'error' => 'Geçerli bir TC kimlik numarası giriniz (11 haneli)']);
        exit;
    }
    
    try {
        // TC numarası daha önce kullanılmış mı kontrolü
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM patients WHERE tc_no = ?");
        $checkStmt->execute([$tcNumber]);
        if ($checkStmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'error' => 'Bu TC kimlik numarası ile daha önce kayıt yapılmış']);
            exit;
        }
        
        // Yeni kullanıcı kaydı
        $stmt = $conn->prepare("INSERT INTO patients (tc_no, name, surname, birthdate) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tcNumber, $name, $surname, $birthdate]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Kayıt işlemi başarısız']);
    }
}

function validateTCNumber($tcNumber) {
    // TC kimlik numarası doğrulama fonksiyonu (login.php ile aynı)
    if (!preg_match('/^\d{11}$/', $tcNumber) || $tcNumber[0] === '0') {
        return false;
    }
    
    $oddSum = 0;
    $evenSum = 0;
    $total = 0;
    
    for ($i = 0; $i < 9; $i++) {
        $total += intval($tcNumber[$i]);
        if ($i % 2 === 0) {
            $oddSum += intval($tcNumber[$i]);
        } else {
            $evenSum += intval($tcNumber[$i]);
        }
    }
    
    $tenthDigit = ($oddSum * 7 - $evenSum) % 10;
    if ($tenthDigit < 0) $tenthDigit += 10;
    
    if ($tenthDigit !== intval($tcNumber[9])) {
        return false;
    }
    
    if (($total + intval($tcNumber[9])) % 10 !== intval($tcNumber[10])) {
        return false;
    }
    
    return true;
}
?>