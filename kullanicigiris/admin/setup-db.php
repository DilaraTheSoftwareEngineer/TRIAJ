<?php
// Hata Ayıklama
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Veritabanı bağlantı bilgileri
$servername = "localhost";
$username = "root";
$password = "";
$database = "triaj_db";

echo "<h1>Triaj Sistemi Veritabanı Kurulumu</h1>";
echo "<p>Veritabanı ve tabloların kurulumu başlatılıyor...</p>";

try {
    // PDO bağlantısı oluştur
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Veritabanını oluştur (eğer yoksa)
    $conn->exec("CREATE DATABASE IF NOT EXISTS $database");
    echo "<p>✅ Veritabanı oluşturuldu veya zaten var: $database</p>";
    
    // Veritabanını seç
    $conn->exec("USE $database");
    
    // Patients tablosu
    $sql_patients = "CREATE TABLE IF NOT EXISTS patients (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tc_no VARCHAR(11) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(50) NOT NULL,
        surname VARCHAR(50) NOT NULL,
        birthdate DATE,
        gender ENUM('Erkek', 'Kadın', 'Diğer'),
        phone VARCHAR(20),
        email VARCHAR(100),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql_patients);
    echo "<p>✅ Patients tablosu oluşturuldu veya zaten var</p>";
    
    // Diseases (hastalıklar/şikayetler) tablosu
    $sql_diseases = "CREATE TABLE IF NOT EXISTS diseases (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tc_no VARCHAR(11) NOT NULL,
        disease VARCHAR(255) NOT NULL,
        disease_details TEXT,
        triage_level ENUM('Düşük', 'Orta', 'Yüksek', 'Çok Yüksek') DEFAULT 'Düşük',
        status ENUM('Beklemede', 'İnceleniyor', 'Tamamlandı') DEFAULT 'Beklemede',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tc_no) REFERENCES patients(tc_no) ON DELETE CASCADE
    )";
    $conn->exec($sql_diseases);
    echo "<p>✅ Diseases tablosu oluşturuldu veya zaten var</p>";
    
    // Admins tablosu
    $sql_admins = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tc_no VARCHAR(11) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(50),
        surname VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($sql_admins);
    echo "<p>✅ Admins tablosu oluşturuldu veya zaten var</p>";
    
    // Test admin kullanıcısı ekle (şifre: admin123)
    $admin_tc = "11111111111";
    $admin_password = password_hash("admin123", PASSWORD_DEFAULT);
    
    // Önce mevcut admin kontrolü yap
    $check_admin = $conn->prepare("SELECT * FROM admins WHERE tc_no = ?");
    $check_admin->execute([$admin_tc]);
    
    if ($check_admin->rowCount() == 0) {
        $insert_admin = $conn->prepare("INSERT INTO admins (tc_no, password, name, surname) VALUES (?, ?, ?, ?)");
        $insert_admin->execute([$admin_tc, $admin_password, "Admin", "Kullanıcı"]);
        echo "<p>✅ Test admin kullanıcısı oluşturuldu</p>";
    } else {
        echo "<p>✅ Test admin kullanıcısı zaten var</p>";
    }
    
    // Test hasta kullanıcısı ekle (şifre: test123)
    $patient_tc = "22222222222";
    $patient_password = password_hash("test123", PASSWORD_DEFAULT);
    
    // Önce mevcut hasta kontrolü yap
    $check_patient = $conn->prepare("SELECT * FROM patients WHERE tc_no = ?");
    $check_patient->execute([$patient_tc]);
    
    if ($check_patient->rowCount() == 0) {
        $insert_patient = $conn->prepare("INSERT INTO patients (tc_no, password, name, surname, birthdate, gender) VALUES (?, ?, ?, ?, ?, ?)");
        $insert_patient->execute([$patient_tc, $patient_password, "Test", "Hasta", "1990-01-01", "Erkek"]);
        echo "<p>✅ Test hasta kullanıcısı oluşturuldu</p>";
        
        // Test hastasına örnek şikayet ekle
        $insert_complaint = $conn->prepare("INSERT INTO diseases (tc_no, disease, disease_details, triage_level) VALUES (?, ?, ?, ?)");
        $insert_complaint->execute([$patient_tc, "Ateş ve Öksürük", "İki gündür 38 derece ateş ve kuru öksürük var.", "Orta"]);
        echo "<p>✅ Test hastasına örnek şikayet eklendi</p>";
    } else {
        echo "<p>✅ Test hasta kullanıcısı zaten var</p>";
    }
    
    echo "<h2>Kurulum Tamamlandı! ✅</h2>";
    echo "<p>Admin giriş bilgileri:</p>";
    echo "<ul>";
    echo "<li>TC: 11111111111</li>";
    echo "<li>Şifre: admin123</li>";
    echo "</ul>";
    
    echo "<p>Test hasta giriş bilgileri:</p>";
    echo "<ul>";
    echo "<li>TC: 22222222222</li>";
    echo "<li>Şifre: test123</li>";
    echo "</ul>";
    
    echo "<p><a href='../admin-login.php'>Admin Giriş Sayfasına Git</a></p>";
    
} catch(PDOException $e) {
    echo "<div style='color: red; padding: 20px; background-color: #ffeeee; border: 1px solid #ffcccc;'>";
    echo "<h2>Hata Oluştu!</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?> 