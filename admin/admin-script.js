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
        activateTab(usersTab, usersSection);
    });
    
    statsTab.addEventListener('click', function(e) {
        e.preventDefault();
        activateTab(statsTab, statsSection);
        updateStats();
    });
    
    settingsTab.addEventListener('click', function(e) {
        e.preventDefault();
        activateTab(settingsTab, settingsSection);
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
        // LocalStorage'dan kullanıcıları al
        const users = JSON.parse(localStorage.getItem('triajUsers')) || [];
        
        // Kullanıcı listesini temizle
        usersList.innerHTML = '';
        
        // Arama terimini küçük harfe çevir
        searchTerm = searchTerm.toLowerCase();
        
        // Filtrelenmiş kullanıcıları al
        const filteredUsers = users.filter(user => 
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
            // LocalStorage'dan kullanıcıları al
            const users = JSON.parse(localStorage.getItem('triajUsers')) || [];
            
            // Kullanıcıyı filtrele
            const updatedUsers = users.filter(user => user.tc !== userToDelete);
            
            // LocalStorage'a kaydet
            localStorage.setItem('triajUsers', JSON.stringify(updatedUsers));
            
            // Modalı kapat
            deleteModal.classList.remove('active');
            
            // Kullanıcı listesini yenile
            loadUsers();
            
            // İstatistikleri güncelle
            updateStats();
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