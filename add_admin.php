<?php
require_once('config/db.php');

$tcNumber = '30534748970';
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$name = 'Admin';
$surname = 'Kullanıcı';

try {
    $stmt = $conn->prepare("INSERT INTO admins (tc_no, password, name, surname) VALUES (?, ?, ?, ?)");
    $result = $stmt->execute([$tcNumber, $hashedPassword, $name, $surname]);
    
    if ($result) {
        echo "Admin başarıyla eklendi!\n";
        echo "TC No: $tcNumber\n";
        echo "Şifre: $password\n";
    } else {
        echo "Admin eklenirken bir hata oluştu.\n";
    }
} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?> 