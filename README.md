# Hastane Triaj Sistemi

Bu proje, hastanelerde acil servislerde kullanılmak üzere tasarlanmış bir triaj ve hasta önceliklendirme sistemidir.

## Kurulum

1. XAMPP, WAMP veya benzeri bir local web server kurun
2. Projeyi htdocs veya www klasörüne yerleştirin
3. Veritabanı kurulumu için `config/setup.php` dosyasını çalıştırın:
   - Tarayıcınızdan `http://localhost/triajGuncellenmis/config/setup.php` adresine gidin
   - Bu işlem veritabanını ve gerekli tabloları otomatik olarak oluşturacaktır

## Varsayılan Kullanıcılar

### Admin Giriş Bilgileri
- TC Kimlik: 11111111111
- Şifre: admin123

## Sistem Özellikleri

- Hasta kayıt ve giriş
- Hastalık şikayeti ve belirtileri giriş
- Triaj seviyesi belirleme (Kırmızı, Sarı, Yeşil)
- Admin paneli ile hasta takibi ve durum güncelleme
- Raporlama ve istatistikler

## Kullanım

1. Ana sayfaya erişmek için: `http://localhost/triajGuncellenmis/index.html`
2. Kullanıcı girişi: `http://localhost/triajGuncellenmis/kullanicigiris/login.html`
3. Admin girişi: `http://localhost/triajGuncellenmis/kullanicigiris/admin-login.php`

## Hatalar ve Sorunlar

Herhangi bir hata veya sorunla karşılaşırsanız, lütfen aşağıdaki kontrolleri yapın:

1. Veritabanı bağlantısı için `config/db.php` dosyasındaki kimlik bilgilerinin doğru olduğundan emin olun
2. Veritabanının ve tabloların doğru oluşturulduğunu kontrol edin
3. PHP ve MySQL'in çalıştığından emin olun
4. Oturum yönetimi için PHP session'ın çalıştığını kontrol edin

## Güvenlik

Bu sistem, TC Kimlik numarası doğrulama algoritması içermektedir. Ayrıca, admin şifreleri güvenli bir şekilde hashlenmekte ve veritabanına bu şekilde kaydedilmektedir. 