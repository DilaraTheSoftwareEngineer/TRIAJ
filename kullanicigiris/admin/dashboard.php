<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Tüm session işlemlerini geçici olarak devre dışı bırakıyoruz
/*
error_log("Dashboard page loaded");
session_start();
error_log("Session started in dashboard. Session ID: " . session_id());
error_log("Current session data in dashboard: " . print_r($_SESSION, true));

// Yönlendirme döngüsünü kırmak için - session kontrolünü düzeltiyoruz
$admin_tc = isset($_SESSION['admin_tc']) ? $_SESSION['admin_tc'] : '';
$is_admin = isset($_SESSION['isAdmin']) ? $_SESSION['isAdmin'] : false;
*/

// Geçici değerler atayalım
$admin_tc = '30534748970';
$is_admin = true;

// Session'ı güncelle
//$_SESSION['last_activity'] = time();

//error_log("Admin accessed dashboard - TC: " . $admin_tc);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Şikayetler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        .description-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .description-text:hover {
            white-space: normal;
            overflow: visible;
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.35em 0.65em;
        }
        .urgency-kirmizi {
            background-color: #f8d7da;
            color: #842029;
            border-left: 4px solid #dc3545;
        }
        .urgency-sari {
            background-color: #fff3cd;
            color: #664d03;
            border-left: 4px solid #ffc107;
        }
        .urgency-yesil {
            background-color: #d1e7dd;
            color: #0f5132;
            border-left: 4px solid #198754;
        }
        .nav-tabs .nav-link {
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            color: #0d6efd;
            font-weight: bold;
        }
        .nav-link.active {
            background-color: #0d6efd !important;
            color: white !important;
        }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="admin.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" id="complaints-tab">Şikayetler</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" id="diseases-tab">Hastalıklar</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Çıkış
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Şikayetler Bölümü -->
        <div id="complaints-section" class="section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Hasta Şikayetleri</h2>
                <div>
                    <button class="btn btn-success" onclick="refreshComplaints()">
                        <i class="bi bi-arrow-clockwise"></i> Yenile
                    </button>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="complaints-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>TC Kimlik No</th>
                                    <th>Ad Soyad</th>
                                    <th>Şikayet</th>
                                    <th>Açıklama</th>
                                    <th>Aciliyet</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="complaints-tbody">
                                <!-- Veriler JavaScript ile doldurulacak -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hastalıklar Bölümü -->
        <div id="diseases-section" class="section d-none">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Hasta Hastalıkları</h2>
                <div>
                    <button class="btn btn-success" onclick="refreshDiseases()">
                        <i class="bi bi-arrow-clockwise"></i> Yenile
                    </button>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="diseases-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>TC Kimlik No</th>
                                    <th>Ad Soyad</th>
                                    <th>Hastalık</th>
                                    <th>Açıklama</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody id="diseases-tbody">
                                <!-- Veriler JavaScript ile doldurulacak -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Şikayet Detay Modal -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hasta Şikayet Detayı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="diseaseDetails"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                    <button type="button" id="updateStatusBtn" class="btn btn-primary">Durumu Güncelle</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Ekleme Modal -->
    <div class="modal fade" id="addAdminModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Admin Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addAdminForm">
                        <div class="mb-3">
                            <label for="admin-tc" class="form-label">TC Kimlik No</label>
                            <input type="text" class="form-control" id="admin-tc" name="tc" required maxlength="11" pattern="[0-9]{11}">
                            <div class="form-text">11 haneli TC kimlik numarası giriniz.</div>
                        </div>
                        <div class="mb-3">
                            <label for="admin-name" class="form-label">Ad</label>
                            <input type="text" class="form-control" id="admin-name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="admin-surname" class="form-label">Soyad</label>
                            <input type="text" class="form-control" id="admin-surname" name="surname" required>
                        </div>
                        <div class="mb-3">
                            <label for="admin-password" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="admin-password" name="password" required>
                        </div>
                        <div id="admin-form-message" class="alert alert-danger d-none"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" id="saveAdminBtn" class="btn btn-primary">Kaydet</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap ve diğer JS dosyaları -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentDisease = null;
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        
        function formatDate(dateString) {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('tr-TR', options);
        }
        
        function getStatusText(status) {
            switch(status) {
                case 'bekliyor': return 'Bekliyor';
                case 'inceleniyor': return 'İnceleniyor';
                case 'tamamlandi': return 'Tamamlandı';
                default: return status;
            }
        }
        
        function getStatusClass(status) {
            switch(status) {
                case 'bekliyor': return 'bg-warning';
                case 'inceleniyor': return 'bg-info';
                case 'tamamlandi': return 'bg-success';
                default: return 'bg-secondary';
            }
        }
        
        function getNextStatus(currentStatus) {
            switch(currentStatus) {
                case 'bekliyor': return 'inceleniyor';
                case 'inceleniyor': return 'tamamlandi';
                case 'tamamlandi': return 'bekliyor';
                default: return 'bekliyor';
            }
        }

        function showDiseaseDetails(disease) {
            currentDisease = disease;
            const detailsDiv = document.getElementById('diseaseDetails');
            
            const urgencyClass = disease.urgency_level === 'kirmizi' ? 'danger' : 
                               disease.urgency_level === 'sari' ? 'warning' : 'success';
            
            detailsDiv.innerHTML = `
                <div class="card mb-3 border-${urgencyClass}">
                    <div class="card-header bg-${urgencyClass} text-white">
                        <h5>Hasta Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>TC Kimlik No:</strong> ${disease.tc_no}</p>
                        <p><strong>Ad Soyad:</strong> ${disease.name} ${disease.surname}</p>
                        <p><strong>Doğum Tarihi:</strong> ${disease.birthdate || 'Belirtilmemiş'}</p>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Şikayet Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Hastalık/Şikayet:</strong> ${disease.disease_name}</p>
                        <p><strong>Açıklama:</strong> ${disease.description || 'Açıklama eklenmemiş'}</p>
                        <p><strong>Aciliyet Seviyesi:</strong> 
                            <span class="badge bg-${urgencyClass}">
                                ${disease.urgency_level.toUpperCase()}
                            </span>
                        </p>
                        <p><strong>Durum:</strong> 
                            <span class="badge ${getStatusClass(disease.status)}">
                                ${getStatusText(disease.status)}
                            </span>
                        </p>
                        <p><strong>Tarih:</strong> ${formatDate(disease.created_at)}</p>
                    </div>
                </div>
            `;
            
            const updateStatusBtn = document.getElementById('updateStatusBtn');
            updateStatusBtn.textContent = `Durumu "${getStatusText(getNextStatus(disease.status))}" olarak güncelle`;
            updateStatusBtn.onclick = () => updateDiseaseStatus(disease.id, getNextStatus(disease.status));
            
            modal.show();
        }

        function updateDiseaseStatus(id, newStatus) {
            fetch('update_complaint_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: id,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modal.hide();
                    refreshDiseases();
                    alert('Durum başarıyla güncellendi!');
                } else {
                    alert('Durum güncellenirken bir hata oluştu: ' + (data.message || ''));
                }
            })
            .catch(error => {
                console.error('Hata:', error);
                alert('Durum güncellenirken bir hata oluştu!');
            });
        }

        // Tab değiştirme işlemleri
        document.getElementById('complaints-tab').addEventListener('click', function(e) {
            e.preventDefault();
            showSection('complaints');
        });

        document.getElementById('diseases-tab').addEventListener('click', function(e) {
            e.preventDefault();
            showSection('diseases');
        });

        function showSection(sectionName) {
            // Tab'ları güncelle
            document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
            document.getElementById(sectionName + '-tab').classList.add('active');

            // Bölümleri güncelle
            document.querySelectorAll('.section').forEach(section => section.classList.add('d-none'));
            document.getElementById(sectionName + '-section').classList.remove('d-none');

            // Verileri yenile
            if (sectionName === 'complaints') {
                refreshComplaints();
            } else {
                refreshDiseases();
            }
        }

        // Şikayetleri yenileme fonksiyonu
        function refreshComplaints() {
            fetch('get_diseases.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('complaints-tbody');
                    tbody.innerHTML = '';
                    
                    data.forEach(disease => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${disease.id}</td>
                            <td>${disease.tc_no}</td>
                            <td>${disease.name} ${disease.surname}</td>
                            <td>${disease.disease_name}</td>
                            <td>${disease.description}</td>
                            <td>
                                <span class="badge bg-${getUrgencyColor(disease.urgency_level)}">
                                    ${disease.urgency_level.toUpperCase()}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-${getStatusColor(disease.status)}">
                                    ${disease.status}
                                </span>
                            </td>
                            <td>${disease.created_at}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="showDiseaseDetails(${JSON.stringify(disease).replace(/"/g, '&quot;')})">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Şikayetler yüklenirken hata:', error);
                    alert('Şikayetler yüklenirken bir hata oluştu!');
                });
        }

        // Hastalıkları yenileme fonksiyonu
        function refreshDiseases() {
            fetch('get_diseases.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('diseases-tbody');
                    tbody.innerHTML = '';
                    
                    data.forEach(disease => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${disease.id}</td>
                            <td>${disease.tc_no}</td>
                            <td>${disease.name} ${disease.surname}</td>
                            <td>${disease.disease_name}</td>
                            <td>${disease.description}</td>
                            <td>
                                <span class="badge bg-${getStatusColor(disease.status)}">
                                    ${disease.status}
                                </span>
                            </td>
                            <td>${disease.created_at}</td>
                            <td>
                                <button class="btn btn-sm btn-primary" onclick="updateDiseaseStatus(${disease.id})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                })
                .catch(error => {
                    console.error('Hastalıklar yüklenirken hata:', error);
                    alert('Hastalıklar yüklenirken bir hata oluştu!');
                });
        }

        // Yardımcı fonksiyonlar
        function getUrgencyColor(urgency) {
            switch(urgency.toLowerCase()) {
                case 'kirmizi': return 'danger';
                case 'sari': return 'warning';
                case 'yesil': return 'success';
                case 'yüksek': return 'danger';
                case 'orta': return 'warning';
                case 'düşük': return 'success';
                default: return 'secondary';
            }
        }

        function getStatusColor(status) {
            switch(status.toLowerCase()) {
                case 'bekliyor': return 'warning';
                case 'inceleniyor': return 'info';
                case 'tamamlandı': return 'success';
                case 'iptal': return 'danger';
                default: return 'secondary';
            }
        }

        // Durum güncelleme fonksiyonları
        function updateStatus(id) {
            const newStatus = prompt('Yeni durumu girin (Bekliyor/İnceleniyor/Tamamlandı/İptal):');
            if (newStatus) {
                fetch('update_complaint.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: id,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        refreshComplaints();
                    } else {
                        alert('Güncelleme başarısız: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Güncelleme hatası:', error);
                    alert('Güncelleme sırasında bir hata oluştu!');
                });
            }
        }

        function updateDiseaseStatus(id) {
            const newStatus = prompt('Yeni durumu girin (Bekliyor/İnceleniyor/Tamamlandı/İptal):');
            if (newStatus) {
                // Durum değerini düzenle: Küçük harf yap ve Türkçe karakterleri değiştir
                let formattedStatus = newStatus.toLowerCase()
                    .replace('bekliyor', 'bekliyor')
                    .replace('i̇nceleniyor', 'inceleniyor')
                    .replace('inceleniyor', 'inceleniyor')
                    .replace('tamamlandı', 'tamamlandi')
                    .replace('iptal', 'iptal');
                    
                // Geçerli durum değerlerini kontrol et
                const validStatuses = ['bekliyor', 'inceleniyor', 'tamamlandi', 'iptal'];
                if (!validStatuses.includes(formattedStatus)) {
                    alert('Geçersiz durum değeri! Lütfen "Bekliyor", "İnceleniyor", "Tamamlandı" veya "İptal" girin.');
                    return;
                }
                
                fetch('update_disease.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: id,
                        status: formattedStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        refreshDiseases();
                    } else {
                        alert('Güncelleme başarısız: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Güncelleme hatası:', error);
                    alert('Güncelleme sırasında bir hata oluştu!');
                });
            }
        }

        // Sayfa yüklendiğinde şikayetleri göster
        document.addEventListener('DOMContentLoaded', function() {
            refreshComplaints();
            
            // Navbar'a Admin Ekle butonu ekle
            const navbarNav = document.getElementById('navbarNav');
            const adminButton = document.createElement('ul');
            adminButton.classList.add('navbar-nav', 'ms-auto');
            adminButton.innerHTML = `
                <li class="nav-item">
                    <a class="nav-link" href="#" id="add-admin-btn">
                        <i class="bi bi-person-plus"></i> Admin Ekle
                    </a>
                </li>
            `;
            navbarNav.appendChild(adminButton);
            
            // Admin ekle butonuna tıklandığında modal'ı göster
            document.getElementById('add-admin-btn').addEventListener('click', function() {
                const addAdminModal = new bootstrap.Modal(document.getElementById('addAdminModal'));
                addAdminModal.show();
            });
            
            // Admin kaydet butonuna tıklandığında
            document.getElementById('saveAdminBtn').addEventListener('click', function() {
                const formMessage = document.getElementById('admin-form-message');
                formMessage.classList.add('d-none');
                
                const tcNumber = document.getElementById('admin-tc').value;
                const name = document.getElementById('admin-name').value;
                const surname = document.getElementById('admin-surname').value;
                const password = document.getElementById('admin-password').value;
                
                // TC kimlik numarasını doğrula
                if (!validateTCNumber(tcNumber)) {
                    formMessage.textContent = 'Geçerli bir TC kimlik numarası giriniz (11 haneli)';
                    formMessage.classList.remove('d-none');
                    return;
                }
                
                // Form verilerini oluştur
                const formData = new FormData();
                formData.append('tc', tcNumber);
                formData.append('name', name);
                formData.append('surname', surname);
                formData.append('password', password);
                formData.append('action', 'add_admin');
                
                // Kaydet butonu durumunu güncelle
                const saveBtn = document.getElementById('saveAdminBtn');
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Kaydediliyor...';
                
                // AJAX isteği gönder
                fetch('add_admin.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Başarılı kayıt
                        formMessage.classList.remove('alert-danger');
                        formMessage.classList.add('alert-success');
                        formMessage.textContent = 'Admin başarıyla eklendi!';
                        formMessage.classList.remove('d-none');
                        
                        // Formu sıfırla
                        document.getElementById('addAdminForm').reset();
                        
                        // 2 saniye sonra modal'ı kapat
                        setTimeout(() => {
                            bootstrap.Modal.getInstance(document.getElementById('addAdminModal')).hide();
                        }, 2000);
                    } else {
                        // Hata
                        formMessage.classList.remove('alert-success');
                        formMessage.classList.add('alert-danger');
                        formMessage.textContent = data.message || 'Bir hata oluştu. Lütfen tekrar deneyin.';
                        formMessage.classList.remove('d-none');
                    }
                    
                    // Kaydet butonu durumunu sıfırla
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Kaydet';
                })
                .catch(error => {
                    console.error('Hata:', error);
                    formMessage.classList.remove('alert-success');
                    formMessage.classList.add('alert-danger');
                    formMessage.textContent = 'Bir sunucu hatası oluştu. Lütfen tekrar deneyin.';
                    formMessage.classList.remove('d-none');
                    
                    // Kaydet butonu durumunu sıfırla
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Kaydet';
                });
            });
        });
        
        // TC Kimlik numarası doğrulama
        function validateTCNumber(tcNumber) {
            // 11 haneli olmalı ve sadece rakamlardan oluşmalı
            if (!/^\d{11}$/.test(tcNumber)) {
                return false;
            }
            
            // İlk hane 0 olamaz
            if (tcNumber[0] === '0') {
                return false;
            }
            
            // Tüm rakamlar aynı olamaz
            let isSameDigit = true;
            for (let i = 1; i < tcNumber.length; i++) {
                if (tcNumber[i] !== tcNumber[0]) {
                    isSameDigit = false;
                    break;
                }
            }
            if (isSameDigit) {
                return false;
            }
            
            // 1, 3, 5, 7, 9. rakamların toplamının 7 katı ile 2, 4, 6, 8. rakamların toplamı çıkartıldığında
            // elde edilen sonucun 10'a bölümünden kalan, 10. rakamı vermeli
            let oddSum = 0;
            let evenSum = 0;
            
            for (let i = 0; i < 9; i++) {
                if (i % 2 === 0) {
                    oddSum += parseInt(tcNumber[i]);
                } else {
                    evenSum += parseInt(tcNumber[i]);
                }
            }
            
            let tenthDigitCheck = (oddSum * 7 - evenSum) % 10;
            if (tenthDigitCheck < 0) tenthDigitCheck += 10;
            
            if (tenthDigitCheck !== parseInt(tcNumber[9])) {
                return false;
            }
            
            // 11. hane kontrolü
            let total = 0;
            for (let i = 0; i < 10; i++) {
                total += parseInt(tcNumber[i]);
            }
            
            if (total % 10 !== parseInt(tcNumber[10])) {
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html> 