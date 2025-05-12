document.addEventListener('DOMContentLoaded', function() {
    // Tab değişkenleri
    const loginTab = document.getElementById('login-tab');
    const registerTab = document.getElementById('register-tab');
    const loginWrapper = document.getElementById('login-wrapper');
    const registerWrapper = document.getElementById('register-wrapper');
    const adminWrapper = document.getElementById('admin-wrapper');
    const userTabs = document.getElementById('user-tabs');
    const userTypeBtn = document.getElementById('user-type-btn');
    const adminTypeBtn = document.getElementById('admin-type-btn');
    
    // Form değişkenleri
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const loginError = document.getElementById('login-error');
    const registerError = document.getElementById('register-error');
    const adminForm = document.getElementById('admin-form');
    const adminError = document.getElementById('admin-error');
    
    // Tab geçişleri
    loginTab.addEventListener('click', function() {
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
        loginWrapper.classList.add('active');
        registerWrapper.classList.remove('active');
    });
    
    registerTab.addEventListener('click', function() {
        registerTab.classList.add('active');
        loginTab.classList.remove('active');
        registerWrapper.classList.add('active');
        loginWrapper.classList.remove('active');
    });
    
    // LocalStorage'dan kullanıcı verilerini al
    let users = JSON.parse(localStorage.getItem('triajUsers')) || [];
    
    // Giriş formu gönderildiğinde
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tcNumber = document.getElementById('login-tc').value;
        
        // TC kimlik numarası kontrolü client-side'da da yapılıyor
        if (!validateTCNumber(tcNumber)) {
            loginError.textContent = 'Geçerli bir TC kimlik numarası giriniz (11 haneli)';
            return;
        }
        
        // AJAX ile PHP'ye gönder
        fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `tc=${tcNumber}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loginError.textContent = '';
                loginSuccess();
                setTimeout(() => {
                    window.location.href = '../sikayetformu/triage.html';
                }, 1500);
            } else {
                // Veritabanında bulunamadı, localStorage kontrolü yap
                const localUser = users.find(user => user.tc === tcNumber);
                if (localUser) {
                    loginError.textContent = '';
                    loginSuccess();
                    setTimeout(() => {
                        window.location.href = '../sikayetformu/triage.html';
                    }, 1500);
                } else {
                    loginError.textContent = data.error || 'Kullanıcı bulunamadı!';
                }
            }
        })
        .catch(error => {
            loginError.textContent = 'Bir hata oluştu, lütfen tekrar deneyin';
        });
    });
    
    // Kayıt formu gönderildiğinde
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tcNumber = document.getElementById('register-tc').value;
        const name = document.getElementById('register-name').value;
        const surname = document.getElementById('register-surname').value;
        const birthdate = document.getElementById('register-birthdate').value;
        
        // TC kimlik numarası kontrolü
        if (!validateTCNumber(tcNumber)) {
            registerError.textContent = 'Geçerli bir TC kimlik numarası giriniz (11 haneli)';
            return;
        }
        
        // Form verilerini oluştur
        const formData = new FormData();
        formData.append('tc', tcNumber);
        formData.append('name', name);
        formData.append('surname', surname);
        formData.append('birthdate', birthdate);
        
        // PHP backend'e gönder
        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                registerError.textContent = '';
                registerSuccess();
                setTimeout(() => {
                    loginTab.click();
                    registerForm.reset();
                }, 1500);
            } else {
                registerError.textContent = data.error || 'Kayıt işlemi başarısız';
            }
        })
        .catch(error => {
            registerError.textContent = 'Bir hata oluştu, lütfen tekrar deneyin';
            console.error('Error:', error);
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
        let total = 0;  // total değişkenini tanımladık
        
        for (let i = 0; i < 9; i++) {
            total += parseInt(tcNumber[i]);  // Her rakamı toplama ekle
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
        if ((total + parseInt(tcNumber[9])) % 10 !== parseInt(tcNumber[10])) {
            return false;
        }
        
        return true;
    }
    
    // Login başarılı animasyonu
    function loginSuccess() {
        const loginBtn = loginForm.querySelector('button');
        loginBtn.innerHTML = '<i class="fas fa-check"></i> Giriş Başarılı';
        loginBtn.style.backgroundColor = '#4CAF50';
    }
    
    // Register başarılı animasyonu
    function registerSuccess() {
        const registerBtn = registerForm.querySelector('button');
        registerBtn.innerHTML = '<i class="fas fa-check"></i> Kayıt Başarılı';
        registerBtn.style.backgroundColor = '#4CAF50';
    }
    
    // Navbar scroll efekti
    window.addEventListener('scroll', function() {
        const header = document.querySelector('header');
        if (window.scrollY > 50) {
            header.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.1)';
        } else {
            header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.1)';
        }
    });
    
    // Giriş tipi seçimi
    userTypeBtn.addEventListener('click', function() {
        userTypeBtn.classList.add('active');
        adminTypeBtn.classList.remove('active');
        userTabs.style.display = 'flex';
        
        // Önce admin panelini gizle
        adminWrapper.classList.remove('show');
        setTimeout(() => {
            adminWrapper.classList.remove('active');
            adminWrapper.style.display = 'none';
        }, 400);
        
        // Sonra kullanıcı formunu göster
        if (loginTab.classList.contains('active')) {
            loginWrapper.classList.add('active');
        } else {
            registerWrapper.classList.add('active');
        }
    });
    
    adminTypeBtn.addEventListener('click', function() {
        adminTypeBtn.classList.add('active');
        userTypeBtn.classList.remove('active');
        userTabs.style.display = 'none';
        
        // Önce kullanıcı formlarını gizle
        loginWrapper.classList.remove('active');
        registerWrapper.classList.remove('active');
        
        // Sonra admin panelini göster
        adminWrapper.style.display = 'block';
        setTimeout(() => {
            adminWrapper.classList.add('active');
            adminWrapper.classList.add('show');
        }, 50);
    });
    
    // Form içeriklerini sıfırla fonksiyonu
    function resetForms() {
        document.getElementById('register-form').reset();
        document.getElementById('login-form').reset();
        
        // Tüm input alanlarını görünür yap
        const allInputFields = document.querySelectorAll('#register-form .input-field');
        allInputFields.forEach(field => {
            field.style.display = 'flex';
        });
    }
    
    // Admin giriş formu gönderildiğinde
    adminForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tcNumber = document.getElementById('admin-tc').value;
        const password = document.getElementById('admin-password').value;
        
        // TC kimlik numarası kontrolü
        if (!validateTCNumber(tcNumber)) {
            adminError.textContent = 'Geçerli bir TC kimlik numarası giriniz (11 haneli)';
            return;
        }
        
        // Buton durumunu güncelle
        const adminBtn = adminForm.querySelector('button');
        adminBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Giriş yapılıyor...';
        adminBtn.disabled = true;
        
        // Form verilerini oluştur
        const formData = new FormData();
        formData.append('tc', tcNumber);
        formData.append('password', password);
        
        console.log('Admin giriş isteği gönderiliyor:', tcNumber);
        
        // PHP backend'e gönder - doğrudan admin-login.php'ye relatif URL ile istek yapılıyor
        fetch('admin-login.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin' // Session cookie'lerini gönder
        })
        .then(response => {
            console.log('Yanıt durumu:', response.status, response.statusText);
            if (!response.ok) {
                throw new Error('Sunucu yanıtı başarısız: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Sunucu yanıtı:', data);
            if (data.success) {
                console.log('Admin girişi başarılı, yönlendiriliyor...');
                adminError.textContent = '';
                adminBtn.innerHTML = '<i class="fas fa-check"></i> Giriş Başarılı';
                adminBtn.style.backgroundColor = '#4CAF50';
                
                setTimeout(() => {
                    window.location.href = '../kullanicigiris/admin/dashboard.php';
                }, 1000);
            } else {
                adminError.textContent = data.error || 'TC Kimlik veya şifre hatalı!';
                adminBtn.innerHTML = 'Yönetici Girişi';
                adminBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Giriş hatası:', error);
            adminError.textContent = 'Bir hata oluştu. Lütfen tekrar deneyin.';
            adminBtn.innerHTML = 'Yönetici Girişi';
            adminBtn.disabled = false;
        });
    });
}); // DOMContentLoaded kapanış
    
