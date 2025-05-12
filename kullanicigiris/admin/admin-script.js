document.addEventListener('DOMContentLoaded', function() {
    // Tab değişkenleri
    const usersTab = document.getElementById('users-tab');
    const statsTab = document.getElementById('stats-tab');
    const settingsTab = document.getElementById('settings-tab');
    const usersSection = document.getElementById('users-section');
    const statsSection = document.getElementById('stats-section');
    const settingsSection = document.getElementById('settings-section');
    
    // Kullanıcı listesi değişkenleri
    const usersList = document.getElementById('users-list');
    const noUsersMessage = document.getElementById('no-users-message');
    const userSearch = document.getElementById('user-search');
    const searchBtn = document.getElementById('search-btn');
    
    // İstatistik değişkenleri
    const totalUsersElement = document.getElementById('total-users');
    const todayUsersElement = document.getElementById('today-users');
    const todayLoginsElement = document.getElementById('today-logins');
    
    // Modal değişkenleri
    const deleteModal = document.getElementById('delete-modal');
    const confirmDeleteBtn = document.getElementById('confirm-delete');
    const cancelDeleteBtn = document.getElementById('cancel-delete');
    let userToDelete = null;
    
    // Tab geçişleri
    usersTab.addEventListener('click', function(e) {
        e.preventDefault();
        showSection('users-section');
        loadUsers();
    });
    
    statsTab.addEventListener('click', function(e) {
        e.preventDefault();
        showSection('stats-section');
        updateStats();
    });
    
    settingsTab.addEventListener('click', function(e) {
        e.preventDefault();
        showSection('settings-section');
    });
    
    function activateTab(tab, section) {
        // Tüm tabları ve bölümleri pasif yap
        [usersTab, statsTab, settingsTab].forEach(t => t.classList.remove('active'));
        [usersSection, statsSection, settingsSection].forEach(s => s.classList.remove('active-section'));
        
        // Seçilen tabı ve bölümü aktif yap
        tab.classList.add('active');
        section.classList.add('active-section');
    }
    
    // Kullanıcıları yükle
    function loadUsers(searchTerm = '') {
        // Veritabanından kullanıcıları al
        fetch('get-users.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Kullanıcı listesini temizle
                    usersList.innerHTML = '';
                    
                    // Arama terimini küçük harfe çevir
                    searchTerm = searchTerm.toLowerCase();
                    
                    // Filtrelenmiş kullanıcıları al
                    const filteredUsers = data.users.filter(user => 
                        user.tc.includes(searchTerm) || 
                        user.name.toLowerCase().includes(searchTerm) || 
                        user.surname.toLowerCase().includes(searchTerm)
                    );
                    
                    if (filteredUsers.length === 0) {
                        noUsersMessage.style.display = 'block';
                    } else {
                        noUsersMessage.style.display = 'none';
                        
                        // Kullanıcıları listele
                        filteredUsers.forEach(user => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${user.tc}</td>
                                <td>${user.name}</td>
                                <td>${user.surname}</td>
                                <td>${user.birthdate}</td>
                                <td>
                                    <button class="action-btn edit-btn" data-tc="${user.tc}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn delete-btn" data-tc="${user.tc}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            `;
                            usersList.appendChild(row);
                        });
                        
                        // Düzenleme ve silme butonlarına olay dinleyicileri ekle
                        addActionButtonListeners();
                    }
                } else {
                    console.error('Kullanıcılar yüklenirken hata:', data.error);
                    alert('Kullanıcılar yüklenirken bir hata oluştu');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Kullanıcılar yüklenirken bir hata oluştu');
            });
    }
    
    // Düzenleme ve silme butonlarına olay dinleyicileri ekle
    function addActionButtonListeners() {
        // Silme butonları
        const deleteButtons = document.querySelectorAll('.delete-btn');
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                userToDelete = this.getAttribute('data-tc');
                deleteModal.classList.add('active');
            });
        });
        
        // Düzenleme butonları
        const editButtons = document.querySelectorAll('.edit-btn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tc = this.getAttribute('data-tc');
                // Düzenleme işlevi burada eklenecek
                alert(`${tc} numaralı kullanıcıyı düzenleme özelliği yakında eklenecek.`);
            });
        });
    }
    
    // Kullanıcı silme işlemi
    confirmDeleteBtn.addEventListener('click', function() {
        if (userToDelete) {
            const formData = new FormData();
            formData.append('tc', userToDelete);
            
            fetch('delete-user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    deleteModal.classList.remove('active');
                    loadUsers(); // Kullanıcı listesini yenile
                } else {
                    alert(data.error || 'Kullanıcı silinirken bir hata oluştu');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Kullanıcı silinirken bir hata oluştu');
            });
        }
    });
    
    // Modal kapatma
    cancelDeleteBtn.addEventListener('click', function() {
        deleteModal.classList.remove('active');
        userToDelete = null;
    });
    
    // Arama işlevi
    searchBtn.addEventListener('click', function() {
        loadUsers(userSearch.value);
    });
    
    userSearch.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            loadUsers(userSearch.value);
        }
    });
    
    // İstatistikleri güncelle
    function updateStats() {
        const users = JSON.parse(localStorage.getItem('triajUsers')) || [];
        
        // Toplam kullanıcı sayısı
        totalUsersElement.textContent = users.length;
        
        // Bugün kaydolan kullanıcı sayısı (örnek olarak 0 gösteriyoruz)
        // Gerçek uygulamada, kullanıcı kayıt tarihini de saklamanız gerekir
        todayUsersElement.textContent = '0';
        
        // Bugün giriş yapan kullanıcı sayısı (örnek olarak 0 gösteriyoruz)
        // Gerçek uygulamada, giriş loglarını da saklamanız gerekir
        todayLoginsElement.textContent = '0';
    }
    
    // Ayarları kaydet
    const saveSettingsBtn = document.getElementById('save-settings');
    saveSettingsBtn.addEventListener('click', function() {
        const newPassword = document.getElementById('admin-password').value;
        const confirmPassword = document.getElementById('admin-password-confirm').value;
        
        if (newPassword === '') {
            alert('Lütfen yeni şifre girin.');
            return;
        }
        
        if (newPassword !== confirmPassword) {
            alert('Şifreler eşleşmiyor.');
            return;
        }
        
        // Şifreyi LocalStorage'a kaydet (gerçek uygulamada güvenli bir şekilde saklanmalıdır)
        localStorage.setItem('adminPassword', newPassword);
        
        alert('Şifre başarıyla değiştirildi.');
        document.getElementById('admin-password').value = '';
        document.getElementById('admin-password-confirm').value = '';
    });
    
    // Sayfa yüklendiğinde kullanıcıları yükle
    loadUsers();
    
    // Sayfa yüklendiğinde istatistikleri güncelle
    updateStats();
    
    // Varsayılan admin hesabını kontrol et ve oluştur
    function checkDefaultAdmin() {
        const adminAccounts = JSON.parse(localStorage.getItem('triajAdmins')) || [];
        
        // Eğer hiç admin hesabı yoksa, varsayılan hesap oluştur
        if (adminAccounts.length === 0) {
            const defaultAdmin = {
                tc: "11111111110",
                password: "admin123",
                name: "Admin",
                surname: "Kullanıcı",
                createdAt: new Date().toISOString()
            };
            
            adminAccounts.push(defaultAdmin);
            localStorage.setItem('triajAdmins', JSON.stringify(adminAccounts));
            console.log("Varsayılan admin hesabı oluşturuldu.");
        }
    }
    
    // Sayfa yüklendiğinde varsayılan admin hesabını kontrol et
    checkDefaultAdmin();
    
    // Yeni admin ekleme
    const addAdminBtn = document.getElementById('add-admin');
    addAdminBtn.addEventListener('click', function() {
        const tcNumber = document.getElementById('new-admin-tc').value;
        const name = document.getElementById('new-admin-name').value;
        const surname = document.getElementById('new-admin-surname').value;
        const password = document.getElementById('new-admin-password').value;
        
        // Alanları kontrol et
        if (!tcNumber || !name || !surname || !password) {
            alert('Lütfen tüm alanları doldurun.');
            return;
        }
        
        // TC kimlik numarası kontrolü
        if (!/^\d{11}$/.test(tcNumber) || tcNumber[0] === '0') {
            alert('Geçerli bir TC kimlik numarası giriniz (11 haneli).');
            return;
        }
        
        // LocalStorage'dan admin hesaplarını al
        const adminAccounts = JSON.parse(localStorage.getItem('triajAdmins')) || [];
        
        // TC numarası zaten kayıtlı mı kontrol et
        const existingAdmin = adminAccounts.find(admin => admin.tc === tcNumber);
        if (existingAdmin) {
            alert('Bu TC kimlik numarası zaten kayıtlı!');
            return;
        }
        
        // Yeni admin hesabını ekle
        const newAdmin = {
            tc: tcNumber,
            password: password,
            name: name,
            surname: surname,
            createdAt: new Date().toISOString()
        };
        
        adminAccounts.push(newAdmin);
        localStorage.setItem('triajAdmins', JSON.stringify(adminAccounts));
        
        alert('Yeni yönetici başarıyla eklendi.');
        
        // Formu temizle
        document.getElementById('new-admin-tc').value = '';
        document.getElementById('new-admin-name').value = '';
        document.getElementById('new-admin-surname').value = '';
        document.getElementById('new-admin-password').value = '';
    });
});

// Triaj verilerini yükleme fonksiyonu
function loadTriageData() {
    const dateFilter = document.getElementById('date-filter') ? document.getElementById('date-filter').value : '';
    const urgencyFilter = document.getElementById('urgency-filter') ? document.getElementById('urgency-filter').value : '';
    
    // LocalStorage'dan verileri al
    let triageData = JSON.parse(localStorage.getItem('triageRecords') || '[]');
    
    // Filtreleme işlemleri
    if (dateFilter) {
        triageData = triageData.filter(record => {
            const recordDate = new Date(record.timestamp).toLocaleDateString();
            const filterDate = new Date(dateFilter).toLocaleDateString();
            return recordDate === filterDate;
        });
    }
    
    if (urgencyFilter) {
        triageData = triageData.filter(record => record.urgencyLevel === urgencyFilter);
    }
    
    // Tabloyu doldur
    const tableBody = document.getElementById('triage-data');
    if (!tableBody) return;
    tableBody.innerHTML = '';
    
    triageData.forEach(record => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${new Date(record.timestamp).toLocaleString('tr-TR')}</td>
            <td>${record.patientTC || '-'}</td>
            <td>${record.diseases.join(', ')}</td>
            <td>${record.description || '-'}</td>
            <td class="urgency-${record.urgencyLevel}">
                ${record.urgencyLevel.toUpperCase()}
            </td>
            <td>
                <button class="action-btn view-btn" onclick="viewTriageDetails('${record.id}')">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="action-btn delete-btn" onclick="deleteTriageRecord('${record.id}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });
}

// Triaj detaylarını görüntüleme
function viewTriageDetails(recordId) {
    const triageData = JSON.parse(localStorage.getItem('triageRecords') || '[]');
    const record = triageData.find(r => r.id === recordId);
    
    if (record) {
        // Detay modalını göster
        const modal = document.getElementById('triage-details-modal');
        modal.style.display = 'block';
        
        // Modal içeriğini doldur
        document.getElementById('modal-patient-tc').textContent = record.patientTC;
        document.getElementById('modal-diseases').textContent = record.diseases.join(', ');
        document.getElementById('modal-description').textContent = record.description;
        document.getElementById('modal-urgency').textContent = record.urgencyLevel.toUpperCase();
        document.getElementById('modal-timestamp').textContent = new Date(record.timestamp).toLocaleString('tr-TR');
    }
}

// Triaj kaydını silme
function deleteTriageRecord(recordId) {
    if (confirm('Bu triaj kaydını silmek istediğinizden emin misiniz?')) {
        let triageData = JSON.parse(localStorage.getItem('triageRecords') || '[]');
        triageData = triageData.filter(record => record.id !== recordId);
        localStorage.setItem('triageRecords', JSON.stringify(triageData));
        loadTriageData(); // Tabloyu yenile
    }
}

// Event Listeners
const filterBtn = document.getElementById('filter-btn');
if (filterBtn) {
    filterBtn.addEventListener('click', loadTriageData);
}
const triageTab = document.getElementById('triage-tab');
if (triageTab) {
    triageTab.addEventListener('click', function(e) {
        e.preventDefault();
        showSection('triage-section');
        loadTriageData();
    });
}

// Sayfa yüklendiğinde triaj verilerini yükle
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('triage-section').classList.contains('active-section')) {
        loadTriageData();
    }
});

function loadDiseases() {
    fetch('get-diseases.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const diseasesTable = document.getElementById('diseasesTable');
                if (diseasesTable) {
                    diseasesTable.innerHTML = '';
                    
                    if (data.diseases.length === 0) {
                        diseasesTable.innerHTML = `
                            <tr>
                                <td colspan="9" class="text-center">Henüz kayıtlı hastalık/şikayet bulunmamaktadır.</td>
                            </tr>
                        `;
                        return;
                    }
                    
                    data.diseases.forEach(disease => {
                        const row = document.createElement('tr');
                        row.className = `urgency-${disease.urgency_level}`;
                        
                        row.innerHTML = `
                            <td>${disease.id}</td>
                            <td>${disease.tc_no}</td>
                            <td>${disease.name || ''} ${disease.surname || ''}</td>
                            <td>${disease.disease_name}</td>
                            <td class="description-text">${disease.description || ''}</td>
                            <td>
                                <span class="badge ${disease.urgency_level === 'kirmizi' ? 'bg-danger' : 
                                                disease.urgency_level === 'sari' ? 'bg-warning' : 'bg-success'}">
                                    ${disease.urgency_level.toUpperCase()}
                                </span>
                            </td>
                            <td>
                                <span class="badge ${getStatusClass(disease.status)}">
                                    ${getStatusText(disease.status)}
                                </span>
                            </td>
                            <td>${formatDate(disease.created_at)}</td>
                            <td>
                                <button class="btn btn-sm btn-info me-1" onclick='showDiseaseDetails(${JSON.stringify(disease).replace(/'/g, "&#39;")})'>
                                    <i class="bi bi-eye"></i> Detay
                                </button>
                            </td>
                        `;
                        diseasesTable.appendChild(row);
                    });
                }
            } else {
                console.error('Hastalıklar yüklenirken hata oluştu:', data.message || 'Bilinmeyen hata');
            }
        })
        .catch(error => {
            console.error('Hastalıklar yüklenirken hata oluştu:', error);
        });
}

// Helper fonksiyonlar (dashboard.php'den alınan)
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

// Sayfa yüklendiğinde hastalıkları yükle
document.addEventListener('DOMContentLoaded', function() {
    // Önceki kod korundu, sadece loadDiseases() çağrısı yapılıyor
    if (document.getElementById('diseasesTable')) {
        loadDiseases();
    }
    
    // Hastalıklar sekmesi için dinleyici ekle (eğer varsa)
    const diseasesTab = document.getElementById('diseases-tab');
    if (diseasesTab) {
        diseasesTab.addEventListener('click', function(e) {
            e.preventDefault();
            showSection('diseases-section');
            loadDiseases();
        });
    }
});

function showSection(sectionId) {
    document.querySelectorAll('section').forEach(sec => {
        sec.style.display = 'none';
        sec.classList.remove('active-section');
    });
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = 'block';
        section.classList.add('active-section');
    }
    // Sekme aktifliğini güncelle
    document.querySelectorAll('nav ul li a').forEach(tab => tab.classList.remove('active'));
    switch(sectionId) {
        case 'users-section':
            document.getElementById('users-tab').classList.add('active'); break;
        case 'stats-section':
            document.getElementById('stats-tab').classList.add('active'); break;
        case 'triage-section':
            document.getElementById('triage-tab').classList.add('active'); break;
        case 'complaints-section':
            document.getElementById('complaints-tab').classList.add('active'); break;
        case 'settings-section':
            document.getElementById('settings-tab').classList.add('active'); break;
    }
}

document.getElementById('complaints-tab').addEventListener('click', function(e) {
    e.preventDefault();
    showSection('complaints-section');
    loadComplaints();
});