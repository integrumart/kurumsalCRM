# Kurumsal CRM - Sistem Yapısı

## Dosya Organizasyonu

```
kurumsalCRM/
│
├── config.php                 # Veritabanı bağlantı ve yardımcı fonksiyonlar
├── database.sql              # Veritabanı şeması ve örnek veriler
├── style.css                 # CSS stilleri ve tema ayarları
│
├── header.php                # Ortak sayfa başlığı ve navigasyon
├── footer.php                # Ortak sayfa alt bilgisi
│
├── login.php                 # Giriş sayfası
├── logout.php                # Çıkış işlemi
├── index.php                 # Ana dashboard
│
├── customers.php             # Müşteri listesi
├── customer_add.php          # Yeni müşteri ekleme
├── customer_edit.php         # Müşteri düzenleme
├── customer_delete.php       # Müşteri silme
│
├── sales.php                 # Satış fırsatları listesi
├── sale_add.php              # Yeni satış ekleme
├── sale_edit.php             # Satış düzenleme
├── sale_delete.php           # Satış silme
│
├── tasks.php                 # Görev listesi
├── task_add.php              # Yeni görev ekleme
├── task_edit.php             # Görev düzenleme
├── task_delete.php           # Görev silme
│
├── products.php              # Ürün/hizmet listesi
├── product_add.php           # Yeni ürün ekleme
├── product_edit.php          # Ürün düzenleme
├── product_delete.php        # Ürün silme
│
├── reports.php               # Raporlar ve istatistikler
│
├── README.md                 # Detaylı dokümantasyon
├── INSTALL.txt               # Kurulum kılavuzu
├── STRUCTURE.md              # Bu dosya
└── LICENSE                   # Lisans bilgisi
```

## Veritabanı Tabloları

### users (Kullanıcılar)
- id, username, password, full_name, email, role, created_at, last_login, status

### customers (Müşteriler)
- id, company_name, contact_person, email, phone, mobile, address, city, country
- tax_number, industry, website, customer_type, notes, created_by, created_at, updated_at

### products (Ürünler/Hizmetler)
- id, product_name, product_code, description, category, unit_price, currency
- stock_quantity, status, created_at, updated_at

### sales (Satış Fırsatları)
- id, customer_id, title, description, amount, currency, status, probability
- expected_close_date, assigned_to, created_by, created_at, updated_at

### sale_items (Satış Detayları)
- id, sale_id, product_id, quantity, unit_price, discount_percent, total_price

### tasks (Görevler)
- id, title, description, customer_id, sale_id, assigned_to, priority, status
- due_date, completed_at, created_by, created_at, updated_at

### activities (Aktivite Logları)
- id, user_id, activity_type, entity_type, entity_id, description, created_at

## Sayfa Akışı

### Kullanıcı Girişi
```
login.php → Oturum kontrolü → index.php (Dashboard)
```

### Dashboard (Ana Sayfa)
```
index.php
├── İstatistik kartları (müşteri, satış, görev)
├── Son eklenen müşteriler
├── Yaklaşan görevler
└── Son satış fırsatları
```

### Müşteri Yönetimi
```
customers.php (Liste)
├── customer_add.php → Form → DB INSERT → customers.php
├── customer_edit.php → Form → DB UPDATE → customers.php
└── customer_delete.php → DB DELETE → customers.php
```

### Satış Yönetimi
```
sales.php (Liste)
├── sale_add.php → Form → DB INSERT → sales.php
├── sale_edit.php → Form → DB UPDATE → sales.php
└── sale_delete.php → DB DELETE → sales.php
```

### Görev Yönetimi
```
tasks.php (Liste)
├── task_add.php → Form → DB INSERT → tasks.php
├── task_edit.php → Form → DB UPDATE → tasks.php
└── task_delete.php → DB DELETE → tasks.php
```

### Ürün Yönetimi
```
products.php (Liste)
├── product_add.php → Form → DB INSERT → products.php
├── product_edit.php → Form → DB UPDATE → products.php
└── product_delete.php → DB DELETE → products.php
```

### Raporlar
```
reports.php
├── Satış özeti ve istatistikler
├── Aylık satış raporları
├── En iyi müşteriler
├── Görev istatistikleri
└── Kullanıcı performans raporları
```

## Güvenlik Özellikleri

1. **Oturum Yönetimi**
   - Session kontrolü (requireLogin)
   - Otomatik yönlendirme

2. **Veri Güvenliği**
   - SQL Injection koruması (prepared statements)
   - XSS koruması (htmlspecialchars)
   - CSRF koruması (session kontrolü)
   - Input sanitizasyonu (sanitize fonksiyonu)

3. **Şifre Güvenliği**
   - password_hash() ile şifreleme
   - password_verify() ile doğrulama

4. **Aktivite Logları**
   - Tüm önemli işlemler loglanır
   - Kullanıcı, işlem tipi, varlık bilgisi

## Yardımcı Fonksiyonlar (config.php)

- `sanitize()` - Input temizleme
- `redirect()` - Sayfa yönlendirme
- `isLoggedIn()` - Oturum kontrolü
- `requireLogin()` - Zorunlu giriş kontrolü
- `getUser()` - Mevcut kullanıcı bilgisi
- `logActivity()` - Aktivite kaydetme
- `formatCurrency()` - Para formatı
- `formatDate()` - Tarih formatı
- `formatDateTime()` - Tarih-saat formatı
- `getStatusBadge()` - Durum rozeti HTML

## UI Bileşenleri

### Bootstrap 5.3 Kullanımı
- Grid sistemi (responsive)
- Form bileşenleri
- Modal diyaloglar
- Alert mesajları
- Badge ve progress bar
- Card yapıları
- Dropdown menüler

### Bootstrap Icons
- Menü ikonları
- Durum göstergeleri
- Aksiyon butonları

### Özel CSS
- Stat kartları
- Login sayfası
- Navigasyon stili
- Tablo stilleri
- Form stilleri
- Responsive tasarım

## Responsive Tasarım

Sistem tüm cihazlarda çalışacak şekilde tasarlanmıştır:
- Desktop (>= 1200px)
- Tablet (768px - 1199px)
- Mobile (< 768px)

## Tarayıcı Desteği

- Chrome (son 2 versiyon)
- Firefox (son 2 versiyon)
- Safari (son 2 versiyon)
- Edge (son 2 versiyon)

## Genişletme Önerileri

1. **Kullanıcı Yönetimi**
   - Kullanıcı ekleme/düzenleme sayfaları
   - Rol bazlı yetkilendirme

2. **Email Entegrasyonu**
   - Müşterilere otomatik email gönderme
   - Görev hatırlatıcıları

3. **Dosya Yönetimi**
   - Müşteri belgeleri yükleme
   - Sözleşme yönetimi

4. **Gelişmiş Raporlar**
   - Grafik ve chartlar
   - Excel export
   - PDF raporlar

5. **API Entegrasyonu**
   - RESTful API
   - Mobil uygulama desteği

6. **Bildirimler**
   - Push bildirimleri
   - Email bildirimleri
   - SMS entegrasyonu

