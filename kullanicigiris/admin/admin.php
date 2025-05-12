<?php
// Session kontrolü ekleyelim
session_start();

// Session varsa devam et, yoksa login sayfasına yönlendir
/*
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header('Location: ../admin-login.php');
    exit;
}
*/
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .card {
            transition: all 0.3s ease;
        }
        .welcome-text {
            font-size: 1.2rem;
            color: #555;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h1 class="h3 mb-3">Admin Kontrol Paneli</h1>
                            <p class="welcome-text">Hoş geldiniz, <?php echo isset($_SESSION['admin_tc']) ? $_SESSION['admin_tc'] : 'Admin'; ?></p>
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <a href="javascript:void(0)" onclick="goDashboard()" class="text-decoration-none w-100">
                                    <div class="card h-100 border-0 shadow-sm card-hover">
                                        <div class="card-body text-center p-4">
                                            <i class="bi bi-clipboard-pulse fs-1 text-primary mb-3"></i>
                                            <h5 class="card-title">Şikayetler</h5>
                                            <p class="card-text text-muted">Hasta şikayetlerini ve durumlarını görüntüleyin.</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            
                            <div class="col-md-6">
                                <a href="javascript:void(0)" onclick="goDashboard()" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm card-hover">
                                        <div class="card-body text-center p-4">
                                            <i class="bi bi-people fs-1 text-success mb-3"></i>
                                            <h5 class="card-title">Hastalar</h5>
                                            <p class="card-text text-muted">Kayıtlı hastaların listesini görüntüleyin.</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <a href="logout.php" class="btn btn-outline-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Çıkış Yap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function goDashboard() {
            // Tüm sayfayı yeniden yükle dashboard.php'ye giderek
            window.location.href = 'dashboard.php';
        }
        
        // Sayfa yüklendiğinde direkt Dashboard'a yönlendirme butonu
        document.addEventListener('DOMContentLoaded', function() {
            // Dashboard sayfasına manuel bir buton ekleyelim
            const container = document.querySelector('.container');
            const manualButton = document.createElement('div');
            manualButton.classList.add('text-center', 'mt-4');
            manualButton.innerHTML = '<a href="dashboard.php" class="btn btn-primary">Dashboard\'a Git</a>';
            container.appendChild(manualButton);
        });
    </script>
</body>
</html> 