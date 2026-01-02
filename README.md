# Kurumsal CRM Sistemi

Basit ama etkili, PHP ve MySQL tabanlı kurumsal CRM (Müşteri İlişkileri Yönetimi) sistemi.

## Özellikler

### 📊 Dashboard (Ana Sayfa)
- Toplam müşteri, satış ve görev istatistikleri
- Son eklenen müşteriler listesi
- Yaklaşan görevler
- Son satış fırsatları
- Görsel istatistik kartları

### 👥 Müşteri Yönetimi
- Müşteri ekleme, düzenleme, silme
- Detaylı müşteri bilgileri (şirket adı, ilgili kişi, iletişim bilgileri, adres)
- Müşteri durumu (Potansiyel, Aktif, Pasif)
- Arama ve filtreleme
- Sektör ve vergi numarası takibi

### 💰 Satış Fırsatları Yönetimi
- Satış fırsatı ekleme, düzenleme, silme
- Satış durumu takibi (Yeni, Görüşme, Teklif, Kazanıldı, Kaybedildi)
- Fırsat olasılığı yönetimi (%)
- Tutar ve para birimi takibi
- Beklenen kapanış tarihi
- Kullanıcıya atama

### ✅ Görev Yönetimi
- Görev ekleme, düzenleme, silme
- Görev durumu (Bekliyor, Devam Ediyor, Tamamlandı, İptal)
- Öncelik seviyeleri (Düşük, Normal, Yüksek, Acil)
- Müşteri ve satışla ilişkilendirme
- Son tarih takibi
- Kullanıcıya atama

### 📦 Ürün/Hizmet Yönetimi
- Ürün/hizmet ekleme, düzenleme, silme
- Ürün kodu ve kategori yönetimi
- Birim fiyat ve para birimi
- Stok miktarı takibi
- Aktif/Pasif durum

### 📈 Raporlar ve Analitik
- Satış özeti ve durum dağılımı
- Aylık satış raporları (son 6 ay)
- En iyi müşteriler
- Görev istatistikleri
- Kullanıcı performans raporları
- Kazanılan ve potansiyel satış tutarları

### 🔐 Kullanıcı Yönetimi
- Güvenli giriş sistemi
- Oturum yönetimi
- Kullanıcı rolleri (Admin, Manager, User)
- Aktivite logları

## Teknolojiler

- **Backend:** PHP 7.4+
- **Veritabanı:** MySQL 5.7+ / MariaDB 10.3+
- **Frontend:** HTML5, CSS3, JavaScript
- **UI Framework:** Bootstrap 5.3
- **Icons:** Bootstrap Icons
- **Karakter Seti:** UTF-8 (Türkçe karakter desteği)

## Kurulum

### Gereksinimler

- PHP 7.4 veya üzeri
- MySQL 5.7+ veya MariaDB 10.3+
- Apache/Nginx web sunucusu
- mod_rewrite etkin (Apache için)

### Adım 1: Dosyaları Kopyalama

Tüm dosyaları web sunucunuzun kök dizinine (örn: `htdocs`, `www`, `public_html`) kopyalayın.

```bash
git clone https://github.com/integrumart/kurumsalCRM.git
cd kurumsalCRM
```

### Adım 2: Veritabanı Oluşturma

1. phpMyAdmin veya MySQL komut satırını açın
2. `database.sql` dosyasını içe aktarın:

```sql
mysql -u root -p < database.sql
```

Ya da phpMyAdmin'de:
- Yeni veritabanı oluşturun: `kurumsal_crm`
- SQL sekmesine gidin
- `database.sql` dosyasının içeriğini yapıştırın ve çalıştırın

### Adım 3: Veritabanı Bağlantı Ayarları

`config.php` dosyasını düzenleyin ve veritabanı bilgilerinizi girin:

```php
define('DB_HOST', 'localhost');      // Veritabanı sunucusu
define('DB_USER', 'root');           // Veritabanı kullanıcı adı
define('DB_PASS', '');               // Veritabanı şifresi
define('DB_NAME', 'kurumsal_crm');   // Veritabanı adı
```

### Adım 4: Dosya İzinleri

Linux/Unix sistemlerde gerekli izinleri verin:

```bash
chmod 755 /path/to/kurumsalCRM
chmod 644 /path/to/kurumsalCRM/*.php
```

### Adım 5: Web Tarayıcıdan Erişim

Tarayıcınızdan sisteme erişin:

```
http://localhost/kurumsalCRM/
```

veya

```
http://yourdomain.com/
```

## Varsayılan Giriş Bilgileri

İlk giriş için varsayılan yönetici hesabını kullanın:

- **Kullanıcı Adı:** admin
- **Şifre:** admin123

⚠️ **ÖNEMLİ:** İlk girişten sonra mutlaka admin şifresini değiştirin!

## Kullanım

### Dashboard
Ana sayfada sistemin genel durumunu görebilirsiniz:
- Toplam müşteri sayısı ve aktif müşteriler
- Toplam satış tutarı ve bekleyen satışlar
- Bekleyen görevler
- Son eklenen müşteriler
- Yaklaşan görevler
- Son satış fırsatları

### Müşteri Ekleme
1. "Müşteriler" menüsüne tıklayın
2. "Yeni Müşteri Ekle" butonuna tıklayın
3. Formu doldurun (şirket adı zorunludur)
4. "Kaydet" butonuna tıklayın

### Satış Fırsatı Oluşturma
1. "Satışlar" menüsüne tıklayın
2. "Yeni Satış Ekle" butonuna tıklayın
3. Müşteri seçin ve detayları girin
4. Durum, olasılık ve beklenen kapanış tarihini belirleyin
5. "Kaydet" butonuna tıklayın

### Görev Oluşturma
1. "Görevler" menüsüne tıklayın
2. "Yeni Görev Ekle" butonuna tıklayın
3. Görev detaylarını girin
4. Öncelik ve son tarih belirleyin
5. Görev sorumlusunu atayın
6. "Kaydet" butonuna tıklayın

### Raporları Görüntüleme
"Raporlar" menüsünden:
- Satış istatistiklerini görüntüleyin
- Aylık performansı takip edin
- En iyi müşterileri görün
- Kullanıcı performansını analiz edin

## Güvenlik

- Tüm kullanıcı girişleri sanitize edilir
- SQL injection koruması (prepared statements)
- XSS koruması
- Session güvenliği
- Şifreler hash'lenerek saklanır (password_hash)

## Özelleştirme

### Tema Renkleri
`style.css` dosyasındaki CSS değişkenlerini düzenleyerek renkleri özelleştirebilirsiniz:

```css
:root {
    --primary-color: #0d6efd;
    --secondary-color: #6c757d;
    --success-color: #198754;
    /* ... */
}
```

### Para Birimi
`config.php` dosyasında varsayılan para birimini değiştirebilirsiniz.

## Veritabanı Yapısı

Sistem aşağıdaki ana tabloları kullanır:
- `users` - Kullanıcılar
- `customers` - Müşteriler
- `products` - Ürünler/Hizmetler
- `sales` - Satış fırsatları
- `sale_items` - Satış detayları
- `tasks` - Görevler
- `activities` - Sistem aktivite logları

## Destek ve Katkı

Hata bildirimleri ve öneriler için GitHub Issues kullanabilirsiniz.

## Lisans

Bu proje GPL-3.0 lisansı altında lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakın.

## Yapımcı

İntegrum Art - 2026

---

**Not:** Bu sistem temel bir CRM çözümüdür. Kurumsal kullanım için ek güvenlik önlemleri, yedekleme sistemleri ve performans optimizasyonları yapılması önerilir.
