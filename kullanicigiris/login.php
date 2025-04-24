<?php
require_once('../config/db.php');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tcNumber = $_POST['tc'];
    
    // TC Kimlik kontrolü
    if (!validateTCNumber($tcNumber)) {
        echo json_encode(['success' => false, 'error' => 'Geçerli bir TC kimlik numarası giriniz (11 haneli)']);
        exit;
    }
    
    try {
        $stmt = $conn->prepare("SELECT * FROM patients WHERE tc_no = ?");
        $stmt->execute([$tcNumber]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            session_start();
            $_SESSION['user'] = $user;
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Bu TC kimlik numarasına ait kayıt bulunamadı']);
        }
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Giriş işlemi başarısız']);
    }
}

function validateTCNumber($tcNumber) {
    // TC kimlik numarası doğrulama fonksiyonu
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