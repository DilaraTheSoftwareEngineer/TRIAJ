<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('db.php');

try {
    // SQL dosyasını oku
    $sql = file_get_contents('db.sql');
    
    // Her bir SQL komutunu ayrı ayrı çalıştır
    $queries = explode(';', $sql);
    foreach ($queries as $query) {
        if (trim($query) != '') {
            $conn->exec($query);
            echo "Tablo oluşturuldu veya güncellendi.<br>";
        }
    }
    
    // Örnek admin hesabı oluştur
    $tc = '11111111111';
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $name = 'Admin';
    $surname = 'User';
    
    // Önce admin var mı kontrol et
    $stmt = $conn->prepare("SELECT * FROM admins WHERE tc_no = ?");
    $stmt->execute([$tc]);
    
    if (!$stmt->fetch()) {
        $stmt = $conn->prepare("INSERT INTO admins (tc_no, password, name, surname) VALUES (?, ?, ?, ?)");
        $stmt->execute([$tc, $password, $name, $surname]);
        echo "Admin hesabı oluşturuldu.<br>";
    } else {
        echo "Admin hesabı zaten mevcut.<br>";
    }
    
    echo "<p style='color:green'>✅ Kurulum başarıyla tamamlandı!</p>";
    echo "<p>Admin giriş bilgileri:</p>";
    echo "<ul>";
    echo "<li>TC: 11111111111</li>";
    echo "<li>Şifre: admin123</li>";
    echo "</ul>";
    echo "<p><a href='../kullanicigiris/admin-login.php'>Admin giriş sayfasına git</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color:red'>❌ Hata: " . $e->getMessage() . "</p>";
}
?> 