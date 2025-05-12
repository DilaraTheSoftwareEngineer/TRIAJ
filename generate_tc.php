<?php

function generateValidTCNumber() {
    // İlk 9 hanenin rastgele oluşturulması (ilk hane 0 olamaz)
    $firstNine = rand(1, 9); // İlk hane 1-9 arası rastgele
    
    for ($i = 1; $i < 9; $i++) {
        $firstNine .= rand(0, 9); // Kalan 8 hane 0-9 arası rastgele
    }
    
    // 10. haneyi hesapla
    // (1, 3, 5, 7, 9. rakamların toplamının 7 katı ile 2, 4, 6, 8. rakamların toplamı çıkartıldığında
    // elde edilen sonucun 10'a bölümünden kalan, 10. rakamı verir)
    $oddSum = 0;
    $evenSum = 0;
    
    for ($i = 0; $i < 9; $i++) {
        if ($i % 2 == 0) { // 1, 3, 5, 7, 9. rakamlar (index 0, 2, 4, 6, 8)
            $oddSum += (int)$firstNine[$i];
        } else { // 2, 4, 6, 8. rakamlar (index 1, 3, 5, 7)
            $evenSum += (int)$firstNine[$i];
        }
    }
    
    $tenthDigit = ($oddSum * 7 - $evenSum) % 10;
    if ($tenthDigit < 0) $tenthDigit += 10;
    
    // 11. haneyi hesapla (ilk 10 hanenin toplamının 10'a bölümünden kalan)
    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
        $sum += (int)$firstNine[$i];
    }
    $sum += $tenthDigit;
    
    $eleventhDigit = $sum % 10;
    
    // Tam TC numarasını döndür
    return $firstNine . $tenthDigit . $eleventhDigit;
}

$validTC = generateValidTCNumber();
echo "Oluşturulan geçerli TC: " . $validTC . "\n";

// Hash'lenmiş bir şifre oluştur
$password = 'admin123';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
echo "Şifre: " . $password . "\n";
echo "Hash'lenmiş şifre: " . $hashedPassword . "\n";

// SQL sorgusunu oluştur
$sql = "INSERT INTO admins (tc_no, password, name, surname) VALUES ('$validTC', '$hashedPassword', 'Admin', 'Kullanıcı');";
echo "\nVeritabanına eklemek için SQL sorgusu:\n" . $sql . "\n";
?> 