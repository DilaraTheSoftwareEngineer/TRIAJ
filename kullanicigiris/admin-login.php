<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Debug için request bilgilerini logla
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Script Name: " . $_SERVER['SCRIPT_NAME']);

require_once('../config/db.php');

// Session ayarlarını yapılandır
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);

session_start();
error_log("Session started - Session ID: " . session_id());
error_log("Current Session Data: " . print_r($_SESSION, true));

// Session ID'yi yenile (session fixation saldırılarına karşı)
if (!isset($_SESSION['initiated'])) {
    error_log("Regenerating session ID - New session");
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// POST işlemi varsa application/json header'ı ekle
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Processing POST request");
    header('Content-Type: application/json');
    
    $tcNumber = $_POST['tc'] ?? '';
    $password = $_POST['password'] ?? '';
    
    error_log("Login attempt - TC: " . $tcNumber);
    
    // TC Kimlik kontrolü
    if (!validateTCNumber($tcNumber)) {
        error_log("TC validation failed for: " . $tcNumber);
        echo json_encode(['success' => false, 'error' => 'Geçerli bir TC kimlik numarası giriniz (11 haneli)']);
        exit;
    }
    
    try {
        error_log("Attempting database query for TC: " . $tcNumber);
        $stmt = $conn->prepare("SELECT * FROM admins WHERE tc_no = ?");
        $stmt->execute([$tcNumber]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin && password_verify($password, $admin['password'])) {
            error_log("Login successful - Setting up session for TC: " . $tcNumber);
            
            // Session'ı yenile
            session_regenerate_id(true);
            
            $_SESSION['admin'] = $admin;
            $_SESSION['isAdmin'] = true;
            $_SESSION['admin_tc'] = $tcNumber;
            $_SESSION['last_activity'] = time();
            
            error_log("Session data after successful login: " . print_r($_SESSION, true));
            echo json_encode(['success' => true]);
        } else {
            error_log("Login failed - Invalid credentials for TC: " . $tcNumber);
            echo json_encode(['success' => false, 'error' => 'TC Kimlik numarası veya şifre hatalı!']);
        }
    } catch(PDOException $e) {
        error_log("Database error during login: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Giriş işlemi başarısız: ' . $e->getMessage()]);
    }
    exit;
}

function validateTCNumber($tcNumber) {
    // Basit doğrulama yeterli - sadece 11 haneli sayı kontrolü
    return preg_match('/^\d{11}$/', $tcNumber);
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h3 class="text-center mb-4">Admin Girişi</h3>
                        <div id="alertBox" class="alert alert-danger d-none" role="alert">
                            <!-- Hata mesajı buraya gelecek -->
                        </div>
                        <form id="adminLoginForm">
                            <div class="mb-3">
                                <label for="tc" class="form-label">TC Kimlik No</label>
                                <input type="text" class="form-control" id="tc" name="tc" required maxlength="11">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" id="submitBtn" class="btn btn-primary">Giriş Yap</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <a href="../index.html" class="text-decoration-none">Ana Sayfaya Dön</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('adminLoginForm');
            const alertBox = document.getElementById('alertBox');
            const submitBtn = document.getElementById('submitBtn');
            
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Buton durumunu güncelle
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Giriş yapılıyor...';
                
                // Alert kutusunu gizle
                alertBox.classList.add('d-none');
                
                const formData = new FormData(this);
                
                fetch('admin-login.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Başarılı giriş - doğrudan dashboard sayfasına yönlendir
                        // Tam yolu kullanarak yönlendirme yapıyoruz
                        window.location.href = 'http://localhost/triajGuncellenmis/kullanicigiris/admin/dashboard.php';
                    } else {
                        // Hata mesajını göster
                        alertBox.textContent = data.error || 'Giriş başarısız! Lütfen bilgilerinizi kontrol edin.';
                        alertBox.classList.remove('d-none');
                        
                        // Buton durumunu sıfırla
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Giriş Yap';
                    }
                })
                .catch(error => {
                    console.error('Hata:', error);
                    alertBox.textContent = 'Sunucu hatası! Lütfen daha sonra tekrar deneyin.';
                    alertBox.classList.remove('d-none');
                    
                    // Buton durumunu sıfırla
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Giriş Yap';
                });
            });
        });
    </script>
</body>
</html>